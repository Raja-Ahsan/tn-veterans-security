<?php

namespace App\Services;

use App\Models\CourseModule;
use App\Models\ModuleQuizAttempt;
use App\Models\ModuleQuizSession;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\StudentModuleProgress;
use Illuminate\Support\Collection;

class BlendedCourseService
{
    public const PASSING_SCORE = 90;

    public const DEFAULT_QUIZ_MINUTES = 15;

    public function getModulesForService(Service $service): Collection
    {
        return $service->courseModules()->where('is_active', true)->orderBy('order')->get();
    }

    public function getProgress(Student $student, Service $service): Collection
    {
        return StudentModuleProgress::query()
            ->where('student_id', $student->id)
            ->where('service_id', $service->id)
            ->get()
            ->keyBy('course_module_id');
    }

    public function canAccessModule(Student $student, CourseModule $module, Collection $progress, Collection $modules): bool
    {
        $existing = $progress->get($module->id);
        if ($existing?->admin_override) {
            return true;
        }

        $moduleIndex = $modules->search(fn ($m) => $m->id === $module->id);
        if ($moduleIndex === false || $moduleIndex === 0) {
            return true;
        }

        $previousModule = $modules[$moduleIndex - 1];
        $previousProgress = $progress->get($previousModule->id);

        return $previousProgress?->is_completed === true;
    }

    /**
     * Attempts remaining / allowed for this module.
     */
    public function canAttemptQuiz(Student $student, CourseModule $module): bool
    {
        $progress = StudentModuleProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->first();

        if (! $progress) {
            return true;
        }

        if ($progress->is_completed || $progress->admin_override) {
            return false;
        }

        return (int) ($progress->attempts ?? 0) < $module->maxAttempts();
    }

    public function hasExhaustedQuizAttempt(Student $student, CourseModule $module): bool
    {
        $progress = StudentModuleProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->first();

        return $progress !== null
            && ! $progress->is_completed
            && ! $progress->admin_override
            && (int) ($progress->attempts ?? 0) >= $module->maxAttempts();
    }

    public function isEligibleForInPersonTesting(Student $student, Service $service): bool
    {
        $modules = $this->getModulesForService($service);
        if ($modules->isEmpty()) {
            return false;
        }

        $progress = $this->getProgress($student, $service);

        foreach ($modules as $module) {
            $record = $progress->get($module->id);
            $required = $module->passingScore();
            if (! $record?->is_completed || ($record->best_score ?? 0) < $required) {
                if (! $record?->admin_override) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array{completed: int, total: int, percent: int}
     */
    public function progressSummary(Student $student, Service $service): array
    {
        $modules = $this->getModulesForService($service);
        $progress = $this->getProgress($student, $service);
        $total = $modules->count();
        $completed = $modules->filter(function (CourseModule $module) use ($progress) {
            $record = $progress->get($module->id);

            return (bool) ($record?->is_completed || $record?->admin_override);
        })->count();

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    public function firstContinueModule(Student $student, Service $service): ?CourseModule
    {
        $modules = $this->getModulesForService($service);
        $progress = $this->getProgress($student, $service);

        foreach ($modules as $module) {
            if (! $this->canAccessModule($student, $module, $progress, $modules)) {
                continue;
            }

            $record = $progress->get($module->id);
            if (! $record?->is_completed && ! $record?->admin_override) {
                return $module;
            }
        }

        return $modules->last();
    }

    public function studentHasPaidAccess(Student $student, Service $service): bool
    {
        return $this->paidBookingForService($student, $service) !== null;
    }

    public function quizTimeLimitMinutes(CourseModule $module): int
    {
        $minutes = (int) ($module->quiz_time_limit_minutes ?? 0);

        return $minutes > 0 ? $minutes : self::DEFAULT_QUIZ_MINUTES;
    }

    public function startQuizSession(Student $student, Service $service, CourseModule $module): ModuleQuizSession
    {
        ModuleQuizSession::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->where('status', ModuleQuizSession::STATUS_IN_PROGRESS)
            ->update([
                'status' => ModuleQuizSession::STATUS_EXPIRED,
                'submitted_at' => now(),
            ]);

        $minutes = $this->quizTimeLimitMinutes($module);

        return ModuleQuizSession::create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'course_module_id' => $module->id,
            'current_index' => 0,
            'answers' => [],
            'started_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
            'status' => ModuleQuizSession::STATUS_IN_PROGRESS,
        ]);
    }

    public function getOpenSession(Student $student, CourseModule $module): ?ModuleQuizSession
    {
        $session = ModuleQuizSession::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->where('status', ModuleQuizSession::STATUS_IN_PROGRESS)
            ->latest('id')
            ->first();

        if (! $session || $session->isExpired()) {
            return null;
        }

        return $session;
    }

    /**
     * Auto-submit an in-progress session when the countdown has expired.
     */
    public function finalizeExpiredOpenSession(Student $student, CourseModule $module): ?ModuleQuizSession
    {
        $session = ModuleQuizSession::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->where('status', ModuleQuizSession::STATUS_IN_PROGRESS)
            ->latest('id')
            ->first();

        if (! $session || ! $session->isExpired()) {
            return null;
        }

        return $this->finalizeSession($student, $module, $session, true);
    }

    /**
     * @param  string|array<int, string>|null  $answer
     */
    public function saveAnswerAndAdvance(
        Student $student,
        CourseModule $module,
        ModuleQuizSession $session,
        int $questionId,
        string|array|null $answer
    ): ModuleQuizSession {
        if ($session->isExpired()) {
            return $this->finalizeSession($student, $module, $session, true);
        }

        $questions = $module->quizQuestions()->orderBy('order')->get();
        $current = $questions->get($session->current_index);

        if (! $current || $current->id !== $questionId) {
            throw new \RuntimeException('Invalid quiz question for this step.');
        }

        $answers = $session->answers ?? [];
        $answers[(string) $questionId] = $answer;
        $session->answers = $answers;

        $isLast = $session->current_index >= $questions->count() - 1;
        if ($isLast) {
            return $this->finalizeSession($student, $module, $session, false);
        }

        $session->current_index = $session->current_index + 1;
        $session->save();

        return $session;
    }

    public function finalizeSession(
        Student $student,
        CourseModule $module,
        ModuleQuizSession $session,
        bool $timedOut = false
    ): ModuleQuizSession {
        if (in_array($session->status, [ModuleQuizSession::STATUS_SUBMITTED, ModuleQuizSession::STATUS_EXPIRED], true)
            && $session->module_quiz_attempt_id) {
            return $session->fresh(['attempt']);
        }

        $result = $this->submitQuiz($student, $module, $session->answers ?? []);

        $session->update([
            'status' => $timedOut ? ModuleQuizSession::STATUS_EXPIRED : ModuleQuizSession::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'module_quiz_attempt_id' => $result['attempt']->id,
            'answers' => $session->answers ?? [],
        ]);

        return $session->fresh(['attempt']);
    }

    /**
     * @param  array<string, string|array<int, string>>  $answers
     * @return array{score: int, passed: bool, attempt: ModuleQuizAttempt, review: array}
     */
    public function submitQuiz(Student $student, CourseModule $module, array $answers): array
    {
        $questions = $module->quizQuestions()->orderBy('order')->get();
        $total = $questions->count();
        $correct = 0;

        foreach ($questions as $question) {
            $given = $answers[(string) $question->id] ?? $answers[$question->id] ?? null;
            if ($question->isAnswerCorrect($given)) {
                $correct++;
            }
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= $module->passingScore();

        $attempt = ModuleQuizAttempt::create([
            'student_id' => $student->id,
            'course_module_id' => $module->id,
            'score' => $score,
            'passed' => $passed,
            'answers' => $answers,
        ]);

        $progress = StudentModuleProgress::firstOrCreate(
            [
                'student_id' => $student->id,
                'course_module_id' => $module->id,
            ],
            ['service_id' => $module->service_id]
        );

        $progress->increment('attempts');
        $progress->best_score = max($progress->best_score ?? 0, $score);

        if ($passed) {
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();

        return [
            'score' => $score,
            'passed' => $passed,
            'attempt' => $attempt,
            'review' => $this->buildQuizReview($module, $answers),
        ];
    }

    public function getLatestAttempt(Student $student, CourseModule $module): ?ModuleQuizAttempt
    {
        return ModuleQuizAttempt::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @param  array<string, string|array<int, string>>  $answers
     * @return list<array{question_id: int, question: string, options: array, allow_multiple: bool, selected: array, correct_answer: array, is_correct: bool}>
     */
    public function buildQuizReview(CourseModule $module, array $answers, bool $revealCorrectAnswers = true): array
    {
        $module->loadMissing('quizQuestions');
        $review = [];

        foreach ($module->quizQuestions as $question) {
            $given = $answers[(string) $question->id] ?? $answers[$question->id] ?? null;
            $selected = is_array($given)
                ? array_values(array_map('strval', array_filter($given, fn ($a) => filled($a))))
                : (filled($given) ? [(string) $given] : []);

            $review[] = [
                'question_id' => $question->id,
                'question' => $question->question,
                'options' => $question->options ?? [],
                'allow_multiple' => (bool) $question->allow_multiple,
                'selected' => $selected,
                'correct_answer' => $revealCorrectAnswers ? $question->correctAnswers() : [],
                'is_correct' => $question->isAnswerCorrect($given),
                'reveal_correct' => $revealCorrectAnswers,
            ];
        }

        return $review;
    }

    public function paidBookingForService(Student $student, Service $service): ?ServiceBooking
    {
        return ServiceBooking::query()
            ->where('student_id', $student->id)
            ->where('service_id', $service->id)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->latest('id')
            ->first();
    }
}
