<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardClassDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_online_blended_and_in_person_class_options(): void
    {
        $student = Student::query()->create([
            'name' => 'Dashboard Student',
            'email' => 'dashboard.student@example.com',
            'password' => 'password123',
        ]);

        Service::query()->create([
            'title' => 'Online Firearms Safety',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => false,
            'order' => 0,
        ]);
        Service::query()->create([
            'title' => 'Blended Handgun Carry',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 1,
        ]);
        Service::query()->create([
            'title' => 'In Person First Aid',
            'is_active' => true,
            'has_online_parts' => false,
            'testing_in_person' => true,
            'order' => 2,
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Choose a class format')
            ->assertSee('Online')
            ->assertSee('Blended')
            ->assertSee('In Person')
            ->assertSee(route('training-classes', ['delivery' => 'online']), false)
            ->assertSee(route('training-classes', ['delivery' => 'blended']), false)
            ->assertSee(route('training-classes', ['delivery' => 'in-person']), false)
            ->assertSee('1 class');
    }

    public function test_training_classes_can_be_filtered_by_delivery_format(): void
    {
        Service::query()->create([
            'title' => 'Online Firearms Safety',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => false,
            'order' => 0,
        ]);
        Service::query()->create([
            'title' => 'Blended Handgun Carry',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 1,
        ]);
        Service::query()->create([
            'title' => 'In Person First Aid',
            'is_active' => true,
            'has_online_parts' => false,
            'testing_in_person' => true,
            'order' => 2,
        ]);

        $this->get(route('training-classes', ['delivery' => 'online']))
            ->assertOk()
            ->assertSee('ONLINE')
            ->assertSee('Online Firearms Safety');

        $this->getJson(route('training-classes.search', ['delivery' => 'online']))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertSee('Online Firearms Safety', false)
            ->assertDontSee('Blended Handgun Carry', false)
            ->assertDontSee('In Person First Aid', false);

        $this->get(route('training-classes', ['delivery' => 'blended']))
            ->assertOk()
            ->assertSee('BLENDED')
            ->assertSee('Blended Handgun Carry');

        $this->getJson(route('training-classes.search', ['delivery' => 'blended']))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertSee('Blended Handgun Carry', false)
            ->assertDontSee('Online Firearms Safety', false)
            ->assertDontSee('In Person First Aid', false);

        $this->get(route('training-classes', ['delivery' => 'in-person']))
            ->assertOk()
            ->assertSee('IN PERSON');

        $this->getJson(route('training-classes.search', ['delivery' => 'in-person']))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertSee('In Person First Aid', false)
            ->assertDontSee('Online Firearms Safety', false)
            ->assertDontSee('Blended Handgun Carry', false);
    }
}
