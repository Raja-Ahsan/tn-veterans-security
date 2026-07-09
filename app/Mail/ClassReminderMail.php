<?php

namespace App\Mail;

use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClassReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceBooking $booking) {}

    public function build(): self
    {
        $schedule = $this->booking->classSchedule;
        $service = $this->booking->service;

        return $this
            ->subject('Class Reminder - '.$service->title)
            ->view('emails.class-reminder', [
                'booking' => $this->booking,
                'schedule' => $schedule,
                'service' => $service,
                'instructor' => $schedule?->instructor_name,
                'location' => $schedule?->location_name ?? $this->booking->location,
                'travelNotes' => $schedule?->travel_notes ?? $service?->travel_notes,
                'lodgingInstructions' => $service?->lodging_instructions,
            ]);
    }
}
