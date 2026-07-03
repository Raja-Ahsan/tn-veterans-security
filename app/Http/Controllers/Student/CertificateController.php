<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseCertificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View
    {
        $student = Auth::guard('student')->user();
        $certificates = CourseCertificate::query()
            ->where('student_id', $student->id)
            ->with('service')
            ->orderByDesc('issued_at')
            ->get();

        return view('student.certificates.index', compact('certificates'));
    }

    public function show(CourseCertificate $certificate): View
    {
        $student = Auth::guard('student')->user();
        abort_unless($certificate->student_id === $student->id, 403);

        $certificate->load(['service', 'student']);

        return view('student.certificates.show', compact('certificate'));
    }
}
