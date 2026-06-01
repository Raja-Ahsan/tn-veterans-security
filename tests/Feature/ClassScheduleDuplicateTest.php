<?php

namespace Tests\Feature;

use App\Models\ClassSchedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassScheduleDuplicateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_admin_cannot_create_duplicate_class_on_same_date_time_and_location(): void
    {
        $admin = User::factory()->create();
        $service = Service::query()->create([
            'title' => 'Armed Guard Training',
            'is_active' => true,
        ]);

        ClassSchedule::query()->create([
            'service_id' => $service->id,
            'class_date' => '2026-06-15',
            'start_time' => '12:12:00',
            'end_time' => '20:12:00',
            'duration_hours' => 8,
            'max_students' => 10,
            'min_students' => 2,
            'current_students' => 0,
            'location' => 'Nashville',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.class-schedules.store'), [
            'service_id' => $service->id,
            'class_date' => '2026-06-15',
            'start_time' => '12:12',
            'duration_hours' => 8,
            'max_students' => 10,
            'min_students' => 2,
            'locations' => ['Nashville'],
        ]);

        $response->assertSessionHasErrors('class_date');
        $this->assertSame(1, ClassSchedule::query()->count());
    }

    public function test_admin_can_create_class_with_same_date_time_but_different_location(): void
    {
        $admin = User::factory()->create();
        $service = Service::query()->create([
            'title' => 'Armed Guard Training',
            'is_active' => true,
        ]);

        ClassSchedule::query()->create([
            'service_id' => $service->id,
            'class_date' => '2026-06-15',
            'start_time' => '12:12:00',
            'end_time' => '20:12:00',
            'duration_hours' => 8,
            'max_students' => 10,
            'min_students' => 2,
            'current_students' => 0,
            'location' => 'Nashville',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.class-schedules.store'), [
            'service_id' => $service->id,
            'class_date' => '2026-06-15',
            'start_time' => '12:12',
            'duration_hours' => 8,
            'max_students' => 10,
            'min_students' => 2,
            'locations' => ['Memphis'],
        ]);

        $response->assertRedirect(route('admin.class-schedules.index'));
        $response->assertSessionHas('success');
        $this->assertSame(2, ClassSchedule::query()->count());
    }
}
