<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminEnrollmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceBooking $booking, public Payment $payment) {}

    public function build(): self
    {
        return $this
            ->subject('New Enrollment - '.$this->booking->service->title)
            ->view('emails.admin-enrollment-confirmed', [
                'booking' => $this->booking,
                'payment' => $this->payment,
            ]);
    }
}
