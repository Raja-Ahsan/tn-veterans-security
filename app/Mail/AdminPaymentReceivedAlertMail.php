<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPaymentReceivedAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ServiceBooking $booking, public Payment $payment) {}

    public function build(): self
    {
        return $this
            ->subject('Payment Received for Booking')
            ->view('emails.admin-payment-received-alert', [
                'booking' => $this->booking,
                'payment' => $this->payment,
            ]);
    }
}
