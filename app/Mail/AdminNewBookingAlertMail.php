<?php

namespace App\Mail;

use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewBookingAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceBooking $booking) {}

    public function build(): self
    {
        return $this
            ->subject('New Booking Created')
            ->view('emails.admin-new-booking-alert', [
                'booking' => $this->booking,
            ]);
    }
}
