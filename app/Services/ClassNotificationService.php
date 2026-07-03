<?php

namespace App\Services;

use App\Mail\ClassChangeNotificationMail;
use App\Models\ClassNotification;
use App\Models\ClassSchedule;
use App\Models\ServiceBooking;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClassNotificationService
{
    public function __construct(private SmsService $smsService) {}

    /**
     * @return array{sent: int, failed: int, log: ClassNotification}
     */
    public function notifyEnrolledStudents(
        ClassSchedule $schedule,
        string $notificationType,
        string $deliveryMethod,
        string $message
    ): array {
        $schedule->loadMissing(['service', 'locationRecord', 'instructorRecord']);

        $bookings = ServiceBooking::query()
            ->where('class_schedule_id', $schedule->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->with('student')
            ->get();

        $studentIds = $bookings->pluck('student_id')->unique()->values()->all();
        $sent = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            $student = $booking->student;
            if (! $student) {
                $failed++;

                continue;
            }

            $delivered = $this->deliverToStudent($student, $deliveryMethod, $message, $schedule, $notificationType);
            $delivered ? $sent++ : $failed++;
        }

        $log = ClassNotification::create([
            'class_schedule_id' => $schedule->id,
            'sent_by' => Auth::id(),
            'notification_type' => $notificationType,
            'delivery_method' => $deliveryMethod,
            'message' => $message,
            'student_ids' => $studentIds,
            'sent_count' => $sent,
            'failed_count' => $failed,
        ]);

        return ['sent' => $sent, 'failed' => $failed, 'log' => $log];
    }

    private function deliverToStudent(
        Student $student,
        string $deliveryMethod,
        string $message,
        ClassSchedule $schedule,
        string $notificationType
    ): bool {
        $emailOk = true;
        $smsOk = true;

        if (in_array($deliveryMethod, ['email', 'both'], true)) {
            try {
                Mail::to($student->email)->send(
                    new ClassChangeNotificationMail($student, $schedule, $notificationType, $message)
                );
            } catch (\Throwable $e) {
                Log::warning('Class notification email failed', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
                $emailOk = false;
            }
        }

        if (in_array($deliveryMethod, ['sms', 'both'], true) && $student->phone) {
            $result = $this->smsService->send($student->phone, $message);
            $smsOk = $result['success'];
        } elseif (in_array($deliveryMethod, ['sms', 'both'], true)) {
            $smsOk = false;
        }

        if ($deliveryMethod === 'both') {
            return $emailOk || $smsOk;
        }

        if ($deliveryMethod === 'email') {
            return $emailOk;
        }

        return $smsOk;
    }
}
