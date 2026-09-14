<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student, public string $token) {}

    public function build(): self
    {
        $resetUrl = route('student.password.reset', [
            'token' => $this->token,
            'email' => $this->student->email,
        ]);

        return $this
            ->subject('Reset your '.config('app.name').' password')
            ->view('emails.student-password-reset', [
                'student' => $this->student,
                'resetUrl' => $resetUrl,
            ]);
    }
}
