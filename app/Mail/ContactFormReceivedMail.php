<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactSubmission $submission,
        public bool $forAdmin = true
    ) {}

    public function build(): self
    {
        $subject = $this->forAdmin
            ? 'New contact form: '.($this->submission->subject ?: 'General Inquiry')
            : 'We received your message — '.config('app.name');

        return $this
            ->subject($subject)
            ->view('emails.contact-form-received', [
                'submission' => $this->submission,
                'forAdmin' => $this->forAdmin,
                'contactBody' => $this->submission->message,
            ]);
    }
}
