<?php

namespace App\Services;

use App\Mail\AdminEnrollmentConfirmationMail;
use App\Mail\EnrollmentConfirmedMail;
use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\SiteSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnrollmentConfirmationService
{
    public function __construct(private SmsService $smsService) {}

    public function sendAfterSuccessfulDeposit(ServiceBooking $booking, Payment $payment): void
    {
        $booking->loadMissing(['student', 'service', 'classSchedule.locationRecord', 'classSchedule.instructorRecord']);

        $this->sendStudentEmail($booking, $payment);
        $this->sendStudentSms($booking);
        $this->sendAdminEmail($booking, $payment);
    }

    private function sendStudentEmail(ServiceBooking $booking, Payment $payment): void
    {
        try {
            Mail::to($booking->student->email)->send(new EnrollmentConfirmedMail($booking, $payment));
        } catch (\Throwable $e) {
            Log::warning('Enrollment confirmation email failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendStudentSms(ServiceBooking $booking): void
    {
        $student = $booking->student;
        if (! $student?->phone) {
            return;
        }

        $schedule = $booking->classSchedule;
        $className = $booking->service->title;
        $date = $booking->booking_date
            ? Carbon::parse($booking->booking_date)->format('M d, Y')
            : 'TBD';
        $time = $booking->booking_time
            ? Carbon::parse($booking->booking_time)->format('g:i A')
            : 'TBD';
        $location = $schedule?->location_name ?? $booking->location ?? 'TBD';

        $message = "You are enrolled in {$className} on {$date} at {$time} at {$location}. Check your email for details.";

        $this->smsService->send($student->phone, $message);
    }

    private function sendAdminEmail(ServiceBooking $booking, Payment $payment): void
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

            $adminEmails = array_unique($adminEmails);

            if ($adminEmails === []) {
                return;
            }

            Mail::to($adminEmails)->send(new AdminEnrollmentConfirmationMail($booking, $payment));
        } catch (\Throwable $e) {
            Log::warning('Admin enrollment confirmation email failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
