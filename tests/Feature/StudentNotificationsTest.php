<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Notifications\StudentAlert;
use App\Services\StudentNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StudentNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_notifications_and_mark_all_as_read(): void
    {
        $student = Student::query()->create([
            'name' => 'Notify Student',
            'email' => 'notify.student@example.com',
            'password' => 'password123',
        ]);

        StudentNotifier::push(
            $student,
            'Welcome to TN Veterans Security',
            'Your account is ready.',
            'user',
            route('student.dashboard'),
            'welcome'
        );

        $this->assertSame(1, $student->unreadNotifications()->count());

        $this->actingAs($student, 'student')
            ->get(route('student.notifications.index'))
            ->assertOk()
            ->assertSee('Welcome to TN Veterans Security');

        $this->actingAs($student, 'student')
            ->post(route('student.notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $student->fresh()->notifications()->count());
    }

    public function test_reading_a_notification_removes_it(): void
    {
        $student = Student::query()->create([
            'name' => 'Notify Student',
            'email' => 'notify.remove@example.com',
            'password' => 'password123',
        ]);

        StudentNotifier::push(
            $student,
            'Class update: Class rescheduled',
            'testing',
            'info',
            route('student.bookings'),
            'class_update'
        );

        $notification = $student->notifications()->first();
        $this->assertNotNull($notification);

        $this->actingAs($student, 'student')
            ->post(route('student.notifications.read', $notification->id))
            ->assertRedirect(route('student.bookings'))
            ->assertSessionHas('notification_context.title', 'Class update: Class rescheduled')
            ->assertSessionHas('notification_context.body', 'testing');

        $this->assertSame(0, $student->fresh()->notifications()->count());
    }

    public function test_registration_creates_in_app_notification(): void
    {
        Notification::fake();

        $this->post(route('student.register'), [
            'name' => 'Jane Student',
            'email' => 'jane.notify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('student.dashboard'));

        $student = Student::query()->where('email', 'jane.notify@example.com')->firstOrFail();

        Notification::assertSentTo($student, StudentAlert::class, function (StudentAlert $notification): bool {
            return $notification->category === 'welcome'
                && $notification->title === 'Welcome to TN Veterans Security';
        });
    }
}
