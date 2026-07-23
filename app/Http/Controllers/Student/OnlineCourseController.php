<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\ModuleQuizSession;
use App\Models\Service;
use App\Services\BlendedCourseCompletionService;
use App\Services\BlendedCourseService;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineCourseController extends Controller
{
    public function __construct(
        private BlendedCourseService $blendedCourse,
        private BlendedCourseCompletionService $completionService,
        private CertificateService $certificateService
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
        $certificate = $eligible
            ? $this->certificateService->issueForOnlineCourseCompletion($student, $service)
            : null;

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

        $courseModule->load('quizQuestions');
        $moduleProgress = $progress->get($courseModule->id);
        $latestAttempt = $this->blendedCourse->getLatestAttempt($student, $courseModule);
        $passed = (bool) ($moduleProgress?->is_completed);
        $quizReview = ($latestAttempt && $passed)
            ? $this->blendedCourse->buildQuizReview($courseModule, $latestAttempt->answers ?? [], true)
            : [];
        $canAttemptQuiz = $this->blendedCourse->canAttemptQuiz($student, $courseModule);
        $needsReenrollment = $this->blendedCourse->hasExhaustedQuizAttempt($student, $courseModule);
        $expired = $this->blendedCourse->finalizeExpiredOpenSession($student, $courseModule);
        if ($expired) {
            $this->afterQuizCompleted($student, $service, $courseModule);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $expired])
                ->with('warning', 'Time is up. Your answers were submitted automatically.');
        }

        $openSession = $this->blendedCourse->getOpenSession($student, $courseModule);
        if ($openSession) {
            return redirect()->route('student.online-course.quiz.take', [$service, $courseModule])
                ->with('warning', 'Finish this quiz first — your timer is still running.');
        }

        $quizMinutes = $this->blendedCourse->quizTimeLimitMinutes($courseModule);
        $passingScore = $courseModule->passingScore();
        $maxAttempts = $courseModule->maxAttempts();
        $attemptsUsed = (int) ($moduleProgress?->attempts ?? 0);
        $materials = $courseModule->materialFiles();
        $supportEmail = \App\Models\SiteSetting::query()->value('email');
        $supportPhone = \App\Models\SiteSetting::query()->value('phone');

        return view('student.online-course.module', compact(
            'service',
            'courseModule',
            'moduleProgress',
            'modules',
            'latestAttempt',
            'quizReview',
            'openSession',
            'quizMinutes',
            'canAttemptQuiz',
            'needsReenrollment',
            'passingScore',
            'maxAttempts',
            'attemptsUsed',
            'materials',
            'supportEmail',
            'supportPhone'
        ));
    }

    public function startQuiz(Service $service, CourseModule $courseModule)
    {
        $this->assertModuleAccess($service, $courseModule);
        $student = Auth::guard('student')->user();

        $courseModule->load('quizQuestions');
        if ($courseModule->quizQuestions->isEmpty()) {
            return redirect()->route('student.online-course.module', [$service, $courseModule])
                ->with('error', 'This module has no quiz questions yet.');
        }

        $expired = $this->blendedCourse->finalizeExpiredOpenSession($student, $courseModule);
        if ($expired) {
            $this->afterQuizCompleted($student, $service, $courseModule);

            return redirect()->route('student.online-course.quiz.result', [$service, $courseModule, $expired])
                ->with('warning', 'Time is up. Your answers were submitted automatically.');
        }

        $open = $this->blendedCourse->getOpenSession($student, $courseModule);
        if ($open) {
            return redirect()->route('student.online-course.quiz.take', [$service, $courseModule]);
        }

        if (! $this->blendedCourse->canAttemptQuiz($student, $courseModule)) {
            return redirect()->route('student.online-course.module', [$service, $courseModule])
                ->with('error', 'This quiz attempt is used. Contact admin to re-enroll for a new attempt with updated questions.');
        }

        $this->blendedCourse->startQuizSession($student, $service, $courseModule);

        return redirect()->route('student.online-course.quiz.take', [$service, $courseModule]);
    }

    public function takeQuiz(Service $service, CourseModule $courseModule)
    {
        $this->assertModuleAccess($service, $courseModule);
        $student = Auth::guard('student')->user();
        $courseModule->load(['quizQuestions' => fn ($q) => $q->orderBy('order')]);

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

        $questions = $courseModule->quizQuestions;
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

        if ($session->isExpired() || $request->boolean('auto_submit')) {
            $question = $courseModule->quizQuestions->get($session->current_index);
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

        $question = $courseModule->quizQuestions->get($session->current_index);
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
        $quizReview = $passed
            ? $this->blendedCourse->buildQuizReview($courseModule, $answers, true)
            : [];
        $progress = $this->blendedCourse->getProgress($student, $service);
        $modules = $this->blendedCourse->getModulesForService($service);
        $eligible = $this->blendedCourse->isEligibleForInPersonTesting($student, $service);
        $certificate = $eligible
            ? $this->certificateService->issueForOnlineCourseCompletion($student, $service)
            : null;
        $supportEmail = \App\Models\SiteSetting::query()->value('email');
        $supportPhone = \App\Models\SiteSetting::query()->value('phone');
        $passingScore = $courseModule->passingScore();

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
            'passingScore'
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
        $this->completionService->notifyIfCourseCompleted($student, $service, $this->blendedCourse, $courseModule);
        $this->certificateService->issueForOnlineCourseCompletion($student, $service);
    }
}
