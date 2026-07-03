<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClassChangeNotificationMail;
use App\Models\ClassNotification;
use App\Models\ClassSchedule;
use App\Services\ClassNotificationService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClassNotificationController extends Controller
{
    public function __construct(
        private ClassNotificationService $notificationService,
        private SmsService $smsService
    ) {}

    public function store(Request $request, ClassSchedule $classSchedule)
    {
        $validated = $request->validate([
            'notification_type' => 'required|in:class_canceled,class_rescheduled,class_moved,time_changed,instructor_changed',
            'delivery_method' => 'required|in:email,sms,both',
            'message' => 'required|string|max:2000',
        ]);

        $result = $this->notificationService->notifyEnrolledStudents(
            $classSchedule,
            $validated['notification_type'],
            $validated['delivery_method'],
            $validated['message']
        );

        return redirect()->route('admin.class-schedules.show', $classSchedule)
            ->with('success', "Notification sent to {$result['sent']} student(s).".($result['failed'] > 0 ? " {$result['failed']} failed." : ''));
    }

    public function notifyWaitlist(Request $request, ClassSchedule $classSchedule)
    {
        $validated = $request->validate([
            'delivery_method' => 'required|in:email,sms,both',
            'message' => 'required|string|max:2000',
        ]);

        $classSchedule->loadMissing(['service', 'locationRecord', 'instructorRecord']);

        $entries = $classSchedule->waitlistEntries()->where('status', 'waiting')->with('student')->get();
        $studentIds = [];
        $sent = 0;
        $failed = 0;

        foreach ($entries as $entry) {
            $student = $entry->student;
            if (! $student) {
                $failed++;

                continue;
            }

            $studentIds[] = $student->id;
            $delivered = $this->deliverToStudent($student, $validated['delivery_method'], $validated['message'], $classSchedule);

            if ($delivered) {
                $entry->update(['status' => 'notified', 'notified_at' => now()]);
                $sent++;
            } else {
                $failed++;
            }
        }

        ClassNotification::create([
            'class_schedule_id' => $classSchedule->id,
            'sent_by' => Auth::id(),
            'notification_type' => 'waitlist_notification',
            'delivery_method' => $validated['delivery_method'],
            'message' => $validated['message'],
            'student_ids' => $studentIds,
            'sent_count' => $sent,
            'failed_count' => $failed,
        ]);

        return redirect()->route('admin.class-schedules.show', $classSchedule)
            ->with('success', "Waitlist notified: {$sent} sent, {$failed} failed.");
    }

    private function deliverToStudent($student, string $deliveryMethod, string $message, ClassSchedule $schedule): bool
    {
        $emailOk = true;
        $smsOk = true;

        if (in_array($deliveryMethod, ['email', 'both'], true)) {
            try {
                Mail::to($student->email)->send(
                    new ClassChangeNotificationMail($student, $schedule, 'waitlist_notification', $message)
                );
            } catch (\Throwable $e) {
                Log::warning('Waitlist notification email failed', ['student_id' => $student->id, 'error' => $e->getMessage()]);
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

        return $deliveryMethod === 'email' ? $emailOk : $smsOk;
    }
}
