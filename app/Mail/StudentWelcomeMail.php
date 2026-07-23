<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student) {}

    public function build(): self
    {
        return $this
            ->subject('Welcome to '.config('app.name'))
            ->view('emails.student-welcome', [
                'student' => $this->student,
                'loginUrl' => route('student.login'),
                'dashboardUrl' => route('student.dashboard'),
            ]);
    }
}
