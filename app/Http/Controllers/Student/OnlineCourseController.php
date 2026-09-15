<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\CourseModuleVideo;
use App\Models\ModuleQuizSession;
use App\Models\Service;
use App\Services\AdminNotifier;
use App\Services\BlendedCourseCompletionService;
use App\Services\BlendedCourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineCourseController extends Controller
{
    public function __construct(
        private BlendedCourseService $blendedCourse,
        private BlendedCourseCompletionService $completionService
    ) {}

    public function index(Service $service)
    {
        if (! $service->has_online_parts) {
            abort(404);
        }

        $student = Auth::guard('student')->user();
        $this->assertPaidAccess($service, $student);

        $modules = $this->blendedCourse->getModulesForService($service);
        $progress = $this->blendedCourse->getProgress($student, $service);
        $progressSummary = $this->blendedCourse->progressSummary($student, $service);
        $continueModule = $this->blendedCourse->firstContinueModule($student, $service);
        $eligible = $this->blendedCourse->isEligibleForInPersonTesting($student, $service);
        $certificate = null;

        return view('student.online-course.index', compact(
            'service',
            'modules',
            'progress',
            'progressSummary',
            'continueModule',
            'eligible',
            'certificate'
        ));
    }

    public function show(Service $service, CourseModule $courseModule)
    {
        if ($courseModule->service_id !== $service->id || ! $service->has_online_parts) {
            abort(404);
        }

        $student = Auth::guard('student')->user();
        $this->assertPaidAccess($service, $student);
        $modules = $this->blendedCourse->getModulesForService($service);
        $progress = $this->blendedCourse->getProgress($student, $service);

        if (! $this->blendedCourse->canAccessModule($student, $courseModule, $progress, $modules)) {
            return redirect()->route('student.online-course.index', $service)
                ->with('error', 'Complete the previous module before continuing.');
        }

        $courseModule->load(['videos.quizQuestions', 'quizQuestions']);
        $videos = $this->blendedCourse->getVideosForModule($courseModule);
        $selectedVideo = $this->resolveSelectedVideo($student, $courseModule, $videos);
        if ($selectedVideo && ! $this->blendedCourse->canAccessVideo($student, $courseModule, $selectedVideo)) {
            return redirect()->route('student.online-course.module', [$service, $courseModule])
                ->with('error', 'Pass the previous video quiz before continuing.');
        }

        $moduleProgress = $progress->get($courseModule->id);
        $videoProgressMap = $this->blendedCourse->getVideoProgress($student, $courseModule);
        $latestAttempt = $this->blendedCourse->getLatestAttempt($student, $courseModule, $selectedVideo);
        $videoProgress = $selectedVideo ? $videoProgressMap->get($selectedVideo->id) : null;
        $passed = $selectedVideo
            ? $this->blendedCourse->isVideoComplete($student, $selectedVideo)
            : (bool) ($moduleProgress?->is_completed);
        $quizReview = ($latestAttempt && $passed)
            ? $this->blendedCourse->buildQuizReview($courseModule, $latestAttempt->answers ?? [], true, $selectedVideo)
            : [];
        $canAttemptQuiz = $this->blendedCourse->canAttemptQuiz($student, $courseModule, $selectedVideo);
        $needsReenrollment = $this->blendedCourse->hasExhaustedQuizAttempt($student, $courseModule, $selectedVideo);
        $hasWatchedVideo = $selectedVideo
            ? $this->blendedCourse->hasWatchedVideo($student, $selectedVideo)
            : true;
        $expired = $this->blendedCourse->finalizeExpiredOpenSession($student, $courseModule, $selectedVideo);
        if ($expired) {
            $this->afterQuizCompleted($student, $service, $courseModule);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $expired])
                ->with('warning', 'Time is up. Your answers were submitted automatically.');
        }

        $openSession = $this->blendedCourse->getOpenSession($student, $courseModule, $selectedVideo);
        if ($openSession) {
            return redirect()->route('student.online-course.quiz.take', [$service, $courseModule])
                ->with('warning', 'Finish this quiz first — your timer is still running.');
        }

        $quizQuestions = $this->blendedCourse->questionsForDisplay($courseModule, $selectedVideo);
        $quizMinutes = $this->blendedCourse->quizTimeLimitMinutes($courseModule);
        $passingScore = $courseModule->passingScore();
        $maxAttempts = $courseModule->maxAttempts();
        $attemptsUsed = (int) ($videoProgress?->attempts ?? $moduleProgress?->attempts ?? 0);
        $materials = $courseModule->materialFiles();
        $supportEmail = \App\Models\SiteSetting::query()->value('email');
        $supportPhone = \App\Models\SiteSetting::query()->value('phone');
        $modulePassed = (bool) ($moduleProgress?->is_completed);

        return view('student.online-course.module', compact(
            'service',
            'courseModule',
            'moduleProgress',
            'modules',
            'videos',
            'selectedVideo',
            'videoProgressMap',
            'latestAttempt',
            'quizReview',
            'openSession',
            'quizMinutes',
            'canAttemptQuiz',
            'needsReenrollment',
            'hasWatchedVideo',
            'passingScore',
            'maxAttempts',
            'attemptsUsed',
            'materials',
            'supportEmail',
            'supportPhone',
            'quizQuestions',
            'modulePassed'
        ));
    }

    public function markWatched(Request $request, Service $service, CourseModule $courseModule, CourseModuleVideo $courseModuleVideo)
    {
        $this->assertModuleAccess($service, $courseModule);
        abort_unless($courseModuleVideo->course_module_id === $courseModule->id, 404);

        $student = Auth::guard('student')->user();
        if (! $this->blendedCourse->canAccessVideo($student, $courseModule, $courseModuleVideo)) {
            abort(403, 'Pass the previous video quiz first.');
        }

        $validated = $request->validate([
            'completed' => ['sometimes', 'boolean'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'position_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $completed = (bool) ($validated['completed'] ?? false);
        $duration = isset($validated['duration_seconds']) ? (int) $validated['duration_seconds'] : null;
        $position = isset($validated['position_seconds'])
            ? (int) $validated['position_seconds']
            : ($completed ? (int) ($duration ?? 0) : 0);

        // Legacy clients that only posted duration after "ended" still count as complete.
        if (! array_key_exists('completed', $validated) && $request->filled('duration_seconds')) {
            $completed = true;
            $position = max($position, (int) $request->integer('duration_seconds'));
        }

        $progress = $this->blendedCourse->markVideoWatched(
            $student,
            $courseModule,
            $courseModuleVideo,
            $duration,
            $position,
            $completed
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'watched' => (bool) $progress->video_watched,
                'last_position_seconds' => (int) $progress->last_position_seconds,
            ]);
        }

        if ($progress->video_watched) {
            return redirect()->route('student.online-course.module', [$service, $courseModule, 'video' => $courseModuleVideo->id])
                ->with('success', 'Video complete. You can start the quiz.');
        }

        return redirect()->route('student.online-course.module', [$service, $courseModule, 'video' => $courseModuleVideo->id])
            ->with('error', 'Finish the video to the end before starting the quiz.');
    }

    public function startQuiz(Request $request, Service $service, CourseModule $courseModule)
    {
        $this->assertModuleAccess($service, $courseModule);
        $student = Auth::guard('student')->user();
        $courseModule->load(['videos.quizQuestions', 'quizQuestions']);
        $video = $this->resolveQuizVideoFromRequest($request, $student, $courseModule);

        $questions = $this->blendedCourse->questionsForDisplay($courseModule, $video);
        if ($questions->isEmpty()) {
            return redirect()->route('student.online-course.module', [$service, $courseModule])
                ->with('error', 'This module has no quiz questions yet.');
        }

        if ($video && ! $this->blendedCourse->canAccessVideo($student, $courseModule, $video)) {
            return redirect()->route('student.online-course.module', [$service, $courseModule])
                ->with('error', 'Pass the previous video quiz before continuing.');
        }

        if ($video && $video->requiresWatchCompletion() && ! $this->blendedCourse->hasWatchedVideo($student, $video)) {
            return redirect()->route('student.online-course.module', [$service, $courseModule, 'video' => $video->id])
                ->with('error', 'Watch the full video before starting the quiz. You can pause, but you cannot skip ahead.');
        }

        $expired = $this->blendedCourse->finalizeExpiredOpenSession($student, $courseModule, $video);
        if ($expired) {
            $this->afterQuizCompleted($student, $service, $courseModule);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $expired])
                ->with('warning', 'Time is up. Your answers were submitted automatically.');
        }

        $open = $this->blendedCourse->getOpenSession($student, $courseModule, $video);
        if ($open) {
            return redirect()->route('student.online-course.quiz.take', [$service, $courseModule]);
        }

        if (! $this->blendedCourse->canAttemptQuiz($student, $courseModule, $video)) {
            $message = ($video && $video->requiresWatchCompletion() && ! $this->blendedCourse->hasWatchedVideo($student, $video))
                ? 'Watch the full video before starting the quiz. You can pause, but you cannot skip ahead.'
                : 'This quiz is already completed.';

            $params = [$service, $courseModule];
            if ($video) {
                $params['video'] = $video->id;
            }

            return redirect()->route('student.online-course.module', $params)
                ->with('error', $message);
        }

        $this->blendedCourse->startQuizSession($student, $service, $courseModule, $video);

        return redirect()->route('student.online-course.quiz.take', [$service, $courseModule]);
    }

    public function takeQuiz(Service $service, CourseModule $courseModule)
    {
        $this->assertModuleAccess($service, $courseModule);
        $student = Auth::guard('student')->user();
        $courseModule->load(['videos.quizQuestions']);

        $expired = $this->blendedCourse->finalizeExpiredOpenSession($student, $courseModule);
        if ($expired) {
            $this->afterQuizCompleted($student, $service, $courseModule);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $expired])
                ->with('warning', 'Time is up. Your answers were submitted automatically.');
        }

        $session = $this->blendedCourse->getOpenSession($student, $courseModule);
        if (! $session) {
            return redirect()->route('student.online-course.module', [$service, $courseModule])
                ->with('error', 'Start the quiz to begin the timed attempt.');
        }

        $questions = $this->blendedCourse->questionsForQuiz($courseModule, $session->courseModuleVideo);
        $total = $questions->count();
        $index = min($session->current_index, max($total - 1, 0));
        $question = $questions->get($index);

        if (! $question) {
            $session = $this->blendedCourse->finalizeSession($student, $courseModule, $session, false);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $session]);
        }

        return view('student.online-course.quiz', [
            'service' => $service,
            'courseModule' => $courseModule,
            'session' => $session,
            'question' => $question,
            'questionNumber' => $index + 1,
            'totalQuestions' => $total,
            'isLast' => $index >= $total - 1,
            'remainingSeconds' => $session->remainingSeconds(),
        ]);
    }

    public function answerQuiz(Request $request, Service $service, CourseModule $courseModule)
    {
        $this->assertModuleAccess($service, $courseModule);
        $student = Auth::guard('student')->user();
        $courseModule->load(['quizQuestions' => fn ($q) => $q->orderBy('order')]);

        $session = ModuleQuizSession::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $courseModule->id)
            ->where('status', ModuleQuizSession::STATUS_IN_PROGRESS)
            ->latest('id')
            ->firstOrFail();

        $questions = $this->blendedCourse->questionsForQuiz($courseModule, $session->courseModuleVideo);

        if ($session->isExpired() || $request->boolean('auto_submit')) {
            $question = $questions->get($session->current_index);
            if ($question) {
                $answer = $question->allow_multiple
                    ? $request->input('answers', [])
                    : $request->input('answer');

                $hasAnswer = $question->allow_multiple
                    ? filled($answer)
                    : filled($answer);

                if ($hasAnswer) {
                    $answers = $session->answers ?? [];
                    $answers[(string) $question->id] = $answer;
                    $session->answers = $answers;
                    $session->save();
                }
            }

            $session = $this->blendedCourse->finalizeSession($student, $courseModule, $session, true);
            $this->afterQuizCompleted($student, $service, $courseModule);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $session])
                ->with('warning', 'Time is up. Your answers were submitted automatically.');
        }

        $question = $questions->get($session->current_index);
        if (! $question) {
            $session = $this->blendedCourse->finalizeSession($student, $courseModule, $session, false);
            $this->afterQuizCompleted($student, $service, $courseModule);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $session]);
        }

        $answer = $question->allow_multiple
            ? $request->input('answers', [])
            : $request->input('answer');

        if ($question->allow_multiple) {
            $request->validate([
                'answers' => 'required|array|min:1',
                'answers.*' => 'string',
            ], [
                'answers.required' => 'Select at least one answer before continuing.',
            ]);
        } else {
            $request->validate([
                'answer' => 'required|string',
            ], [
                'answer.required' => 'Select an answer before continuing.',
            ]);
        }

        try {
            $session = $this->blendedCourse->saveAnswerAndAdvance(
                $student,
                $courseModule,
                $session,
                $question->id,
                $answer
            );
        } catch (\RuntimeException) {
            return redirect()->route('student.online-course.quiz.take', [$service, $courseModule])
                ->with('error', 'That question was already answered. Continue from your current question.');
        }

        if (in_array($session->status, [ModuleQuizSession::STATUS_SUBMITTED, ModuleQuizSession::STATUS_EXPIRED], true)) {
            $this->afterQuizCompleted($student, $service, $courseModule);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $session]);
        }

        return redirect()->route('student.online-course.quiz.take', [$service, $courseModule]);
    }

    public function quizResult(Service $service, CourseModule $courseModule, ModuleQuizSession $moduleQuizSession)
    {
        $this->assertModuleAccess($service, $courseModule);
        $student = Auth::guard('student')->user();

        if (
            $moduleQuizSession->student_id !== $student->id
            || $moduleQuizSession->course_module_id !== $courseModule->id
        ) {
            abort(404);
        }

        if ($moduleQuizSession->status === ModuleQuizSession::STATUS_IN_PROGRESS) {
            if ($moduleQuizSession->isExpired()) {
                $moduleQuizSession = $this->blendedCourse->finalizeSession($student, $courseModule, $moduleQuizSession, true);
                $this->afterQuizCompleted($student, $service, $courseModule);
            } else {
                return redirect()->route('student.online-course.quiz.take', [$service, $courseModule]);
            }
        }

        $moduleQuizSession->load('attempt');
        $answers = $moduleQuizSession->answers ?? ($moduleQuizSession->attempt->answers ?? []);
        $score = $moduleQuizSession->attempt?->score ?? 0;
        $passed = (bool) ($moduleQuizSession->attempt?->passed);
        $quizVideo = $moduleQuizSession->courseModuleVideo;
        $quizReview = $passed
            ? $this->blendedCourse->buildQuizReview($courseModule, $answers, true, $quizVideo)
            : [];
        $progress = $this->blendedCourse->getProgress($student, $service);
        $modules = $this->blendedCourse->getModulesForService($service);
        $eligible = $this->blendedCourse->isEligibleForInPersonTesting($student, $service);
        $certificate = null;
        $supportEmail = \App\Models\SiteSetting::query()->value('email');
        $supportPhone = \App\Models\SiteSetting::query()->value('phone');
        $passingScore = $courseModule->passingScore();
        $nextVideo = null;
        if ($passed && $quizVideo) {
            $nextVideo = $this->blendedCourse->firstContinueVideo($student, $courseModule);
            if ($nextVideo && $nextVideo->id === $quizVideo->id) {
                $nextVideo = null;
            }
        }
        $modulePassed = (bool) ($progress->get($courseModule->id)?->is_completed);

        return view('student.online-course.quiz-result', compact(
            'service',
            'courseModule',
            'moduleQuizSession',
            'quizReview',
            'score',
            'passed',
            'progress',
            'modules',
            'eligible',
            'certificate',
            'supportEmail',
            'supportPhone',
            'passingScore',
            'quizVideo',
            'nextVideo',
            'modulePassed'
        ));
    }

    /**
     * Legacy endpoint — redirects into timed quiz flow.
     */
    public function submitQuiz(Request $request, Service $service, CourseModule $courseModule)
    {
        return redirect()->route('student.online-course.quiz.start', [$service, $courseModule]);
    }

    private function assertPaidAccess(Service $service, $student): void
    {
        if (! $this->blendedCourse->studentHasPaidAccess($student, $service)) {
            abort(403, 'Pay the course deposit to unlock online modules.');
        }
    }

    private function assertModuleAccess(Service $service, CourseModule $courseModule): void
    {
        if ($courseModule->service_id !== $service->id || ! $service->has_online_parts) {
            abort(404);
        }

        $student = Auth::guard('student')->user();
        $this->assertPaidAccess($service, $student);
        $modules = $this->blendedCourse->getModulesForService($service);
        $progress = $this->blendedCourse->getProgress($student, $service);

        if (! $this->blendedCourse->canAccessModule($student, $courseModule, $progress, $modules)) {
            abort(403, 'Complete the previous module first.');
        }
    }

    private function afterQuizCompleted($student, Service $service, CourseModule $courseModule): void
    {
        $attempt = $this->blendedCourse->getLatestAttempt($student, $courseModule);
        $score = (int) ($attempt?->score ?? 0);
        $passed = (bool) ($attempt?->passed);
        $status = $passed ? 'passed' : 'completed';

        AdminNotifier::broadcast(
            "Quiz {$status}: {$courseModule->title}",
            "{$student->name} scored {$score}% on {$courseModule->title} ({$service->title}).",
            'book',
            route('admin.students.show', $student),
            'quiz'
        );

        $this->completionService->notifyIfCourseCompleted($student, $service, $this->blendedCourse, $courseModule);
    }

    /**
     * @param  Collection<int, CourseModuleVideo>  $videos
     */
    private function resolveSelectedVideo($student, CourseModule $courseModule, $videos): ?CourseModuleVideo
    {
        if ($videos->isEmpty()) {
            return null;
        }

        $requestedId = (int) request('video');
        if ($requestedId) {
            $requested = $videos->firstWhere('id', $requestedId);
            if ($requested && $this->blendedCourse->canAccessVideo($student, $courseModule, $requested)) {
                return $requested;
            }
        }

        return $this->blendedCourse->firstContinueVideo($student, $courseModule);
    }

    private function resolveQuizVideoFromRequest(Request $request, $student, CourseModule $courseModule): ?CourseModuleVideo
    {
        $videoId = $request->integer('video_id') ?: $request->integer('video');
        $video = $this->blendedCourse->resolveQuizVideo($courseModule, $videoId ?: null);

        if ($video) {
            return $video;
        }

        return $this->blendedCourse->firstContinueVideo($student, $courseModule);
    }
}
