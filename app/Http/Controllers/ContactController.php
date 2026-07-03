<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        ContactSubmission::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $request->input('phone'),
            'subject' => $validated['subject'] ?? 'General Inquiry',
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        session()->forget('contact_captcha_answer');

        return redirect()->route('contact')->with('success', 'Thank you! Your message has been received. We will reply within 24 hours.');
    }
}
