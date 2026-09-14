<?php

namespace Tests\Feature;

use App\Models\ClassSchedule;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StudentOneEnrollmentPerClassTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_book_a_second_session_of_the_same_class(): void
    {
        Mail::fake();

        [$service, $firstSchedule] = $this->createBookableClass();
        $secondSchedule = $this->createSchedule($service, now()->addWeeks(2)->toDateString());
        $student = $this->createStudent('already.enrolled@example.com');

        $existing = $this->enroll($student, $service, $firstSchedule, 'pending');

        $this->actingAs($student, 'student')
            ->post(route('student.booking.store'), [
                'service_id' => $service->id,
                'class_schedule_id' => $secondSchedule->id,
            ])
            ->assertRedirect(route('student.bookings.show', $existing))
            ->assertSessionHas('info', ServiceBooking::alreadyEnrolledMessage());

        $this->assertSame(1, ServiceBooking::query()->where('student_id', $student->id)->count());
        $this->assertTrue(ServiceBooking::query()->whereKey($existing->id)->exists());
    }

    public function test_student_can_book_a_different_class(): void
    {
        Mail::fake();

        [$firstService, $firstSchedule] = $this->createBookableClass('Handgun Carry Permit');
        [$secondService, $secondSchedule] = $this->createBookableClass('Unarmed Guard Class');
        $student = $this->createStudent('two.classes@example.com');

        $this->enroll($student, $firstService, $firstSchedule, 'confirmed');

        $this->actingAs($student, 'student')
            ->post(route('student.booking.store'), [
                'service_id' => $secondService->id,
                'class_schedule_id' => $secondSchedule->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(2, ServiceBooking::query()->where('student_id', $student->id)->count());
    }

    public function test_student_can_book_again_after_a_cancelled_enrollment(): void
    {
        Mail::fake();

        [$service, $schedule] = $this->createBookableClass();
        $student = $this->createStudent('cancelled.then.book@example.com');

        $this->enroll($student, $service, $schedule, 'cancelled');

        $this->actingAs($student, 'student')
            ->post(route('student.booking.store'), [
                'service_id' => $service->id,
                'class_schedule_id' => $schedule->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(2, ServiceBooking::query()->where('student_id', $student->id)->count());
        $this->assertSame(1, ServiceBooking::query()
            ->where('student_id', $student->id)
            ->whereIn('status', ServiceBooking::openEnrollmentStatuses())
            ->count());
    }

    public function test_logged_in_inquiry_redirects_to_the_existing_booking(): void
    {
        [$service, $schedule] = $this->createBookableClass();
        $student = $this->createStudent('logged.in.inquiry@example.com');
        $booking = $this->enroll($student, $service, $schedule, 'confirmed');

        $this->actingAs($student, 'student')
            ->post(route('training-classes.booking-inquiry', $service), [
                'name' => $student->name,
                'email' => $student->email,
                'phone' => '615-555-0111',
                'class_schedule_id' => $schedule->id,
            ])
            ->assertRedirect(route('student.bookings.show', $booking))
            ->assertSessionHas('info', ServiceBooking::alreadyEnrolledMessage());

        $this->assertNull(session('booking_inquiry_'.$service->id));
        $this->assertSame(1, ServiceBooking::query()->where('student_id', $student->id)->count());
    }

    public function test_guest_inquiry_with_an_existing_student_email_is_rejected(): void
    {
        [$service, $schedule] = $this->createBookableClass();
        $student = $this->createStudent('guest.duplicate@example.com');
        $this->enroll($student, $service, $schedule, 'pending');

        $this->from(route('training-classes.show', $service->id))
            ->post(route('training-classes.booking-inquiry', $service), [
                'name' => 'Guest Name',
                'email' => $student->email,
                'phone' => '615-555-0222',
                'class_schedule_id' => $schedule->id,
            ])
            ->assertRedirect(route('training-classes.show', $service->id))
            ->assertSessionHasErrors('email');

        $this->assertSame(1, ServiceBooking::query()->where('student_id', $student->id)->count());
    }

    public function test_class_details_hides_book_form_when_the_student_is_already_enrolled(): void
    {
        [$service, $schedule] = $this->createBookableClass();
        $student = $this->createStudent('sees.existing@example.com');
        $booking = $this->enroll($student, $service, $schedule, 'pending');

        $this->actingAs($student, 'student')
            ->get(route('training-classes.show', $service->id))
            ->assertOk()
            ->assertDontSee('Book Now', false)
            ->assertDontSee('id="service-booking-form"', false)
            ->assertSee('View your booking', false)
            ->assertSee(route('student.bookings.show', $booking), false);

        $this->actingAs($student, 'student')
            ->get(route('student.booking.create.schedule', [
                'serviceId' => $service->id,
                'scheduleId' => $schedule->id,
            ]))
            ->assertRedirect(route('student.bookings.show', $booking));
    }

    public function test_checkout_does_not_create_a_second_booking_for_the_same_class(): void
    {
        Mail::fake();

        [$service, $schedule] = $this->createBookableClass();
        $student = $this->createStudent('checkout.duplicate@example.com');
        $booking = $this->enroll($student, $service, $schedule, 'pending');

        $this->actingAs($student, 'student')
            ->withSession([
                'booking_inquiry_'.$service->id => [
                    'name' => $student->name,
                    'email' => $student->email,
                    'class_schedule_id' => $schedule->id,
                    'number_of_students' => 1,
                ],
            ])
            ->post(route('student.classes.checkout.process', $service->id), [
                'policy_acknowledged' => '1',
            ])
            ->assertRedirect(route('student.bookings.show', $booking))
            ->assertSessionHas('info', ServiceBooking::alreadyEnrolledMessage());

        $this->assertSame(1, ServiceBooking::query()->where('student_id', $student->id)->count());
        $this->assertTrue(ServiceBooking::query()->whereKey($booking->id)->exists());
    }

    private function createStudent(string $email): Student
    {
        return Student::query()->create([
            'name' => 'Enrollment Student',
            'email' => $email,
            'password' => 'password123',
        ]);
    }

    /**
     * @return array{0: Service, 1: ClassSchedule}
     */
    private function createBookableClass(string $title = 'Handgun Carry Permit'): array
    {
        $service = Service::query()->create([
            'title' => $title,
            'is_active' => true,
            'price' => 150,
            'deposit_amount' => 20,
            'class_type' => 'group',
            'min_students' => 2,
        ]);

        $schedule = $this->createSchedule($service, now()->addWeek()->toDateString());

        return [$service, $schedule];
    }

    private function createSchedule(Service $service, string $classDate): ClassSchedule
    {
        return ClassSchedule::query()->create([
            'service_id' => $service->id,
            'class_date' => $classDate,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'duration_hours' => 8,
            'max_students' => 10,
            'min_students' => 2,
            'current_students' => 0,
            'location' => 'Nashville',
            'status' => 'scheduled',
        ]);
    }

    private function enroll(Student $student, Service $service, ClassSchedule $schedule, string $status): ServiceBooking
    {
        return ServiceBooking::query()->create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'class_schedule_id' => $schedule->id,
            'status' => $status,
            'payment_status' => 'pending',
            'booking_type' => 'group',
            'booking_date' => $schedule->class_date->toDateString(),
            'number_of_students' => 1,
            'total_amount' => 150,
        ]);
    }
}
