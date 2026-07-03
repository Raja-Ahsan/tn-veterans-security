<?php

namespace App\Mail;

use App\Models\Service;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BlendedCourseCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Student $student,
        public Service $service,
        public bool $forStudent
    ) {}

    public function build(): self
    {
        $subject = $this->forStudent
            ? 'Online Course Completed - '.$this->service->title
            : 'Student Completed Online Portion - '.$this->service->title;

        return $this->subject($subject)->view('emails.blended-course-completed', [
            'student' => $this->student,
            'service' => $this->service,
            'forStudent' => $this->forStudent,
            'completedAt' => now(),
        ]);
    }
}
