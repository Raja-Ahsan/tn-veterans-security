<?php

namespace Database\Seeders;

use App\Models\ClassSchedule;
use App\Models\CourseModule;
use App\Models\ModuleQuizQuestion;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BlendedCourseDemoSeeder extends Seeder
{
    private const DEMO_SERVICE_TITLE = 'Blended Security Training (Demo)';

    private const DEMO_STUDENT_EMAIL = 'student.demo@test.com';

    /**
     * Seed a blended class with 2 modules, quiz questions, and an enrolled demo student.
     */
    public function run(): void
    {
        $service = Service::updateOrCreate(
            ['title' => self::DEMO_SERVICE_TITLE],
            [
                'short_description' => 'Demo blended course for testing online modules and quizzes.',
                'description' => 'Use this class to test the full blended learning flow: online modules, 90% quiz pass, and in-person test marking.',
                'categories' => ['security_training'],
                'subcategory' => 'Blended Demo',
                'price' => 150.00,
                'deposit_amount' => 20.00,
                'duration_hours' => 8,
                'max_students' => 12,
                'min_students' => 2,
                'class_type' => 'group',
                'has_online_parts' => true,
                'testing_in_person' => true,
                'is_active' => true,
                'order' => 99,
            ]
        );

        $modules = $this->seedModules($service);

        $schedule = ClassSchedule::updateOrCreate(
            [
                'service_id' => $service->id,
                'class_date' => now()->addDays(14)->toDateString(),
                'start_time' => '09:00:00',
            ],
            [
                'end_time' => '17:00:00',
                'duration_hours' => 8,
                'max_students' => 12,
                'min_students' => 2,
                'current_students' => 1,
                'room' => 'Demo Room A',
                'location' => 'Main Training Center',
                'instructor' => 'Demo Instructor',
                'status' => 'scheduled',
                'notes' => 'Seeded schedule for blended course demo testing.',
            ]
        );

        $student = Student::updateOrCreate(
            ['email' => self::DEMO_STUDENT_EMAIL],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('password'),
                'phone' => '615-555-0100',
            ]
        );

        ServiceBooking::updateOrCreate(
            [
                'student_id' => $student->id,
                'service_id' => $service->id,
                'class_schedule_id' => $schedule->id,
            ],
            [
                'booking_date' => now()->toDateString(),
                'booking_time' => '09:00:00',
                'location' => $schedule->location,
                'status' => 'confirmed',
                'payment_status' => 'deposit_paid',
                'total_amount' => 150.00,
                'deposit_amount' => 20.00,
                'remaining_amount' => 130.00,
                'booking_type' => 'group',
                'number_of_students' => 1,
            ]
        );

        $baseUrl = rtrim(config('app.url'), '/');

        $this->command?->newLine();
        $this->command?->info('=== Blended Course Demo Seeded ===');
        $this->command?->line("Service ID: {$service->id}");
        $this->command?->line("Module 1 ID: {$modules[0]->id}");
        $this->command?->line("Module 2 ID: {$modules[1]->id}");
        $this->command?->line("Schedule ID: {$schedule->id}");
        $this->command?->line("Student ID: {$student->id}");
        $this->command?->newLine();
        $this->command?->info('Demo student login');
        $this->command?->line('Email: '.self::DEMO_STUDENT_EMAIL);
        $this->command?->line('Password: password');
        $this->command?->newLine();
        $this->command?->info('Admin URLs');
        $this->command?->line("{$baseUrl}/admin/classes/{$service->id}/edit");
        $this->command?->line("{$baseUrl}/admin/classes/{$service->id}/course-modules");
        $this->command?->line("{$baseUrl}/admin/classes/{$service->id}/blended-progress");
        $this->command?->newLine();
        $this->command?->info('Student URLs');
        $this->command?->line("{$baseUrl}/student/login");
        $this->command?->line("{$baseUrl}/student/online-courses");
        $this->command?->line("{$baseUrl}/student/courses/{$service->id}/online");
        $this->command?->line("{$baseUrl}/student/courses/{$service->id}/online/modules/{$modules[0]->id}");
        $this->command?->line("{$baseUrl}/student/courses/{$service->id}/online/modules/{$modules[1]->id}");
        $this->command?->newLine();
        $this->command?->info('Quiz answers (all correct)');
        $this->command?->line('Module 1 Q1: Observe and report');
        $this->command?->line('Module 1 Q2: Call 911');
        $this->command?->line('Module 2 Q1: Every 30 minutes');
        $this->command?->line('Module 2 Q2: Document and notify supervisor');
    }

    /**
     * @return array{0: CourseModule, 1: CourseModule}
     */
    private function seedModules(Service $service): array
    {
        $moduleOne = CourseModule::updateOrCreate(
            [
                'service_id' => $service->id,
                'title' => 'Module 1: Security Basics',
            ],
            [
                'content' => "Welcome to blended security training.\n\nIn this module you will learn:\n- Role of a security officer\n- Observe and report mindset\n- Emergency response basics",
                'video_url' => null,
                'order' => 1,
                'is_active' => true,
            ]
        );

        $moduleTwo = CourseModule::updateOrCreate(
            [
                'service_id' => $service->id,
                'title' => 'Module 2: Patrol Procedures',
            ],
            [
                'content' => "This module covers patrol best practices.\n\nTopics:\n- Patrol intervals\n- Incident documentation\n- Supervisor communication",
                'video_url' => null,
                'order' => 2,
                'is_active' => true,
            ]
        );

        $this->seedQuestions($moduleOne, [
            [
                'question' => 'What is the primary duty of a security officer?',
                'options' => ['Use force first', 'Observe and report', 'Ignore suspicious activity', 'Leave the post early'],
                'correct_answer' => 'Observe and report',
                'order' => 1,
            ],
            [
                'question' => 'Who should you contact first in a life-threatening emergency?',
                'options' => ['Social media', 'Call 911', 'Wait until shift ends', 'Only tell a coworker'],
                'correct_answer' => 'Call 911',
                'order' => 2,
            ],
        ]);

        $this->seedQuestions($moduleTwo, [
            [
                'question' => 'How often should patrol rounds typically be completed on most sites?',
                'options' => ['Once per week', 'Every 30 minutes', 'Only at clock-in', 'Never'],
                'correct_answer' => 'Every 30 minutes',
                'order' => 1,
            ],
            [
                'question' => 'What should you do after finding a minor incident during patrol?',
                'options' => ['Ignore it', 'Document and notify supervisor', 'Delete camera footage', 'Post online'],
                'correct_answer' => 'Document and notify supervisor',
                'order' => 2,
            ],
        ]);

        return [$moduleOne, $moduleTwo];
    }

    /**
     * @param  array<int, array{question: string, options: array<int, string>, correct_answer: string, order: int}>  $questions
     */
    private function seedQuestions(CourseModule $module, array $questions): void
    {
        $module->quizQuestions()->delete();

        foreach ($questions as $question) {
            ModuleQuizQuestion::create([
                'course_module_id' => $module->id,
                'question' => $question['question'],
                'options' => $question['options'],
                'correct_answer' => $question['correct_answer'],
                'order' => $question['order'],
            ]);
        }
    }
}
