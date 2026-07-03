<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingStatusUpdatedMail;
use App\Models\ClassSchedule;
use App\Models\ServiceBooking;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    public function __construct(private CertificateService $certificateService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServiceBooking::with(['service', 'student', 'classSchedule']);

        // Filter by schedule if provided
        if ($request->has('schedule') && $request->schedule) {
            $query->where('class_schedule_id', $request->schedule);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
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
