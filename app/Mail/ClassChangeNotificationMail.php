<?php

namespace App\Mail;

use App\Models\ClassSchedule;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClassChangeNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Student $student,
        public ClassSchedule $schedule,
        public string $notificationType,
        public string $message
    ) {}

    public function build(): self
    {
        $typeLabel = ucfirst(str_replace('_', ' ', $this->notificationType));

        return $this
            ->subject("Class Update: {$typeLabel} - ".$this->schedule->service->title)
            ->view('emails.class-change-notification', [
                'student' => $this->student,
                'schedule' => $this->schedule,
                'notificationType' => $typeLabel,
                'message' => $this->message,
            ]);
    }
}
