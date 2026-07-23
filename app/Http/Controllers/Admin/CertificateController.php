<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCertificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $certificates = CourseCertificate::query()
            ->with(['student', 'service'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->toString();
                $query->where(function ($inner) use ($q) {
                    $inner->where('certificate_number', 'like', "%{$q}%")
                        ->orWhereHas('student', fn ($s) => $s->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
                        ->orWhereHas('service', fn ($s) => $s->where('title', 'like', "%{$q}%"));
                });
            })
            ->orderByDesc('issued_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.certificates.index', compact('certificates'));
    }

    public function show(CourseCertificate $certificate): View
    {
        $certificate->load(['student', 'service', 'booking']);

        return view('admin.certificates.show', compact('certificate'));
    }

    public function print(CourseCertificate $certificate): View
    {
        $certificate->load(['student', 'service']);

        return view('student.certificates.show', compact('certificate'));
    }

    public function destroy(CourseCertificate $certificate): RedirectResponse
    {
        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate revoked.');
    }
}
