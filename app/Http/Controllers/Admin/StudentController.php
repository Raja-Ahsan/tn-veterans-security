<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()->withCount('bookings');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('security_registration_number', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student->load(['bookings.service', 'bookings.classSchedule', 'bookings.payments']);

        $payments = $student->payments()
            ->with(['booking.service'])
            ->orderByDesc('payment_date')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.students.show', compact('student', 'payments'));
    }
}
