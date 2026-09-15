<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\CourseModuleVideo;
use App\Models\ModuleQuizQuestion;
use App\Models\Service;
use App\Support\QuizQuestionPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CourseModuleVideoController extends Controller
{
    public function create(Service $service, CourseModule $courseModule)
    {
        abort_unless($courseModule->service_id === $service->id, 404);
        abort_unless($service->has_online_parts, 404);

        return view('admin.course-module-videos.create', compact('service', 'courseModule'));
    }

    public function store(Request $request, Service $service, CourseModule $courseModule)
    {
        abort_unless($courseModule->service_id === $service->id, 404);
        abort_unless($service->has_online_parts, 404);

        $this->prepareVideoRequest($request);
        $validated = $this->validateVideoRequest($request);

        $nextOrder = (int) ($courseModule->videos()->max('order') ?? 0) + 1;

        $video = $courseModule->videos()->create([
            'title' => $validated['title'] ?? ('Video '.$nextOrder),
            'video_url' => $validated['video_url'] ?? null,
            'order' => $nextOrder,
        ]);

        $this->storeVideoFile($request, $video);
        $this->syncQuestions($courseModule, $video, $validated['questions'] ?? []);

        return redirect()->route('admin.classes.course-modules.edit', [$service, $courseModule])
            ->with('success', 'Video added. Students must watch it before taking its quiz.');
    }

    public function edit(Service $service, CourseModule $courseModule, CourseModuleVideo $courseModuleVideo)
    {
        abort_unless($courseModule->service_id === $service->id, 404);
        abort_unless($courseModuleVideo->course_module_id === $courseModule->id, 404);
        abort_unless($service->has_online_parts, 404);

        $courseModuleVideo->load('quizQuestions');

        return view('admin.course-module-videos.edit', compact('service', 'courseModule', 'courseModuleVideo'));
    }

    public function update(Request $request, Service $service, CourseModule $courseModule, CourseModuleVideo $courseModuleVideo)
    {
        abort_unless($courseModule->service_id === $service->id, 404);
        abort_unless($courseModuleVideo->course_module_id === $courseModule->id, 404);
        abort_unless($service->has_online_parts, 404);

        $this->prepareVideoRequest($request);
        $validated = $this->validateVideoRequest($request);

        $courseModuleVideo->update([
            'title' => $validated['title'] ?? $courseModuleVideo->title,
            'video_url' => $validated['video_url'] ?? null,
            'order' => $validated['order'] ?? $courseModuleVideo->order,
        ]);

        if ($request->boolean('remove_video_file')) {
            $courseModuleVideo->deleteStoredFile();
            $courseModuleVideo->update([
                'video_path' => null,
                'original_name' => null,
            ]);
        }

        $this->storeVideoFile($request, $courseModuleVideo);
        $this->syncQuestions($courseModule, $courseModuleVideo, $validated['questions'] ?? []);

        return redirect()->route('admin.classes.course-modules.edit', [$service, $courseModule])
            ->with('success', 'Video updated.');
    }

    public function destroy(Service $service, CourseModule $courseModule, CourseModuleVideo $courseModuleVideo)
    {
        abort_unless($courseModule->service_id === $service->id, 404);
        abort_unless($courseModuleVideo->course_module_id === $courseModule->id, 404);
        abort_unless($service->has_online_parts, 404);

        $courseModuleVideo->delete();

        return redirect()->route('admin.classes.course-modules.edit', [$service, $courseModule])
            ->with('success', 'Video deleted.');
    }

    public function reorder(Request $request, Service $service, CourseModule $courseModule)
    {
        abort_unless($courseModule->service_id === $service->id, 404);
        abort_unless($service->has_online_parts, 404);

        if ($request->has('order')) {
            $validated = $request->validate([
                'order' => 'required|array|min:1',
                'order.*' => 'integer|exists:course_module_videos,id',
            ]);

            foreach ($validated['order'] as $index => $videoId) {
                CourseModuleVideo::query()
                    ->where('course_module_id', $courseModule->id)
                    ->where('id', $videoId)
                    ->update(['order' => $index + 1]);
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Video order updated.']);
            }

            return back()->with('success', 'Video order updated.');
        }

        if ($request->filled('move_video_id') && $request->filled('direction')) {
            $validated = $request->validate([
                'move_video_id' => 'required|integer|exists:course_module_videos,id',
                'direction' => 'required|in:up,down',
            ]);

            $videos = $courseModule->videos()->orderBy('order')->orderBy('id')->get()->values();
            $index = $videos->search(
                fn (CourseModuleVideo $video): bool => (int) $video->id === (int) $validated['move_video_id']
            );

            abort_if($index === false, 404);

            $swapWith = $validated['direction'] === 'up' ? $index - 1 : $index + 1;
            if ($swapWith < 0 || $swapWith >= $videos->count()) {
                return back();
            }

            $orderedIds = $videos->pluck('id')->all();
            [$orderedIds[$index], $orderedIds[$swapWith]] = [$orderedIds[$swapWith], $orderedIds[$index]];

            foreach ($orderedIds as $position => $videoId) {
                CourseModuleVideo::query()
                    ->where('course_module_id', $courseModule->id)
                    ->where('id', $videoId)
                    ->update(['order' => $position + 1]);
            }

            return back()->with('success', 'Video order updated.');
        }

        $validated = $request->validate([
            'positions' => 'required|array',
            'positions.*' => 'integer|min:1',
        ]);

        foreach ($validated['positions'] as $videoId => $position) {
            CourseModuleVideo::query()
                ->where('course_module_id', $courseModule->id)
                ->where('id', $videoId)
                ->update(['order' => $position]);
        }

        return back()->with('success', 'Video order updated.');
    }

    private function prepareVideoRequest(Request $request): void
    {
        $request->merge([
            'questions' => QuizQuestionPayload::normalize($request->input('questions', [])),
            'video_url' => $request->filled('video_url') ? $request->input('video_url') : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateVideoRequest(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url|max:500',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/ogg|max:102400',
            'remove_video_file' => 'sometimes|boolean',
            'order' => 'nullable|integer|min:1',
            'questions' => 'nullable|array',
            'questions.*.question' => 'required|string|max:1000',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*' => 'required|string|max:500',
            'questions.*.allow_multiple' => 'sometimes|boolean',
            'questions.*.correct_answer' => 'required|array|min:1',
            'questions.*.correct_answer.*' => 'required|string|max:500',
        ], [
            'title.required' => 'Video title is required.',
            'video_file.mimetypes' => 'Upload an MP4, WebM, MOV, or OGG video.',
            'video_file.max' => 'Video must be 100MB or smaller.',
            'questions.*.question.required' => 'Each quiz question needs question text.',
            'questions.*.options.min' => 'Each quiz question needs at least 2 options.',
        ]);

        $validator->after(function ($validator): void {
            $questions = $validator->getData()['questions'] ?? [];
            foreach ($questions as $index => $question) {
                $options = $question['options'] ?? [];
                $correct = $question['correct_answer'] ?? [];
                $allowMultiple = (bool) ($question['allow_multiple'] ?? false);

                if ($correct === []) {
                    $validator->errors()->add(
                        "questions.$index.correct_answer",
                        'Select the correct answer for this question.'
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
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private function storeVideoFile(Request $request, CourseModuleVideo $video): void
    {
        if (! $request->hasFile('video_file')) {
            return;
        }

        $video->deleteStoredFile();
        $file = $request->file('video_file');
        $video->update([
            'video_path' => $file->store('course-videos', 'public'),
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    private function syncQuestions(CourseModule $module, CourseModuleVideo $video, array $questions): void
    {
        $video->quizQuestions()->delete();

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
                'course_module_video_id' => $video->id,
                'question' => $q['question'],
                'options' => $options,
                'allow_multiple' => (bool) ($q['allow_multiple'] ?? false),
                'correct_answer' => $correct,
                'order' => $index,
            ]);
        }
    }
}
