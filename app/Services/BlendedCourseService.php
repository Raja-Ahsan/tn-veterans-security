<?php

namespace App\Services;

use App\Models\CourseModule;
use App\Models\CourseModuleVideo;
use App\Models\ModuleQuizAttempt;
use App\Models\ModuleQuizQuestion;
use App\Models\ModuleQuizSession;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\StudentModuleProgress;
use App\Models\StudentVideoProgress;
use Illuminate\Support\Collection;

class BlendedCourseService
{
    public const PASSING_SCORE = 90;

    public const DEFAULT_QUIZ_MINUTES = 15;

    public function getModulesForService(Service $service): Collection
    {
        return $service->courseModules()->where('is_active', true)->orderBy('order')->get();
    }

    public function getVideosForModule(CourseModule $module): Collection
    {
        return $module->videos()->orderBy('order')->orderBy('id')->get();
    }

    public function getProgress(Student $student, Service $service): Collection
    {
        return StudentModuleProgress::query()
            ->where('student_id', $student->id)
            ->where('service_id', $service->id)
            ->get()
            ->keyBy('course_module_id');
    }

    public function getVideoProgress(Student $student, CourseModule $module): Collection
    {
        return StudentVideoProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->get()
            ->keyBy('course_module_video_id');
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

    public function canAccessVideo(Student $student, CourseModule $module, CourseModuleVideo $video): bool
    {
        $progress = $this->getProgress($student, $module->service);
        $modules = $this->getModulesForService($module->service);

        if (! $this->canAccessModule($student, $module, $progress, $modules)) {
            return false;
        }

        if ($progress->get($module->id)?->admin_override) {
            return true;
        }

        $videos = $this->getVideosForModule($module);
        $index = $videos->search(fn (CourseModuleVideo $item) => $item->id === $video->id);
        if ($index === false) {
            return false;
        }

        if ($index === 0) {
            return true;
        }

        $previous = $videos[$index - 1];

        return $this->isVideoComplete($student, $previous);
    }

    public function isVideoComplete(Student $student, CourseModuleVideo $video): bool
    {
        $record = StudentVideoProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_video_id', $video->id)
            ->first();

        if ($record?->is_completed) {
            return true;
        }

        if ($video->quizQuestions()->exists()) {
            return false;
        }

        if ($video->requiresWatchCompletion()) {
            return (bool) $record?->video_watched;
        }

        return false;
    }

    public function hasWatchedVideo(Student $student, CourseModuleVideo $video): bool
    {
        if (! $video->requiresWatchCompletion()) {
            return true;
        }

        return (bool) StudentVideoProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_video_id', $video->id)
            ->value('video_watched');
    }

    public function markVideoWatched(Student $student, CourseModule $module, CourseModuleVideo $video, ?int $durationSeconds = null, ?int $positionSeconds = null, bool $completed = true): StudentVideoProgress
    {
        $progress = StudentVideoProgress::firstOrCreate(
            [
                'student_id' => $student->id,
                'course_module_video_id' => $video->id,
            ],
            [
                'service_id' => $module->service_id,
                'course_module_id' => $module->id,
            ]
        );

        $position = max(0, (int) ($positionSeconds ?? 0));
        if ($position > (int) ($progress->last_position_seconds ?? 0)) {
            $progress->last_position_seconds = $position;
        }

        if ($durationSeconds && $durationSeconds > 0) {
            $progress->duration_seconds = $durationSeconds;
        }

        if ($completed) {
            $duration = (int) ($durationSeconds ?: $progress->duration_seconds ?: $position);
            // Require reaching near the end (95% or within 2 seconds).
            if ($duration > 0) {
                $required = max(0, min($duration - 2, (int) floor($duration * 0.95)));
                if ($position < $required && $position + 1 < $duration) {
                    $progress->save();

                    return $progress;
                }
            }

            $progress->video_watched = true;
            $progress->watched_at = $progress->watched_at ?? now();
            if ($duration > 0) {
                $progress->duration_seconds = $duration;
                $progress->last_position_seconds = max((int) $progress->last_position_seconds, $duration);
            }
        }

        $progress->save();

        if ($completed && $progress->video_watched && ! $video->quizQuestions()->exists()) {
            $this->markVideoCompletedWithoutQuiz($progress);
            $this->syncModuleCompletionFromVideos($student, $module);
        }

        return $progress;
    }

    /**
     * Attempts remaining / allowed for this module or video quiz.
     */
    public function canAttemptQuiz(Student $student, CourseModule $module, ?CourseModuleVideo $video = null): bool
    {
        if ($video) {
            if ($video->requiresWatchCompletion() && ! $this->hasWatchedVideo($student, $video)) {
                return false;
            }

            $progress = StudentVideoProgress::query()
                ->where('student_id', $student->id)
                ->where('course_module_video_id', $video->id)
                ->first();

            if (! $progress) {
                return true;
            }

            if ($progress->is_completed) {
                return false;
            }

            return (int) ($progress->attempts ?? 0) < $module->maxAttempts();
        }

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

    public function hasExhaustedQuizAttempt(Student $student, CourseModule $module, ?CourseModuleVideo $video = null): bool
    {
        if ($video) {
            $progress = StudentVideoProgress::query()
                ->where('student_id', $student->id)
                ->where('course_module_video_id', $video->id)
                ->first();

            return $progress !== null
                && ! $progress->is_completed
                && (int) ($progress->attempts ?? 0) >= $module->maxAttempts();
        }

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

    public function firstContinueVideo(Student $student, CourseModule $module): ?CourseModuleVideo
    {
        foreach ($this->getVideosForModule($module) as $video) {
            if (! $this->canAccessVideo($student, $module, $video)) {
                continue;
            }

            if (! $this->isVideoComplete($student, $video)) {
                return $video;
            }
        }

        return $this->getVideosForModule($module)->last();
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

    /**
     * @return Collection<int, ModuleQuizQuestion>
     */
    public function questionsForQuiz(CourseModule $module, ?CourseModuleVideo $video = null): Collection
    {
        if ($video) {
            $questions = $video->quizQuestions()->orderBy('order')->get();
            if ($questions->isNotEmpty()) {
                return $questions;
            }
        }

        return $module->quizQuestions()
            ->whereNull('course_module_video_id')
            ->orderBy('order')
            ->get();
    }

    public function startQuizSession(Student $student, Service $service, CourseModule $module, ?CourseModuleVideo $video = null): ModuleQuizSession
    {
        $query = ModuleQuizSession::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->where('status', ModuleQuizSession::STATUS_IN_PROGRESS);

        if ($video) {
            $query->where('course_module_video_id', $video->id);
        } else {
            $query->whereNull('course_module_video_id');
        }

        $query->update([
            'status' => ModuleQuizSession::STATUS_EXPIRED,
            'submitted_at' => now(),
        ]);

        $minutes = $this->quizTimeLimitMinutes($module);

        return ModuleQuizSession::create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'course_module_id' => $module->id,
            'course_module_video_id' => $video?->id,
            'current_index' => 0,
            'answers' => [],
            'started_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
            'status' => ModuleQuizSession::STATUS_IN_PROGRESS,
        ]);
    }

    public function getOpenSession(Student $student, CourseModule $module, ?CourseModuleVideo $video = null): ?ModuleQuizSession
    {
        $query = ModuleQuizSession::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->where('status', ModuleQuizSession::STATUS_IN_PROGRESS);

        if ($video) {
            $query->where('course_module_video_id', $video->id);
        }

        $session = $query->latest('id')->first();

        if (! $session || $session->isExpired()) {
            return null;
        }

        return $session;
    }

    /**
     * Auto-submit an in-progress session when the countdown has expired.
     */
    public function finalizeExpiredOpenSession(Student $student, CourseModule $module, ?CourseModuleVideo $video = null): ?ModuleQuizSession
    {
        $query = ModuleQuizSession::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id)
            ->where('status', ModuleQuizSession::STATUS_IN_PROGRESS);

        if ($video) {
            $query->where('course_module_video_id', $video->id);
        }

        $session = $query->latest('id')->first();

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

        $video = $session->courseModuleVideo;
        $questions = $this->questionsForQuiz($module, $video);
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

        $video = $session->courseModuleVideo;
        $result = $this->submitQuiz($student, $module, $session->answers ?? [], $video);

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
    public function submitQuiz(Student $student, CourseModule $module, array $answers, ?CourseModuleVideo $video = null): array
    {
        $questions = $this->questionsForQuiz($module, $video);
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
            'course_module_video_id' => $video?->id,
            'score' => $score,
            'passed' => $passed,
            'answers' => $answers,
        ]);

        if ($video) {
            $this->recordVideoQuizProgress($student, $module, $video, $score, $passed);
            $this->syncModuleCompletionFromVideos($student, $module);
        } else {
            $this->recordModuleQuizProgress($student, $module, $score, $passed);
        }

        return [
            'score' => $score,
            'passed' => $passed,
            'attempt' => $attempt,
            'review' => $this->buildQuizReview($module, $answers, true, $video),
        ];
    }

    public function getLatestAttempt(Student $student, CourseModule $module, ?CourseModuleVideo $video = null): ?ModuleQuizAttempt
    {
        $query = ModuleQuizAttempt::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module->id);

        if ($video) {
            $query->where('course_module_video_id', $video->id);
        }

        return $query->orderByDesc('id')->first();
    }

    /**
     * @param  array<string, string|array<int, string>>  $answers
     * @return list<array{question_id: int, question: string, options: array, allow_multiple: bool, selected: array, correct_answer: array, is_correct: bool}>
     */
    public function buildQuizReview(CourseModule $module, array $answers, bool $revealCorrectAnswers = true, ?CourseModuleVideo $video = null): array
    {
        $questions = $this->questionsForQuiz($module, $video);
        $review = [];

        foreach ($questions as $question) {
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

    public function resolveQuizVideo(CourseModule $module, ?int $videoId = null): ?CourseModuleVideo
    {
        $videos = $this->getVideosForModule($module);
        if ($videos->isEmpty()) {
            return null;
        }

        if ($videoId) {
            return $videos->firstWhere('id', $videoId);
        }

        return $videos->first();
    }

    public function questionsForDisplay(CourseModule $module, ?CourseModuleVideo $video = null): Collection
    {
        if ($video) {
            $questions = $video->quizQuestions()->orderBy('order')->get();
            if ($questions->isNotEmpty()) {
                return $questions;
            }
        }

        $legacy = $module->quizQuestions()->whereNull('course_module_video_id')->orderBy('order')->get();
        if ($legacy->isNotEmpty()) {
            return $legacy;
        }

        if ($video === null) {
            $firstVideo = $this->getVideosForModule($module)->first();
            if ($firstVideo) {
                return $firstVideo->quizQuestions()->orderBy('order')->get();
            }
        }

        return collect();
    }

    private function recordVideoQuizProgress(
        Student $student,
        CourseModule $module,
        CourseModuleVideo $video,
        int $score,
        bool $passed
    ): void {
        $progress = StudentVideoProgress::firstOrCreate(
            [
                'student_id' => $student->id,
                'course_module_video_id' => $video->id,
            ],
            [
                'service_id' => $module->service_id,
                'course_module_id' => $module->id,
            ]
        );

        $progress->increment('attempts');
        $progress->best_score = max($progress->best_score ?? 0, $score);

        if ($passed) {
            $progress->is_completed = true;
            $progress->completed_at = now();
            $progress->video_watched = true;
            $progress->watched_at = $progress->watched_at ?? now();
        }

        $progress->save();
    }

    private function recordModuleQuizProgress(Student $student, CourseModule $module, int $score, bool $passed): void
    {
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
    }

    private function markVideoCompletedWithoutQuiz(StudentVideoProgress $progress): void
    {
        $progress->is_completed = true;
        $progress->completed_at = now();
        $progress->best_score = $progress->best_score ?? 100;
        $progress->save();
    }

    public function syncModuleCompletionFromVideos(Student $student, CourseModule $module): void
    {
        $videos = $this->getVideosForModule($module);
        if ($videos->isEmpty()) {
            return;
        }

        $allComplete = true;
        $scores = [];

        foreach ($videos as $video) {
            if (! $this->isVideoComplete($student, $video)) {
                $allComplete = false;
                break;
            }

            $record = StudentVideoProgress::query()
                ->where('student_id', $student->id)
                ->where('course_module_video_id', $video->id)
                ->first();

            if ($record?->best_score !== null) {
                $scores[] = (int) $record->best_score;
            }
        }

        $progress = StudentModuleProgress::firstOrCreate(
            [
                'student_id' => $student->id,
                'course_module_id' => $module->id,
            ],
            ['service_id' => $module->service_id]
        );

        if ($allComplete) {
            $progress->is_completed = true;
            $progress->completed_at = $progress->completed_at ?? now();
            $progress->best_score = $scores === [] ? 100 : (int) round(array_sum($scores) / count($scores));
            $progress->save();

            return;
        }

        if (! $progress->admin_override) {
            $progress->is_completed = false;
            $progress->completed_at = null;
            $progress->save();
        }
    }
}
