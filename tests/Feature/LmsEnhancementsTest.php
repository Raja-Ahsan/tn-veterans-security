<?php

namespace Tests\Feature;

use App\Models\CourseModule;
use App\Models\ModuleQuizQuestion;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\StudentModuleProgress;
use App\Services\BlendedCourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LmsEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_index_shows_progress_percent_and_continue_cta(): void
    {
        [$student, $service, $module1, $module2] = $this->seedCourse();

        StudentModuleProgress::query()->create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'course_module_id' => $module1->id,
            'is_completed' => true,
            'best_score' => 100,
            'attempts' => 1,
            'completed_at' => now(),
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.index', $service))
            ->assertOk()
            ->assertSee('Course progress:')
            ->assertSee('50%')
            ->assertSee('Continue')
            ->assertSee(route('student.online-course.module', [$service, $module2], false));
    }

    public function test_module_shows_materials_and_custom_passing_score(): void
    {
        Storage::fake('public');

        [$student, $service, $module1] = $this->seedCourse();

        $path = UploadedFile::fake()->create('handbook.pdf', 120, 'application/pdf')
            ->store('course-materials', 'public');

        $module1->update([
            'passing_score' => 80,
            'max_attempts' => 3,
            'materials' => [
                [
                    'path' => $path,
                    'original_name' => 'handbook.pdf',
                ],
            ],
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module1]))
            ->assertOk()
            ->assertSee('Downloadable materials')
            ->assertSee('handbook.pdf')
            ->assertSee('80% required to pass')
            ->assertSee('3 attempts');
    }

    public function test_progress_summary_helper_counts_completed_modules(): void
    {
        [$student, $service, $module1] = $this->seedCourse();

        StudentModuleProgress::query()->create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'course_module_id' => $module1->id,
            'is_completed' => true,
            'best_score' => 95,
            'attempts' => 1,
            'completed_at' => now(),
        ]);

        $summary = app(BlendedCourseService::class)->progressSummary($student, $service);

        $this->assertSame(1, $summary['completed']);
        $this->assertSame(2, $summary['total']);
        $this->assertSame(50, $summary['percent']);
    }

    /**
     * @return array{0: Student, 1: Service, 2: CourseModule, 3: CourseModule}
     */
    private function seedCourse(): array
    {
        $student = Student::query()->create([
            'name' => 'LMS Student',
            'email' => 'lms.student@example.com',
            'password' => 'password',
        ]);

        $service = Service::query()->create([
            'title' => 'LMS Blended Course',
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
            'passing_score' => 90,
            'max_attempts' => 1,
        ]);

        $module2 = CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 2',
            'order' => 2,
            'is_active' => true,
            'quiz_time_limit_minutes' => 10,
            'passing_score' => 90,
            'max_attempts' => 1,
        ]);

        ModuleQuizQuestion::query()->create([
            'course_module_id' => $module1->id,
            'question' => 'Q1',
            'options' => ['Correct', 'Wrong'],
            'allow_multiple' => false,
            'correct_answer' => ['Correct'],
            'order' => 0,
        ]);

        return [$student, $service, $module1, $module2];
    }
}
