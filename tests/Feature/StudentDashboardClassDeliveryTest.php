<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardClassDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_blended_and_in_person_class_options(): void
    {
        $student = Student::query()->create([
            'name' => 'Dashboard Student',
            'email' => 'dashboard.student@example.com',
            'password' => 'password123',
        ]);

        Service::query()->create([
            'title' => 'Blended Firearms Safety',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 0,
        ]);
        Service::query()->create([
            'title' => 'In Person First Aid',
            'is_active' => true,
            'has_online_parts' => false,
            'testing_in_person' => true,
            'order' => 1,
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Choose a class format')
            ->assertSee('Blended')
            ->assertSee('In Person')
            ->assertDontSee('Browse Online, Blended, or In Person training', false)
            ->assertSee(route('training-classes', ['delivery' => 'blended']), false)
            ->assertSee(route('training-classes', ['delivery' => 'in-person']), false)
            ->assertDontSee(route('training-classes', ['delivery' => 'online']), false)
            ->assertSee('1 class');
    }

    public function test_dashboard_lists_scheduled_classes_with_course_cta_when_deposit_paid(): void
    {
        $student = Student::query()->create([
            'name' => 'Scheduled Student',
            'email' => 'scheduled.student@example.com',
            'password' => 'password123',
        ]);

        $service = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 0,
        ]);

        $schedule = \App\Models\ClassSchedule::query()->create([
            'service_id' => $service->id,
            'class_date' => now()->addDays(10)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'duration_hours' => 8,
            'max_students' => 10,
            'min_students' => 1,
            'current_students' => 1,
            'location' => 'Nashville Training Center',
            'status' => 'scheduled',
        ]);

        \App\Models\ServiceBooking::query()->create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'class_schedule_id' => $schedule->id,
            'status' => 'confirmed',
            'payment_status' => 'deposit_paid',
            'booking_type' => 'individual',
            'booking_date' => $schedule->class_date->toDateString(),
            'number_of_students' => 1,
            'total_amount' => 200,
            'deposit_amount' => 50,
            'remaining_amount' => 150,
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Your scheduled classes')
            ->assertSee('Handgun Carry Permit')
            ->assertSee('Nashville Training Center')
            ->assertSee('Start / continue course')
            ->assertSee(route('student.online-course.index', $service), false)
            ->assertSee('Go to Online Courses')
            ->assertSee(route('student.online-courses.index'), false);
    }

    public function test_my_bookings_upcoming_shows_active_pending_bookings_even_if_schedule_date_passed(): void
    {
        $student = Student::query()->create([
            'name' => 'Booking Student',
            'email' => 'booking.student@example.com',
            'password' => 'password123',
        ]);

        $service = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 0,
        ]);

        $schedule = \App\Models\ClassSchedule::query()->create([
            'service_id' => $service->id,
            'class_date' => now()->subDays(20)->toDateString(),
            'start_time' => '21:40:00',
            'end_time' => '23:00:00',
            'duration_hours' => 2,
            'max_students' => 10,
            'min_students' => 1,
            'current_students' => 1,
            'location' => 'Shooter\'s Guns, Ammo, and Range, Nashville, TN',
            'status' => 'scheduled',
        ]);

        \App\Models\ServiceBooking::query()->create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'class_schedule_id' => $schedule->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'booking_type' => 'individual',
            'booking_date' => now()->subDays(30)->toDateString(),
            'number_of_students' => 1,
            'total_amount' => 200,
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.bookings', ['filter' => 'upcoming']))
            ->assertOk()
            ->assertSee('Handgun Carry Permit')
            ->assertSee('Shooter\'s Guns')
            ->assertDontSee('No upcoming classes');

        $this->actingAs($student, 'student')
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Your scheduled classes')
            ->assertSee('Handgun Carry Permit')
            ->assertSee(now()->subDays(20)->format('F Y'));
    }

    public function test_dashboard_shows_unbooked_open_sessions_for_booking(): void
    {
        $student = Student::query()->create([
            'name' => 'Open Session Student',
            'email' => 'open.session@example.com',
            'password' => 'password123',
        ]);

        $service = Service::query()->create([
            'title' => 'Force Science (De-Escalation)',
            'is_active' => true,
            'has_online_parts' => true,
            'testing_in_person' => true,
            'order' => 0,
        ]);

        \App\Models\ClassSchedule::query()->create([
            'service_id' => $service->id,
            'class_date' => now()->addDays(2)->toDateString(),
            'start_time' => '10:20:00',
            'end_time' => '11:20:00',
            'duration_hours' => 1,
            'max_students' => 10,
            'min_students' => 1,
            'current_students' => 0,
            'location' => 'Nashville Range',
            'status' => 'scheduled',
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Available classes to book')
            ->assertSee('Force Science (De-Escalation)')
            ->assertSee('Book this class')
            ->assertSee(route('student.available-classes', $service->id), false);

        $this->actingAs($student, 'student')
            ->get(route('student.bookings'))
            ->assertOk()
            ->assertSee('Available classes to book')
            ->assertSee('Force Science (De-Escalation)');
    }

    public function test_training_classes_can_be_filtered_by_delivery_format(): void
    {
        Service::query()->create([
            'title' => 'Blended Firearms Safety',
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
            ->assertRedirect(route('training-classes', ['delivery' => 'blended']));

        $this->get(route('training-classes', ['delivery' => 'blended']))
            ->assertOk()
            ->assertSee('BLENDED')
            ->assertSee('Blended Handgun Carry')
            ->assertSee('Blended Firearms Safety')
            ->assertDontSee('>Online<', false);

        $this->getJson(route('training-classes.search', ['delivery' => 'blended']))
            ->assertOk()
            ->assertJsonPath('count', 2)
            ->assertSee('Blended Handgun Carry', false)
            ->assertSee('Blended Firearms Safety', false)
            ->assertDontSee('In Person First Aid', false)
            ->assertSee('bg-cyan-100 text-cyan-800', false)
            ->assertDontSee('bg-sky-100 text-sky-800', false);

        $this->getJson(route('training-classes.search', ['delivery' => 'online']))
            ->assertOk()
            ->assertJsonPath('count', 2);

        $this->get(route('training-classes', ['delivery' => 'in-person']))
            ->assertOk()
            ->assertSee('IN PERSON');

        $this->getJson(route('training-classes.search', ['delivery' => 'in-person']))
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertSee('In Person First Aid', false)
            ->assertDontSee('Blended Firearms Safety', false)
            ->assertDontSee('Blended Handgun Carry', false);
    }
}
