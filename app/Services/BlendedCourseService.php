<?php

namespace App\Services;

use App\Models\CourseModule;
use App\Models\ModuleQuizAttempt;
use App\Models\Service;
use App\Models\Student;
use App\Models\StudentModuleProgress;
use Illuminate\Support\Collection;

class BlendedCourseService
{
    public const PASSING_SCORE = 90;

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

    public function isEligibleForInPersonTesting(Student $student, Service $service): bool
    {
        $modules = $this->getModulesForService($service);
        if ($modules->isEmpty()) {
            return false;
        }

        $progress = $this->getProgress($student, $service);

        foreach ($modules as $module) {
            $record = $progress->get($module->id);
            if (! $record?->is_completed || ($record->best_score ?? 0) < self::PASSING_SCORE) {
                if (! $record?->admin_override) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  array<string, string>  $answers  question_id => selected answer
     * @return array{score: int, passed: bool, attempt: ModuleQuizAttempt}
     */
    public function submitQuiz(Student $student, CourseModule $module, array $answers): array
    {
        $questions = $module->quizQuestions;
        $total = $questions->count();
        $correct = 0;

        foreach ($questions as $question) {
            $given = $answers[(string) $question->id] ?? $answers[$question->id] ?? null;
            if ($given !== null && $given === $question->correct_answer) {
                $correct++;
            }
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= self::PASSING_SCORE;

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

        return ['score' => $score, 'passed' => $passed, 'attempt' => $attempt];
    }
}
