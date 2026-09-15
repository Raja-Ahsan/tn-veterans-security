<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\CourseModuleVideo;
use App\Models\ModuleQuizQuestion;
use App\Models\Service;
use App\Support\QuizQuestionPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CourseModuleController extends Controller
{
    public function index(Service $service)
    {
        $this->abortUnlessBlended($service);

        $modules = $service->courseModules()->withCount(['quizQuestions', 'videos'])->orderBy('order')->get();

        return view('admin.course-modules.index', compact('service', 'modules'));
    }

    public function create(Service $service)
    {
        $this->abortUnlessBlended($service);

        return view('admin.course-modules.create', compact('service'));
    }

    public function store(Request $request, Service $service)
    {
        $this->abortUnlessBlended($service);
        $this->prepareModuleRequest($request);

        $validated = $this->validateModuleRequest($request);

        $nextOrder = (int) ($service->courseModules()->max('order') ?? 0) + 1;
        $requestedOrder = (int) ($validated['order'] ?? 0);

        $module = $service->courseModules()->create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'order' => $requestedOrder > 0 ? $requestedOrder : $nextOrder,
            'is_active' => $request->boolean('is_active'),
            'quiz_time_limit_minutes' => $validated['quiz_time_limit_minutes'] ?? 15,
            'passing_score' => $validated['passing_score'] ?? 90,
            'max_attempts' => $validated['max_attempts'] ?? 1,
            'materials' => $this->storeMaterials($request, []),
        ]);

        $this->syncPrimaryVideoAndQuestions($request, $module, $validated['questions'] ?? []);

        return redirect()->route('admin.classes.course-modules.edit', [$service, $module])
            ->with('success', 'Module created. Add more videos if this module has more than one lesson.');
    }

    public function edit(Service $service, CourseModule $courseModule)
    {
        $this->abortUnlessBlended($service);

        $courseModule->load(['videos.quizQuestions', 'quizQuestions']);

        return view('admin.course-modules.edit', compact('service', 'courseModule'));
    }

    public function update(Request $request, Service $service, CourseModule $courseModule)
    {
        $this->abortUnlessBlended($service);
        $this->prepareModuleRequest($request);

        $validated = $this->validateModuleRequest($request);

        $materials = $courseModule->materials ?? [];
        if ($request->boolean('remove_materials')) {
            $this->deleteMaterialFiles($materials);
            $materials = [];
        }
        $materials = $this->storeMaterials($request, $materials);

        $courseModule->update([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'order' => $validated['order'] ?? $courseModule->order,
            'is_active' => $request->boolean('is_active'),
            'quiz_time_limit_minutes' => $validated['quiz_time_limit_minutes'] ?? 15,
            'passing_score' => $validated['passing_score'] ?? 90,
            'max_attempts' => $validated['max_attempts'] ?? 1,
            'materials' => $materials,
        ]);

        $this->syncPrimaryVideoAndQuestions($request, $courseModule, $validated['questions'] ?? []);

        return redirect()->route('admin.classes.course-modules.edit', [$service, $courseModule])
            ->with('success', 'Module updated.');
    }

    public function destroy(Service $service, CourseModule $courseModule)
    {
        $this->abortUnlessBlended($service);
        $this->deleteMaterialFiles($courseModule->materials ?? []);
        $courseModule->delete();

        return redirect()->route('admin.classes.course-modules.index', $service)
            ->with('success', 'Module deleted.');
    }

    public function reorder(Request $request, Service $service)
    {
        $this->abortUnlessBlended($service);
        if ($request->has('positions')) {
            $validated = $request->validate([
                'positions' => 'required|array',
                'positions.*' => 'integer|min:1',
            ]);

            foreach ($validated['positions'] as $moduleId => $position) {
                CourseModule::query()
                    ->where('service_id', $service->id)
                    ->where('id', $moduleId)
                    ->update(['order' => $position]);
            }

            return back()->with('success', 'Module order updated.');
        }

        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:course_modules,id',
        ]);

        foreach ($validated['order'] as $index => $moduleId) {
            CourseModule::query()
                ->where('service_id', $service->id)
                ->where('id', $moduleId)
                ->update(['order' => $index + 1]);
        }

        return back()->with('success', 'Module order updated.');
    }

    /**
     * Normalize request before validation (drop blank quiz rows, empty URL).
     */
    private function prepareModuleRequest(Request $request): void
    {
        $request->merge([
            'questions' => QuizQuestionPayload::normalize($request->input('questions', [])),
            'video_url' => $request->filled('video_url') ? $request->input('video_url') : null,
            'quiz_time_limit_minutes' => $request->filled('quiz_time_limit_minutes')
                ? (int) $request->input('quiz_time_limit_minutes')
                : 15,
        ]);
    }

    private function abortUnlessBlended(Service $service): void
    {
        abort_unless($service->has_online_parts, 404);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateModuleRequest(Request $request): array
    {
        $validator = Validator::make(
            $request->all(),
            $this->moduleRules(),
            $this->moduleMessages(),
            $this->moduleAttributes(),
        );

        $validator->after(function ($validator): void {
            $this->correctAnswerMustMatchOptions($validator);
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private function correctAnswerMustMatchOptions(\Illuminate\Validation\Validator $validator): void
    {
        $questions = $validator->getData()['questions'] ?? [];

        foreach ($questions as $index => $question) {
            $options = $question['options'] ?? [];
            $correct = $question['correct_answer'] ?? [];
            $allowMultiple = (bool) ($question['allow_multiple'] ?? false);

            if ($correct === []) {
                $validator->errors()->add(
                    "questions.$index.correct_answer",
                    $allowMultiple
                        ? 'Select at least one correct answer for this question.'
                        : 'Select the correct answer for this question.'
                );

                continue;
            }

            foreach ($correct as $answer) {
                if (! in_array($answer, $options, true)) {
                    $validator->errors()->add(
                        "questions.$index.correct_answer",
                        'Each correct answer must match one of the options.'
                    );
                    break;
                }
            }

            if (! $allowMultiple && count($correct) !== 1) {
                $validator->errors()->add(
                    "questions.$index.correct_answer",
                    'Single-select questions need exactly one correct answer.'
                );
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function moduleRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'video_title' => 'nullable|string|max:255',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/ogg|max:102400',
            'remove_video_file' => 'sometimes|boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'quiz_time_limit_minutes' => 'required|integer|min:1|max:180',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:1|max:20',
            'materials_files' => 'nullable|array|max:5',
            'materials_files.*' => 'file|mimes:pdf,doc,docx,ppt,pptx,png,jpg,jpeg|max:10240',
            'remove_materials' => 'sometimes|boolean',
            'questions' => 'nullable|array',
            'questions.*.question' => 'required|string|max:1000',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*' => 'required|string|max:500',
            'questions.*.allow_multiple' => 'sometimes|boolean',
            'questions.*.correct_answer' => 'required|array|min:1',
            'questions.*.correct_answer.*' => 'required|string|max:500',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function moduleMessages(): array
    {
        return [
            'title.required' => 'Module title is required.',
            'video_url.url' => 'Video URL must be a valid link (or leave it empty).',
            'video_file.mimetypes' => 'Upload an MP4, WebM, MOV, or OGG video.',
            'video_file.max' => 'Video must be 100MB or smaller.',
            'quiz_time_limit_minutes.required' => 'Set a quiz time limit in minutes.',
            'quiz_time_limit_minutes.min' => 'Quiz time must be at least 1 minute.',
            'questions.*.question.required' => 'Each quiz question needs question text.',
            'questions.*.options.required' => 'Each quiz question needs answer options.',
            'questions.*.options.min' => 'Each quiz question needs at least 2 options.',
            'questions.*.options.*.required' => 'Option text cannot be empty.',
            'questions.*.correct_answer.required' => 'Select which option(s) are correct.',
            'questions.*.correct_answer.min' => 'Select at least one correct answer.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function moduleAttributes(): array
    {
        return [
            'title' => 'module title',
            'video_url' => 'video URL',
            'quiz_time_limit_minutes' => 'quiz time limit',
            'passing_score' => 'passing score',
            'max_attempts' => 'max attempts',
            'questions.*.question' => 'question text',
            'questions.*.options' => 'options',
            'questions.*.correct_answer' => 'correct answer',
        ];
    }

    /**
     * @param  list<array{path?: string, original_name?: string}>  $existing
     * @return list<array{path: string, original_name: string}>
     */
    private function storeMaterials(Request $request, array $existing): array
    {
        if (! $request->hasFile('materials_files')) {
            return array_values($existing);
        }

        foreach ($request->file('materials_files') as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('course-materials', 'public');
            $existing[] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return array_values($existing);
    }

    /**
     * @param  list<array{path?: string}>  $materials
     */
    private function deleteMaterialFiles(array $materials): void
    {
        foreach ($materials as $material) {
            $path = $material['path'] ?? null;
            if (filled($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Sync the module's single primary video + quiz.
     * When a module already has multiple videos, quizzes are edited per-video only —
     * updating the module must not rewrite whichever video is currently first by order.
     *
     * @param  array<int, array<string, mixed>>  $questions
     */
    private function syncPrimaryVideoAndQuestions(Request $request, CourseModule $module, array $questions): void
    {
        $videoCount = $module->videos()->count();
        if ($videoCount > 1) {
            return;
        }

        $hasVideoInput = $request->hasFile('video_file')
            || $request->filled('video_url')
            || $request->boolean('remove_video_file')
            || $request->filled('video_title');
        $hasQuestions = $questions !== [];
        $hasExistingVideo = $videoCount > 0;

        if (! $hasVideoInput && ! $hasQuestions && ! $hasExistingVideo) {
            $module->quizQuestions()->whereNull('course_module_video_id')->delete();

            return;
        }

        $video = $this->upsertPrimaryVideo($request, $module);
        $this->syncQuestions($module, $questions, $video);
    }

    private function upsertPrimaryVideo(Request $request, CourseModule $module): CourseModuleVideo
    {
        $editingVideoId = $request->integer('editing_video_id');
        $video = $editingVideoId > 0
            ? $module->videos()->whereKey($editingVideoId)->first()
            : null;
        $video ??= $module->videos()->orderBy('order')->orderBy('id')->first();

        if (! $video) {
            $video = $module->videos()->create([
                'title' => $request->input('video_title') ?: 'Video 1',
                'order' => 1,
            ]);
        }

        $payload = [
            'title' => $request->input('video_title') ?: ($video->title ?: 'Video 1'),
            'video_url' => $request->filled('video_url') ? $request->input('video_url') : null,
        ];

        if ($request->boolean('remove_video_file')) {
            $video->deleteStoredFile();
            $payload['video_path'] = null;
            $payload['original_name'] = null;
        }

        if ($request->hasFile('video_file')) {
            $video->deleteStoredFile();
            $file = $request->file('video_file');
            $payload['video_path'] = $file->store('course-videos', 'public');
            $payload['original_name'] = $file->getClientOriginalName();
        }

        $video->update($payload);

        return $video->fresh();
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    private function syncQuestions(CourseModule $module, array $questions, ?CourseModuleVideo $video = null): void
    {
        $query = $module->quizQuestions();
        if ($video) {
            $query->where(function ($builder) use ($video): void {
                $builder->where('course_module_video_id', $video->id)
                    ->orWhereNull('course_module_video_id');
            });
        } else {
            $query->whereNull('course_module_video_id');
        }
        $query->delete();

        foreach ($questions as $index => $q) {
            if (empty($q['question'])) {
                continue;
            }

            $options = array_values(array_filter(
                $q['options'] ?? [],
                fn ($option) => filled($option)
            ));

            $correct = array_values(array_filter(
                $q['correct_answer'] ?? [],
                fn ($answer) => filled($answer)
            ));

            ModuleQuizQuestion::create([
                'course_module_id' => $module->id,
                'course_module_video_id' => $video?->id,
                'question' => $q['question'],
                'options' => $options,
                'allow_multiple' => (bool) ($q['allow_multiple'] ?? false),
                'correct_answer' => $correct,
                'order' => $index,
            ]);
        }
    }
}
