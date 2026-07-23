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
     * @return array{sent: int, failed: int, log: ClassNotification, errors: list<string>}
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
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->where('payment_status', '!=', 'refunded')
            ->with('student')
            ->get();

        $studentIds = $bookings->pluck('student_id')->unique()->values()->all();
        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($bookings as $booking) {
            $student = $booking->student;
            if (! $student || blank($student->email)) {
                $failed++;
                $errors[] = 'Missing student or email for booking #'.$booking->id;

                continue;
            }

            $result = $this->deliverToStudent(
                $student,
                $deliveryMethod,
                $message,
                $schedule,
                $notificationType,
                $booking
            );
            if ($result['ok']) {
                $sent++;
            } else {
                $failed++;
                if (! empty($result['error'])) {
                    $errors[] = $student->email.': '.$result['error'];
                }
            }
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

        return [
            'sent' => $sent,
            'failed' => $failed,
            'log' => $log,
            'errors' => array_slice($errors, 0, 5),
        ];
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    private function deliverToStudent(
        Student $student,
        string $deliveryMethod,
        string $message,
        ClassSchedule $schedule,
        string $notificationType,
        ?ServiceBooking $booking = null
    ): array {
        $emailOk = true;
        $smsOk = true;
        $errors = [];

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
                $errors[] = $e->getMessage();
            }
        }

        $typeLabel = ucfirst(str_replace('_', ' ', $notificationType));
        $classTitle = $schedule->service?->title ?? 'your class';
        $actionUrl = $booking
            ? route('student.bookings.show', $booking->id)
            : route('student.bookings');

        StudentNotifier::push(
            $student,
            "Class update: {$typeLabel}",
            "{$classTitle}: {$message}",
            'info',
            $actionUrl,
            'class_update'
        );

        if (in_array($deliveryMethod, ['sms', 'both'], true) && $student->phone) {
            $result = $this->smsService->send($student->phone, $message);
            $smsOk = $result['success'];
            if (! $smsOk && ! empty($result['message'])) {
                $errors[] = $result['message'];
            }
        } elseif (in_array($deliveryMethod, ['sms', 'both'], true)) {
            $smsOk = false;
            $errors[] = 'Student has no phone number';
        }

        $ok = match ($deliveryMethod) {
            'both' => $emailOk || $smsOk,
            'email' => $emailOk,
            default => $smsOk,
        };

        return [
            'ok' => $ok,
            'error' => $ok ? null : implode('; ', $errors),
        ];
    }
}
