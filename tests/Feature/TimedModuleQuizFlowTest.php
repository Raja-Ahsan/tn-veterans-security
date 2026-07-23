<?php

namespace Tests\Feature;

use App\Models\CourseCertificate;
use App\Models\CourseModule;
use App\Models\ModuleQuizQuestion;
use App\Models\ModuleQuizSession;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\StudentModuleProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TimedModuleQuizFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_timed_quiz_advances_one_way_and_pass_unlocks_next_module(): void
    {
        Mail::fake();

        [$student, $service, $module1, $module2] = $this->seedBlendedCourse();

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module1]))
            ->assertRedirect(route('student.online-course.quiz.take', [$service, $module1]));

        $session = ModuleQuizSession::query()->first();
        $this->assertNotNull($session);
        $this->assertSame(ModuleQuizSession::STATUS_IN_PROGRESS, $session->status);
        $this->assertTrue($session->expires_at->greaterThan(now()->addMinutes(9)));

        $q1 = $module1->quizQuestions()->orderBy('order')->first();
        $q2 = $module1->quizQuestions()->orderBy('order')->skip(1)->first();

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                'answer' => $q1->correctAnswers()[0],
            ])
            ->assertRedirect(route('student.online-course.quiz.take', [$service, $module1]));

        $session->refresh();
        $this->assertSame(1, $session->current_index);

        $result = $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                'answer' => $q2->correctAnswers()[0],
            ]);

        $session->refresh();
        $result->assertRedirect(route('student.online-course.quiz.result', [$service, $module1, $session]));
        $this->assertSame(ModuleQuizSession::STATUS_SUBMITTED, $session->status);

        $progress = StudentModuleProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module1->id)
            ->first();

        $this->assertTrue((bool) $progress?->is_completed);
        $this->assertGreaterThanOrEqual(90, (int) $progress->best_score);

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module2]))
            ->assertOk();
    }

    public function test_failed_quiz_blocks_retake_without_admin_reset(): void
    {
        Mail::fake();

        [$student, $service, $module1, $module2] = $this->seedBlendedCourse();

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module1]));

        $questions = $module1->quizQuestions()->orderBy('order')->get();

        foreach ($questions as $index => $question) {
            $wrong = collect($question->options)
                ->first(fn ($option) => ! in_array($option, $question->correctAnswers(), true));

            $response = $this->actingAs($student, 'student')
                ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                    'answer' => $wrong,
                ]);

            if ($index === $questions->count() - 1) {
                $session = ModuleQuizSession::query()->latest('id')->first();
                $response->assertRedirect(route('student.online-course.quiz.result', [$service, $module1, $session]));
                $this->actingAs($student, 'student')
                    ->get(route('student.online-course.quiz.result', [$service, $module1, $session]))
                    ->assertOk()
                    ->assertDontSee('Answer review')
                    ->assertSee('Free retake is not available');
            }
        }

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module1]))
            ->assertRedirect(route('student.online-course.module', [$service, $module1]));

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module1]))
            ->assertOk()
            ->assertSee('Quiz attempt used')
            ->assertDontSee('Start timed quiz');

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module2]))
            ->assertRedirect(route('student.online-course.index', $service));
    }

    public function test_timeout_auto_submits_partial_answers(): void
    {
        Mail::fake();

        [$student, $service, $module1] = $this->seedBlendedCourse();

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module1]));

        $session = ModuleQuizSession::query()->first();
        $session->update(['expires_at' => now()->subSecond()]);

        $q1 = $module1->quizQuestions()->orderBy('order')->first();

        $response = $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                'auto_submit' => 1,
                'answer' => $q1->correctAnswers()[0],
            ]);

        $session->refresh();
        $response->assertRedirect(route('student.online-course.quiz.result', [$service, $module1, $session]));
        $this->assertSame(ModuleQuizSession::STATUS_EXPIRED, $session->status);
        $this->assertArrayHasKey((string) $q1->id, $session->answers ?? []);
    }

    public function test_passing_all_modules_does_not_issue_certificate(): void
    {
        Mail::fake();

        [$student, $service, $module1, $module2] = $this->seedBlendedCourse();

        $this->passModule($student, $service, $module1);
        $this->passModule($student, $service, $module2);

        $certificate = CourseCertificate::query()
            ->where('student_id', $student->id)
            ->where('service_id', $service->id)
            ->first();

        $this->assertNull($certificate);
        $this->assertTrue(app(\App\Services\BlendedCourseService::class)->isEligibleForInPersonTesting($student, $service));
    }

    public function test_multi_select_question_requires_exact_match(): void
    {
        Mail::fake();

        [$student, $service, $module1] = $this->seedBlendedCourse(withMultiSelect: true);

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module1]));

        $questions = $module1->quizQuestions()->orderBy('order')->get();
        $multi = $questions->firstWhere('allow_multiple', true);
        $single = $questions->firstWhere('allow_multiple', false);

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                'answers' => $multi->correctAnswers(),
            ])
            ->assertRedirect(route('student.online-course.quiz.take', [$service, $module1]));

        $response = $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                'answer' => $single->correctAnswers()[0],
            ]);

        $session = ModuleQuizSession::query()->latest('id')->first();
        $response->assertRedirect(route('student.online-course.quiz.result', [$service, $module1, $session]));
        $this->assertTrue((bool) $session->attempt?->passed);
    }

    public function test_unpaid_deposit_blocks_online_course_access(): void
    {
        Mail::fake();

        [$student, $service, $module1] = $this->seedBlendedCourse();

        ServiceBooking::query()
            ->where('student_id', $student->id)
            ->where('service_id', $service->id)
            ->update(['payment_status' => 'pending']);

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.index', $service))
            ->assertForbidden();

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module1]))
            ->assertForbidden();
    }

    public function test_module_passing_score_and_max_attempts_are_honored(): void
    {
        Mail::fake();

        [$student, $service, $module1] = $this->seedBlendedCourse();

        $module1->update([
            'passing_score' => 50,
            'max_attempts' => 2,
        ]);

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module1]));

        $questions = $module1->quizQuestions()->orderBy('order')->get();

        // Score 50%: correct then wrong
        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                'answer' => $questions[0]->correctAnswers()[0],
            ]);
        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                'answer' => 'Wrong',
            ]);

        $progress = StudentModuleProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module1->id)
            ->first();

        $this->assertTrue((bool) $progress?->is_completed);
        $this->assertSame(50, (int) $progress->best_score);

        $module1->update([
            'passing_score' => 100,
            'max_attempts' => 2,
            'is_active' => true,
        ]);

        // Reset completion for second attempt scenario on another module setup:
        // Use a fresh module attempt path by resetting progress and re-running fail then retry.
        StudentModuleProgress::query()->whereKey($progress->id)->delete();
        ModuleQuizSession::query()->where('course_module_id', $module1->id)->delete();

        $module1->update(['passing_score' => 100, 'max_attempts' => 2]);

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module1]));
        foreach ($questions as $question) {
            $this->actingAs($student, 'student')
                ->post(route('student.online-course.quiz.answer', [$service, $module1]), [
                    'answer' => 'Wrong',
                ]);
        }

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module1]))
            ->assertOk()
            ->assertSee('Start timed quiz');

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module1]))
            ->assertRedirect(route('student.online-course.quiz.take', [$service, $module1]));
    }

    /**
     * @return array{0: Student, 1: Service, 2: CourseModule, 3?: CourseModule}
     */
    private function seedBlendedCourse(bool $withMultiSelect = false): array
    {
        $student = Student::query()->create([
            'name' => 'Quiz Student',
            'email' => 'quiz.student@example.com',
            'password' => 'password',
        ]);

        $service = Service::query()->create([
            'title' => 'Blended Guard Course',
            'is_active' => true,
            'has_online_parts' => true,
        ]);

        ServiceBooking::query()->create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
            'payment_status' => 'deposit_paid',
            'booking_type' => 'group',
            'booking_date' => now()->toDateString(),
            'number_of_students' => 1,
            'total_amount' => 250,
        ]);

        $module1 = CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 1',
            'order' => 1,
            'is_active' => true,
            'quiz_time_limit_minutes' => 10,
        ]);

        $module2 = CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 2',
            'order' => 2,
            'is_active' => true,
            'quiz_time_limit_minutes' => 10,
        ]);

        if ($withMultiSelect) {
            ModuleQuizQuestion::query()->create([
                'course_module_id' => $module1->id,
                'question' => 'Pick both safe options',
                'options' => ['A', 'B', 'C'],
                'allow_multiple' => true,
                'correct_answer' => ['A', 'B'],
                'order' => 0,
            ]);
            ModuleQuizQuestion::query()->create([
                'course_module_id' => $module1->id,
                'question' => 'Single choice',
                'options' => ['Yes', 'No'],
                'allow_multiple' => false,
                'correct_answer' => ['Yes'],
                'order' => 1,
            ]);
        } else {
            ModuleQuizQuestion::query()->create([
                'course_module_id' => $module1->id,
                'question' => 'M1 Q1',
                'options' => ['Correct', 'Wrong'],
                'allow_multiple' => false,
                'correct_answer' => ['Correct'],
                'order' => 0,
            ]);
            ModuleQuizQuestion::query()->create([
                'course_module_id' => $module1->id,
                'question' => 'M1 Q2',
                'options' => ['Correct', 'Wrong'],
                'allow_multiple' => false,
                'correct_answer' => ['Correct'],
                'order' => 1,
            ]);
        }

        ModuleQuizQuestion::query()->create([
            'course_module_id' => $module2->id,
            'question' => 'M2 Q1',
            'options' => ['Correct', 'Wrong'],
            'allow_multiple' => false,
            'correct_answer' => ['Correct'],
            'order' => 0,
        ]);

        return [$student, $service, $module1, $module2];
    }

    private function passModule(Student $student, Service $service, CourseModule $module): void
    {
        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module]));

        $questions = $module->quizQuestions()->orderBy('order')->get();
        foreach ($questions as $question) {
            $payload = $question->allow_multiple
                ? ['answers' => $question->correctAnswers()]
                : ['answer' => $question->correctAnswers()[0]];

            $this->actingAs($student, 'student')
                ->post(route('student.online-course.quiz.answer', [$service, $module]), $payload);
        }
    }
}
