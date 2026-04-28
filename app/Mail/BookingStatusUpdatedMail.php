<?php

namespace App\Mail;

use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceBooking $booking,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Booking Status Updated')
            ->view('emails.booking-status-updated', [
                'booking' => $this->booking,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
            ]);
    }
}
