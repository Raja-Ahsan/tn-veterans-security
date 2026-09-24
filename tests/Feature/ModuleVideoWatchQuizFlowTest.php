<?php

namespace Tests\Feature;

use App\Models\CourseModule;
use App\Models\CourseModuleVideo;
use App\Models\ModuleQuizQuestion;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\StudentModuleProgress;
use App\Models\StudentVideoProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModuleVideoWatchQuizFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_is_blocked_until_uploaded_video_is_watched(): void
    {
        Mail::fake();
        Storage::fake('public');

        [$student, $service, $module, $video] = $this->seedModuleWithUploadedVideo();

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module]), [
                'video_id' => $video->id,
            ])
            ->assertRedirect(route('student.online-course.module', [$service, $module, 'video' => $video->id]));

        $this->actingAs($student, 'student')
            ->postJson(route('student.online-course.video.watched', [$service, $module, $video]), [
                'completed' => true,
                'duration_seconds' => 60,
                'position_seconds' => 12,
            ])
            ->assertOk()
            ->assertJson(['watched' => false]);

        $this->assertFalse(
            (bool) StudentVideoProgress::query()
                ->where('student_id', $student->id)
                ->where('course_module_video_id', $video->id)
                ->value('video_watched')
        );

        $this->actingAs($student, 'student')
            ->postJson(route('student.online-course.video.watched', [$service, $module, $video]), [
                'completed' => true,
                'duration_seconds' => 60,
                'position_seconds' => 60,
            ])
            ->assertOk()
            ->assertJson(['watched' => true]);

        $this->assertTrue(
            (bool) StudentVideoProgress::query()
                ->where('student_id', $student->id)
                ->where('course_module_video_id', $video->id)
                ->value('video_watched')
        );

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module, 'video' => $video->id]))
            ->assertOk()
            ->assertSee('Start timed quiz')
            ->assertDontSee('Finish watching the video to unlock this quiz', false);

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module]), [
                'video_id' => $video->id,
            ])
            ->assertRedirect(route('student.online-course.quiz.take', [$service, $module]));
    }

    public function test_failing_first_video_quiz_locks_the_next_video(): void
    {
        Mail::fake();
        Storage::fake('public');

        [$student, $service, $module, $video1, $video2] = $this->seedTwoVideos();

        $this->watch($student, $service, $module, $video1);
        $this->failVideoQuiz($student, $service, $module, $video1);

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module, 'video' => $video2->id]))
            ->assertOk()
            ->assertSee('Locked');

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module]), [
                'video_id' => $video2->id,
            ])
            ->assertRedirect(route('student.online-course.module', [$service, $module]));
    }

    public function test_failing_video_quiz_requires_rewatch_then_allows_free_retake(): void
    {
        Mail::fake();
        Storage::fake('public');

        [$student, $service, $module, $video1] = $this->seedTwoVideos();

        $this->watch($student, $service, $module, $video1);
        $this->failVideoQuiz($student, $service, $module, $video1);

        $progress = StudentVideoProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_video_id', $video1->id)
            ->first();

        $this->assertNotNull($progress);
        $this->assertFalse((bool) $progress->video_watched);
        $this->assertFalse((bool) $progress->is_completed);
        $this->assertSame(1, (int) $progress->attempts);

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module]), [
                'video_id' => $video1->id,
            ])
            ->assertRedirect(route('student.online-course.module', [$service, $module, 'video' => $video1->id]));

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module, 'video' => $video1->id]))
            ->assertOk()
            ->assertSee('Finish watching the video to unlock this quiz')
            ->assertDontSee('Quiz attempt used');

        $this->watch($student, $service, $module, $video1);

        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module]), [
                'video_id' => $video1->id,
            ])
            ->assertRedirect(route('student.online-course.quiz.take', [$service, $module]));
    }

    public function test_failing_later_video_quiz_restarts_the_whole_module(): void
    {
        Mail::fake();
        Storage::fake('public');

        [$student, $service, $module, $video1, $video2] = $this->seedTwoVideos();

        $this->watch($student, $service, $module, $video1);
        $this->passVideoQuiz($student, $service, $module, $video1);

        $this->assertTrue(
            (bool) StudentVideoProgress::query()
                ->where('student_id', $student->id)
                ->where('course_module_video_id', $video1->id)
                ->value('is_completed')
        );

        $this->watch($student, $service, $module, $video2);
        $this->failVideoQuiz($student, $service, $module, $video2);

        $video1Progress = StudentVideoProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_video_id', $video1->id)
            ->first();
        $video2Progress = StudentVideoProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_video_id', $video2->id)
            ->first();

        $this->assertFalse((bool) $video1Progress?->is_completed);
        $this->assertFalse((bool) $video1Progress?->video_watched);
        $this->assertFalse((bool) $video2Progress?->is_completed);
        $this->assertFalse((bool) $video2Progress?->video_watched);

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module, 'video' => $video2->id]))
            ->assertOk()
            ->assertSee('Locked');

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module]))
            ->assertOk()
            ->assertSee($video1->title);
    }

    public function test_passing_all_videos_unlocks_the_next_module(): void
    {
        Mail::fake();
        Storage::fake('public');

        [$student, $service, $module1, $video1, $video2, $module2] = $this->seedTwoVideos(withNextModule: true);

        $this->watch($student, $service, $module1, $video1);
        $this->passVideoQuiz($student, $service, $module1, $video1);

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module2]))
            ->assertRedirect(route('student.online-course.index', $service));

        $this->watch($student, $service, $module1, $video2);
        $this->passVideoQuiz($student, $service, $module1, $video2);

        $progress = StudentModuleProgress::query()
            ->where('student_id', $student->id)
            ->where('course_module_id', $module1->id)
            ->first();

        $this->assertTrue((bool) $progress?->is_completed);

        $this->actingAs($student, 'student')
            ->get(route('student.online-course.module', [$service, $module2]))
            ->assertOk();
    }

    /**
     * @return array{0: Student, 1: Service, 2: CourseModule, 3: CourseModuleVideo}
     */
    private function seedModuleWithUploadedVideo(): array
    {
        [$student, $service, $module] = $this->seedBase();

        $path = UploadedFile::fake()->create('lesson.mp4', 200, 'video/mp4')
            ->store('course-videos', 'public');

        $video = CourseModuleVideo::query()->create([
            'course_module_id' => $module->id,
            'title' => 'Video 1',
            'video_path' => $path,
            'original_name' => 'lesson.mp4',
            'order' => 1,
        ]);

        $this->addQuestion($module, $video, 'Q1');

        return [$student, $service, $module, $video];
    }

    /**
     * @return array{0: Student, 1: Service, 2: CourseModule, 3: CourseModuleVideo, 4: CourseModuleVideo, 5?: CourseModule}
     */
    private function seedTwoVideos(bool $withNextModule = false): array
    {
        [$student, $service, $module] = $this->seedBase();

        $path1 = UploadedFile::fake()->create('one.mp4', 200, 'video/mp4')->store('course-videos', 'public');
        $path2 = UploadedFile::fake()->create('two.mp4', 200, 'video/mp4')->store('course-videos', 'public');

        $video1 = CourseModuleVideo::query()->create([
            'course_module_id' => $module->id,
            'title' => 'Video 1',
            'video_path' => $path1,
            'order' => 1,
        ]);
        $video2 = CourseModuleVideo::query()->create([
            'course_module_id' => $module->id,
            'title' => 'Video 2',
            'video_path' => $path2,
            'order' => 2,
        ]);

        $this->addQuestion($module, $video1, 'V1 Q1');
        $this->addQuestion($module, $video2, 'V2 Q1');

        if (! $withNextModule) {
            return [$student, $service, $module, $video1, $video2];
        }

        $module2 = CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 2',
            'order' => 2,
            'is_active' => true,
            'quiz_time_limit_minutes' => 10,
            'passing_score' => 90,
            'max_attempts' => 1,
        ]);

        return [$student, $service, $module, $video1, $video2, $module2];
    }

    /**
     * @return array{0: Student, 1: Service, 2: CourseModule}
     */
    private function seedBase(): array
    {
        $student = Student::query()->create([
            'name' => 'Video Student',
            'email' => 'video.student@example.com',
            'password' => 'password',
        ]);

        $service = Service::query()->create([
            'title' => 'Video Course',
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

        $module = CourseModule::query()->create([
            'service_id' => $service->id,
            'title' => 'Module 1',
            'order' => 1,
            'is_active' => true,
            'quiz_time_limit_minutes' => 10,
            'passing_score' => 90,
            'max_attempts' => 1,
        ]);

        return [$student, $service, $module];
    }

    private function addQuestion(CourseModule $module, CourseModuleVideo $video, string $question): void
    {
        ModuleQuizQuestion::query()->create([
            'course_module_id' => $module->id,
            'course_module_video_id' => $video->id,
            'question' => $question,
            'options' => ['Correct', 'Wrong'],
            'allow_multiple' => false,
            'correct_answer' => ['Correct'],
            'order' => 0,
        ]);
    }

    private function watch(Student $student, Service $service, CourseModule $module, CourseModuleVideo $video): void
    {
        $this->actingAs($student, 'student')
            ->post(route('student.online-course.video.watched', [$service, $module, $video]), [
                'completed' => true,
                'duration_seconds' => 90,
                'position_seconds' => 90,
            ]);
    }

    private function passVideoQuiz(Student $student, Service $service, CourseModule $module, CourseModuleVideo $video): void
    {
        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module]), [
                'video_id' => $video->id,
            ]);

        $question = $video->quizQuestions()->orderBy('order')->first();
        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module]), [
                'answer' => $question->correctAnswers()[0],
            ]);
    }

    private function failVideoQuiz(Student $student, Service $service, CourseModule $module, CourseModuleVideo $video): void
    {
        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.start', [$service, $module]), [
                'video_id' => $video->id,
            ]);

        $question = $video->quizQuestions()->orderBy('order')->first();
        $wrong = collect($question->options)->first(fn ($option) => $option !== $question->correctAnswers()[0]);
        $this->actingAs($student, 'student')
            ->post(route('student.online-course.quiz.answer', [$service, $module]), [
                'answer' => $wrong,
            ]);
    }
}
