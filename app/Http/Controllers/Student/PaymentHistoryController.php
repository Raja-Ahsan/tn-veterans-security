<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentHistoryController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();

        $payments = Payment::where('student_id', $student->id)
            ->with(['booking.service', 'booking.classSchedule'])
            ->orderByDesc('payment_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('student.payment-history', compact('payments', 'student'));
    }
}
