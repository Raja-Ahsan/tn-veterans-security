<?php

namespace Tests\Feature;

use App\Mail\StudentPasswordResetMail;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class StudentPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_has_forgot_password_link(): void
    {
        $this->get(route('student.login'))
            ->assertOk()
            ->assertSee('Forgot password?')
            ->assertSee(route('student.password.request'), false);
    }

    public function test_forgot_password_page_loads(): void
    {
        $this->get(route('student.password.request'))
            ->assertOk()
            ->assertSee('Forgot Password')
            ->assertSee('Send Reset Link');
    }

    public function test_reset_link_is_sent_for_an_existing_student(): void
    {
        Mail::fake();

        $student = Student::query()->create([
            'name' => 'Jane Student',
            'email' => 'jane.reset@example.com',
            'password' => 'password123',
        ]);

        $this->post(route('student.password.email'), [
            'email' => $student->email,
        ])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertSent(StudentPasswordResetMail::class, function (StudentPasswordResetMail $mail) use ($student): bool {
            return $mail->student->is($student)
                && $mail->hasTo($student->email)
                && filled($mail->token);
        });
    }

    public function test_reset_link_request_does_not_reveal_missing_accounts(): void
    {
        Mail::fake();

        $this->post(route('student.password.email'), [
            'email' => 'nobody@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertNothingSent();
    }

    public function test_student_can_reset_password_with_a_valid_token(): void
    {
        $student = Student::query()->create([
            'name' => 'Jane Student',
            'email' => 'jane.reset@example.com',
            'password' => 'old-password',
        ]);

        $token = Password::broker('students')->createToken($student);

        $this->get(route('student.password.reset', [
            'token' => $token,
            'email' => $student->email,
        ]))
            ->assertOk()
            ->assertSee('Reset Password');

        $this->post(route('student.password.update'), [
            'token' => $token,
            'email' => $student->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(route('student.login'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password', $student->fresh()->password));

        $this->post(route('student.login'), [
            'email' => $student->email,
            'password' => 'new-password',
        ])->assertRedirect(route('student.dashboard'));
    }

    public function test_student_cannot_reset_password_with_an_invalid_token(): void
    {
        $student = Student::query()->create([
            'name' => 'Jane Student',
            'email' => 'jane.reset@example.com',
            'password' => 'old-password',
        ]);

        $this->from(route('student.password.reset', [
            'token' => 'invalid-token',
            'email' => $student->email,
        ]))->post(route('student.password.update'), [
            'token' => 'invalid-token',
            'email' => $student->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('old-password', $student->fresh()->password));
    }
}
