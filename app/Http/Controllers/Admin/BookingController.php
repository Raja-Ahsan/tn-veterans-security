<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingStatusUpdatedMail;
use App\Models\ClassSchedule;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Services\CertificateService;
use App\Services\EnrollmentConfirmationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    public function __construct(private CertificateService $certificateService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = ServiceBooking::with(['service', 'student', 'classSchedule']);

        // Filter by schedule if provided
        if ($request->filled('schedule')) {
            $query->where('class_schedule_id', $request->schedule);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $search = $request->string('q')->trim()->value();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%')
                    ->orWhere('group_name', 'like', '%'.$search.'%')
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('title', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('classSchedule', function ($scheduleQuery) use ($search) {
                        $scheduleQuery->where('location', 'like', '%'.$search.'%');
                    });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'html' => view('admin.bookings.partials.table-rows', [
                    'bookings' => $bookings,
                    'search' => $search,
                ])->render(),
                'pagination' => $bookings->links()->toHtml(),
                'total' => $bookings->total(),
            ]);
        }

        return view('admin.bookings.index', compact('bookings', 'search'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceBooking $booking)
    {
        $booking->load(['service', 'student', 'classSchedule', 'payments']);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update the booking status.
     */
    public function updateStatus(Request $request, ServiceBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $validated['status']]);

        // Update class schedule student count if status changed
        if ($booking->classSchedule && $oldStatus !== $validated['status']) {
            $studentCount = $booking->number_of_students ?? 1;

            if (in_array($oldStatus, ['pending', 'confirmed']) && ! in_array($validated['status'], ['pending', 'confirmed'])) {
                // Decrease count (booking was cancelled or completed)
                $booking->classSchedule->decrementStudentCount($studentCount);
            } elseif (! in_array($oldStatus, ['pending', 'confirmed']) && in_array($validated['status'], ['pending', 'confirmed'])) {
                // Increase count (booking was confirmed)
                $booking->classSchedule->incrementStudentCount($studentCount);
            }
        }

        if ($oldStatus !== $validated['status']) {
            try {
                $booking->loadMissing(['student', 'service', 'classSchedule']);
                Mail::to($booking->student->email)->send(
                    new BookingStatusUpdatedMail($booking, $oldStatus, $validated['status'])
                );
            } catch (\Throwable $exception) {
                Log::warning('Failed to send booking status update email', [
                    'booking_id' => $booking->id,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'error' => $exception->getMessage(),
                ]);
            }

            if ($validated['status'] === 'completed') {
                $this->certificateService->issueForBooking($booking, Auth::user());
            }
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking status updated successfully.');
    }

    /**
     * Manually mark deposit as paid (unlocks blended online modules / quizzes).
     */
    public function markDepositPaid(Request $request, ServiceBooking $booking)
    {
        $booking->loadMissing('service');

        if (in_array($booking->payment_status, ['deposit_paid', 'fully_paid'], true)) {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Deposit is already marked as paid for this booking.');
        }

        $depositAmount = (float) ($booking->deposit_amount ?: $booking->total_amount ?: 0);
        $totalAmount = (float) ($booking->total_amount ?: $depositAmount);
        $remaining = max(0, $totalAmount - $depositAmount);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'student_id' => $booking->student_id,
            'amount' => $depositAmount > 0 ? $depositAmount : $totalAmount,
            'payment_type' => 'deposit',
            'payment_method' => 'other',
            'status' => 'completed',
            'payment_gateway' => 'manual',
            'transaction_id' => 'ADMIN-DEP-'.$booking->id.'-'.now()->format('YmdHis'),
            'notes' => 'Deposit marked paid by admin (unlocks online modules/quizzes).',
            'payment_date' => now()->toDateString(),
        ]);

        $booking->update([
            'payment_status' => 'deposit_paid',
            'remaining_amount' => $remaining,
            'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
        ]);

        try {
            app(EnrollmentConfirmationService::class)->sendAfterSuccessfulDeposit(
                $booking->fresh(['student', 'service', 'classSchedule']),
                $payment
            );
        } catch (\Throwable $e) {
            Log::warning('Enrollment confirmation after admin deposit mark failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        $message = 'Deposit marked as paid. Online modules/quizzes are now unlocked for this student.';
        if ($booking->service?->has_online_parts) {
            $message .= ' Open Student Progress on the class to track quizzes.';
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', $message);
    }

    public function exportRoster(ClassSchedule $classSchedule): StreamedResponse
    {
        $bookings = ServiceBooking::query()
            ->where('class_schedule_id', $classSchedule->id)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->with('student')
            ->orderBy('student_id')
            ->get();

        $filename = 'roster-'.$classSchedule->id.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'Students', 'Payment Status', 'Registration #', 'Reg. Expiration']);

            foreach ($bookings as $booking) {
                $student = $booking->student;
                fputcsv($handle, [
                    $student->name ?? '',
                    $student->email ?? '',
                    $student->phone ?? '',
                    $booking->number_of_students,
                    $booking->payment_status,
                    $student->security_registration_number ?? '',
                    optional($student->security_registration_expiration)->format('Y-m-d') ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
