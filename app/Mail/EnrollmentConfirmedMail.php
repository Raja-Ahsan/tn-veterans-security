<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Services\CalendarInviteService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceBooking $booking, public Payment $payment) {}

    public function build(): self
    {
        $schedule = $this->booking->classSchedule;
        $service = $this->booking->service;
        $ics = app(CalendarInviteService::class)->generateIcs($this->booking);

        return $this
            ->subject('Enrollment Confirmed - '.$service->title)
            ->view('emails.enrollment-confirmed', [
                'booking' => $this->booking,
                'payment' => $this->payment,
                'schedule' => $schedule,
                'service' => $service,
                'instructor' => $schedule?->instructor_name,
                'location' => $schedule?->location_name ?? $this->booking->location,
            ])
            ->attachData($ics, 'class-invite.ics', [
                'mime' => 'text/calendar; charset=UTF-8; method=REQUEST',
            ]);
    }
}
