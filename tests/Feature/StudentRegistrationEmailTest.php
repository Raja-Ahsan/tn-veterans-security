<?php

namespace Tests\Feature;

use App\Mail\ContactFormReceivedMail;
use App\Mail\StudentWelcomeMail;
use App\Models\ContactSubmission;
use App\Models\SiteSetting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StudentRegistrationEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_sends_welcome_email(): void
    {
        Mail::fake();

        $response = $this->post(route('student.register'), [
            'name' => 'Jane Student',
            'email' => 'jane.student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '6155550100',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs(Student::query()->where('email', 'jane.student@example.com')->first(), 'student');

        Mail::assertSent(StudentWelcomeMail::class, function (StudentWelcomeMail $mail): bool {
            return $mail->student->email === 'jane.student@example.com'
                && $mail->hasTo('jane.student@example.com');
        });
    }

    public function test_contact_form_sends_admin_and_confirmation_emails(): void
    {
        Mail::fake();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        SiteSetting::query()->create([
            'email' => 'office@example.com',
        ]);

        session(['contact_captcha_answer' => 7]);

        $response = $this->post(route('contact.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'visitor@example.com',
            'phone' => '6155550199',
            'subject' => 'Class question',
            'message' => 'When is the next class?',
            'captcha_answer' => 7,
        ]);

        $response->assertRedirect(route('contact'));
        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'visitor@example.com',
            'subject' => 'Class question',
        ]);

        $submission = ContactSubmission::query()->where('email', 'visitor@example.com')->first();
        $this->assertNotNull($submission);

        Mail::assertSent(ContactFormReceivedMail::class, function (ContactFormReceivedMail $mail) use ($submission): bool {
            return $mail->forAdmin === true
                && $mail->submission->is($submission)
                && ($mail->hasTo('admin@example.com') || $mail->hasTo('office@example.com'));
        });

        Mail::assertSent(ContactFormReceivedMail::class, function (ContactFormReceivedMail $mail) use ($submission): bool {
            return $mail->forAdmin === false
                && $mail->submission->is($submission)
                && $mail->hasTo('visitor@example.com');
        });
    }
}
