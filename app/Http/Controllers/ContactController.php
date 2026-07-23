<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormReceivedMail;
use App\Models\ContactSubmission;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\AdminNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('company_website')) {
            return redirect()->route('contact')->with('success', 'Thank you! Your message has been received.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'captcha_answer' => 'required|integer',
        ]);

        if ((int) $validated['captcha_answer'] !== (int) session('contact_captcha_answer')) {
            throw ValidationException::withMessages([
                'captcha_answer' => 'Incorrect answer. Please try again.',
            ]);
        }

        $submission = ContactSubmission::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $request->input('phone'),
            'subject' => $validated['subject'] ?? 'General Inquiry',
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        session()->forget('contact_captcha_answer');

        $this->sendContactEmails($submission);

        AdminNotifier::broadcast(
            'New contact form submission',
            trim("{$submission->first_name} {$submission->last_name}").' sent: '.($submission->subject ?: 'General Inquiry'),
            'envelope',
            route('admin.contact-submissions.show', $submission),
            'contact'
        );

        return redirect()->route('contact')->with('success', 'Thank you! Your message has been received. We will reply within 24 hours.');
    }

    private function sendContactEmails(ContactSubmission $submission): void
    {
        try {
            $adminEmails = User::query()
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $businessEmail = SiteSetting::first()?->email;
            if ($businessEmail) {
                $adminEmails[] = $businessEmail;
            }

            $adminEmails = array_values(array_unique(array_filter($adminEmails)));

            if ($adminEmails !== []) {
                Mail::to($adminEmails)->send(new ContactFormReceivedMail($submission, true));
            }
        } catch (\Throwable $exception) {
            Log::warning('Contact form admin email failed', [
                'submission_id' => $submission->id,
                'error' => $exception->getMessage(),
            ]);
        }

        try {
            Mail::to($submission->email)->send(new ContactFormReceivedMail($submission, false));
        } catch (\Throwable $exception) {
            Log::warning('Contact form confirmation email failed', [
                'submission_id' => $submission->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
