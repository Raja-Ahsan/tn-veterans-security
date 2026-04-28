<?php

namespace App\Mail;

use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingPendingPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceBooking $booking) {}

    public function build(): self
    {
        return $this
            ->subject('Booking Received - Payment Required')
            ->view('emails.booking-pending-payment', [
                'booking' => $this->booking,
            ]);
    }
}
