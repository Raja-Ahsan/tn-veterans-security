<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AdminAlert;
use App\Services\AdminNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminInAppNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_header_notifications_page_works(): void
    {
        $admin = User::factory()->create();

        AdminNotifier::broadcast(
            'New student registered',
            'Jane Student created a student account.',
            'user',
            route('admin.students.index'),
            'registration'
        );

        $this->actingAs($admin)
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('New student registered');

        $this->assertSame(1, $admin->fresh()->unreadNotifications()->count());

        $this->actingAs($admin)
            ->post(route('admin.notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $admin->fresh()->notifications()->count());
    }

    public function test_student_registration_notifies_admins(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'email' => 'admin.notify@example.com',
        ]);

        $this->post(route('student.register'), [
            'name' => 'Jane Student',
            'email' => 'jane.admin.notify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('student.dashboard'));

        Notification::assertSentTo($admin, AdminAlert::class, function (AdminAlert $notification): bool {
            return $notification->category === 'registration'
                && $notification->title === 'New student registered';
        });
    }
}
