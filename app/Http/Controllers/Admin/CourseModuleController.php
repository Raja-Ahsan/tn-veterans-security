<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\ModuleQuizQuestion;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CourseModuleController extends Controller
{
    public function index(Service $service)
    {
        $modules = $service->courseModules()->withCount('quizQuestions')->orderBy('order')->get();

        return view('admin.course-modules.index', compact('service', 'modules'));
    }

    public function create(Service $service)
    {
        return view('admin.course-modules.create', compact('service'));
    }

    public function store(Request $request, Service $service)
    {
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

        $this->syncQuestions($module, $validated['questions'] ?? []);

        return redirect()->route('admin.classes.course-modules.index', $service)
            ->with('success', 'Module created.');
    }

    public function edit(Service $service, CourseModule $courseModule)
    {
        $courseModule->load('quizQuestions');

        return view('admin.course-modules.edit', compact('service', 'courseModule'));
    }

    public function update(Request $request, Service $service, CourseModule $courseModule)
    {
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

        $courseModule->quizQuestions()->delete();
        $this->syncQuestions($courseModule, $validated['questions'] ?? []);

        return redirect()->route('admin.classes.course-modules.index', $service)
            ->with('success', 'Module updated.');
    }

    public function destroy(Service $service, CourseModule $courseModule)
    {
        $this->deleteMaterialFiles($courseModule->materials ?? []);
        $courseModule->delete();

        return redirect()->route('admin.classes.course-modules.index', $service)
            ->with('success', 'Module deleted.');
    }

    public function reorder(Request $request, Service $service)
    {
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
        $questions = collect($request->input('questions', []))
            ->map(function (array $question): array {
                $options = collect($question['options'] ?? [])
                    ->map(fn ($option) => is_string($option) ? trim($option) : $option)
                    ->filter(fn ($option) => filled($option))
                    ->values()
                    ->all();

                $allowMultiple = filter_var($question['allow_multiple'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $correctRaw = $question['correct_answer'] ?? [];
                if (! is_array($correctRaw)) {
                    $correctRaw = filled($correctRaw) ? [$correctRaw] : [];
                }

                $correct = collect($correctRaw)
                    ->map(fn ($answer) => is_string($answer) ? trim($answer) : $answer)
                    ->filter(fn ($answer) => filled($answer))
                    ->unique()
                    ->values()
                    ->all();

                if (! $allowMultiple && count($correct) > 1) {
                    $correct = [reset($correct)];
                }

                return [
                    'question' => trim((string) ($question['question'] ?? '')),
                    'options' => $options,
                    'allow_multiple' => $allowMultiple,
                    'correct_answer' => $correct,
                ];
            })
            ->filter(fn (array $question) => $question['question'] !== '')
            ->values()
            ->all();

        $request->merge([
            'questions' => $questions,
            'video_url' => $request->filled('video_url') ? $request->input('video_url') : null,
            'quiz_time_limit_minutes' => $request->filled('quiz_time_limit_minutes')
                ? (int) $request->input('quiz_time_limit_minutes')
                : 15,
        ]);
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
     * @param  array<int, array<string, mixed>>  $questions
     */
    private function syncQuestions(CourseModule $module, array $questions): void
    {
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
                'question' => $q['question'],
                'options' => $options,
                'allow_multiple' => (bool) ($q['allow_multiple'] ?? false),
                'correct_answer' => $correct,
                'order' => $index,
            ]);
        }
    }
}
