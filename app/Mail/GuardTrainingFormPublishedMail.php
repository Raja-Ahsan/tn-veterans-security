<?php

namespace App\Mail;

use App\Models\GuardTrainingForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuardTrainingFormPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GuardTrainingForm $form) {}

    public function build(): self
    {
        $this->form->loadMissing(['student', 'service']);

        return $this->subject('Your Guard Training Completion Form is ready')
            ->view('emails.guard-training-form-published', [
                'form' => $this->form,
                'student' => $this->form->student,
            ]);
    }
}
