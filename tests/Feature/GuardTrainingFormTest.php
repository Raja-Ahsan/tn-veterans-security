<?php

namespace Tests\Feature;

use App\Mail\GuardTrainingFormPublishedMail;
use App\Models\GuardTrainingForm;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GuardTrainingFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_form_with_student_details_prefilled(): void
    {
        $admin = User::factory()->create();
        $student = Student::query()->create([
            'name' => 'Jayson D Wheat',
            'first_name' => 'Jayson',
            'middle_name' => 'D',
            'last_name' => 'Wheat',
            'email' => 'jayson@example.com',
            'password' => 'password',
            'ssn' => '408259937',
            'security_registration_number' => '650102',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.guard-training-forms.create', ['student_id' => $student->id]))
            ->assertOk()
            ->assertSee('Jayson')
            ->assertSee('Wheat')
            ->assertSee('650102');
    }

    public function test_admin_can_publish_form_and_student_can_view_it(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $student = Student::query()->create([
            'name' => 'Jayson Wheat',
            'first_name' => 'Jayson',
            'last_name' => 'Wheat',
            'email' => 'jayson.form@example.com',
            'password' => 'password',
            'ssn' => '408259937',
            'security_registration_number' => '650102',
        ]);
        $service = Service::query()->create([
            'title' => 'Armed Security Certification',
            'is_active' => true,
            'has_online_parts' => true,
        ]);
        $booking = ServiceBooking::query()->create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
            'payment_status' => 'deposit_paid',
            'booking_type' => 'group',
            'booking_date' => now()->toDateString(),
            'number_of_students' => 1,
            'total_amount' => 250,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.guard-training-forms.store'), [
            'student_id' => $student->id,
            'service_id' => $service->id,
            'service_booking_id' => $booking->id,
            'registration_type' => 'add_change_weapon',
            'last_name' => 'Wheat',
            'first_name' => 'Jayson',
            'middle_initial' => 'D',
            'ssn' => '408-25-9937',
            'registration_number' => '650102',
            'initial_general' => 1,
            'initial_general_date' => now()->toDateString(),
            'initial_general_score' => 92,
            'weapons' => [
                [
                    'make_model' => 'Smith & Wesson M&P',
                    'caliber' => '.45',
                    'date' => now()->toDateString(),
                    'score' => 80,
                ],
                [
                    'make_model' => 'Smith & Wesson M&P',
                    'caliber' => '10mm',
                    'date' => now()->toDateString(),
                    'score' => 90,
                ],
            ],
            'trainer_name' => 'Aaron Blessing',
            'trainer_certification_number' => '932207',
            'trainer_email' => 'aaron.blessing@example.com',
            'trainer_phone' => '615-955-2415',
            'publish' => 1,
            'notify_student' => 1,
        ]);

        $form = GuardTrainingForm::query()->first();
        $this->assertNotNull($form);
        $response->assertRedirect(route('admin.guard-training-forms.show', $form));
        $this->assertTrue($form->isPublished());
        $this->assertSame('408259937', $form->ssn);
        $this->assertCount(2, $form->weapons);

        Mail::assertSent(GuardTrainingFormPublishedMail::class);

        $this->actingAs($student, 'student')
            ->get(route('student.certificates.index'))
            ->assertOk()
            ->assertSee($form->form_number)
            ->assertSee('State completion forms');

        $this->actingAs($student, 'student')
            ->get(route('student.guard-training-forms.show', $form))
            ->assertOk()
            ->assertSee('Certificate of Successful Completion')
            ->assertSee('Smith & Wesson M&P')
            ->assertSee('90%');

        $this->actingAs($student, 'student')
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Your completion forms')
            ->assertSee($form->form_number);
    }

    public function test_student_cannot_view_draft_form(): void
    {
        $student = Student::query()->create([
            'name' => 'Draft Student',
            'email' => 'draft@example.com',
            'password' => 'password',
        ]);

        $form = GuardTrainingForm::query()->create([
            'student_id' => $student->id,
            'form_number' => 'IN1144-TEST-001',
            'status' => GuardTrainingForm::STATUS_DRAFT,
            'first_name' => 'Draft',
            'last_name' => 'Student',
        ]);

        $this->actingAs($student, 'student')
            ->get(route('student.guard-training-forms.show', $form))
            ->assertNotFound();
    }
}
