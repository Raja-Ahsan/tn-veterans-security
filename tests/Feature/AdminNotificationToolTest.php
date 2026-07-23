<?php

namespace Tests\Feature;

use App\Models\ClassSchedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminNotificationToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_tool_page_is_available_in_admin(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.notification-tool.index'))
            ->assertOk()
            ->assertSee('Notify Enrolled Students')
            ->assertSee('Notification Logs');
    }

    public function test_legacy_communication_logs_redirects_to_notification_tool(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.communication-logs.index'))
            ->assertRedirect(route('admin.notification-tool.index'));
    }

    public function test_class_schedule_show_has_notify_enrolled_students_button(): void
    {
        Mail::fake();

        $admin = User::factory()->create();

        $service = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
        ]);

        $schedule = ClassSchedule::query()->create([
            'service_id' => $service->id,
            'class_date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'duration_hours' => 3,
            'min_students' => 1,
            'max_students' => 10,
            'current_students' => 0,
            'status' => 'scheduled',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.class-schedules.show', $schedule))
            ->assertOk()
            ->assertSee('Notify Enrolled Students')
            ->assertSee('id="notify-enrolled"', false);
    }
}
