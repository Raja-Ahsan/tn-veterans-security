<?php

namespace Tests\Feature;

use App\Models\ClassSchedule;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClassBookingFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_booking_form_does_not_ask_for_student_count_or_group_name(): void
    {
        [$service, $schedule] = $this->createBookableClass();

        $this->get(route('training-classes.show', $service->id))
            ->assertOk()
            ->assertDontSee('Number of students', false)
            ->assertDontSee('Group Name', false)
            ->assertDontSee('name="number_of_students"', false)
            ->assertDontSee('name="group_name"', false)
            ->assertSee('Book Now')
            ->assertSee('one seat for you', false);

        $student = Student::query()->create([
            'name' => 'Booking Student',
            'email' => 'booking.student@example.com',
            'password' => 'password123',
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.booking.create.schedule', [
                'serviceId' => $service->id,
                'scheduleId' => $schedule->id,
            ]))
            ->assertOk()
            ->assertDontSee('Number of Students', false)
            ->assertDontSee('Group Name', false)
            ->assertDontSee('name="number_of_students"', false)
            ->assertDontSee('name="group_name"', false);
    }

    public function test_booking_inquiry_always_reserves_a_single_seat(): void
    {
        [$service, $schedule] = $this->createBookableClass();

        $this->post(route('training-classes.booking-inquiry', $service), [
            'name' => 'Solo Student',
            'email' => 'solo.booker@example.com',
            'phone' => '615-555-0199',
            'class_schedule_id' => $schedule->id,
            'number_of_students' => 5,
            'group_name' => 'Team Alpha',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('student.classes.checkout', $service->id));

        $inquiry = session('booking_inquiry_'.$service->id);

        $this->assertSame(1, $inquiry['number_of_students']);
        $this->assertArrayNotHasKey('group_name', $inquiry);
    }

    public function test_student_booking_store_ignores_submitted_student_count_and_group_name(): void
    {
        Mail::fake();

        [$service, $schedule] = $this->createBookableClass();
        $student = Student::query()->create([
            'name' => 'Booking Student',
            'email' => 'booking.store@example.com',
            'password' => 'password123',
        ]);

        $this->actingAs($student, 'student')
            ->post(route('student.booking.store'), [
                'service_id' => $service->id,
                'class_schedule_id' => $schedule->id,
                'number_of_students' => 5,
                'group_name' => 'Team Alpha',
                'notes' => 'Please email the packet.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $booking = ServiceBooking::query()->where('student_id', $student->id)->firstOrFail();

        $this->assertSame(1, $booking->number_of_students);
        $this->assertNull($booking->group_name);
        $this->assertSame('Please email the packet.', $booking->notes);
    }

    /**
     * @return array{0: Service, 1: ClassSchedule}
     */
    private function createBookableClass(): array
    {
        $service = Service::query()->create([
            'title' => 'Handgun Carry Permit',
            'is_active' => true,
            'price' => 150,
            'deposit_amount' => 20,
            'class_type' => 'group',
            'min_students' => 2,
        ]);

        $schedule = ClassSchedule::query()->create([
            'service_id' => $service->id,
            'class_date' => now()->addWeek()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'duration_hours' => 8,
            'max_students' => 10,
            'min_students' => 2,
            'current_students' => 0,
            'location' => 'Nashville',
            'status' => 'scheduled',
        ]);

        return [$service, $schedule];
    }
}
