<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactSubmission::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $submissions = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.contact-submissions.index', compact('submissions'));
    }

    public function show(ContactSubmission $contactSubmission)
    {
        if ($contactSubmission->status === 'new') {
            $contactSubmission->update(['status' => 'read']);
        }

        return view('admin.contact-submissions.show', compact('contactSubmission'));
    }

    public function destroy(ContactSubmission $contactSubmission)
    {
        $contactSubmission->delete();

        return redirect()->route('admin.contact-submissions.index')
            ->with('success', 'Contact submission deleted.');
    }

    public function updateStatus(Request $request, ContactSubmission $contactSubmission)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,responded,archived',
        ]);

        $contactSubmission->update(['status' => $validated['status']]);

        return redirect()->route('admin.contact-submissions.show', $contactSubmission)
            ->with('success', 'Status updated.');
    }
}
