<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceBooking $booking, public Payment $payment) {}

    public function build(): self
    {
        return $this
            ->subject('Booking Confirmed - Payment Received')
            ->view('emails.booking-confirmed', [
                'booking' => $this->booking,
                'payment' => $this->payment,
            ]);
    }
}
