<?php

namespace App\Services;

use App\Mail\BlendedCourseCompletedMail;
use App\Models\CourseModule;
use App\Models\Instructor;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BlendedCourseCompletionService
{
    public function __construct(
        private SmsService $smsService,
        private CertificateService $certificateService
    ) {}

    public function notifyIfCourseCompleted(Student $student, Service $service, BlendedCourseService $blendedCourse, CourseModule $completedModule): void
    {
        if (! $service->has_online_parts) {
            return;
        }

        if (! $blendedCourse->isEligibleForInPersonTesting($student, $service)) {
            return;
        }

        $this->certificateService->issueForOnlineCourseCompletion($student, $service);

        $modules = $blendedCourse->getModulesForService($service);
        $lastModule = $modules->last();

        if (! $lastModule || $lastModule->id !== $completedModule->id) {
            return;
        }

        $timestamp = now()->format('M d, Y g:i A');
        $message = "{$student->name} completed the online portion of {$service->title} on {$timestamp}. Eligible for in-person testing.";
        try {
            Mail::to($student->email)->send(new BlendedCourseCompletedMail($student, $service, true));
        } catch (\Throwable $e) {
            Log::warning('Blended completion student email failed', ['error' => $e->getMessage()]);
        }

        if ($student->phone) {
            $this->smsService->send($student->phone, "You completed the online portion of {$service->title}. You are eligible for in-person testing. Check your email for details.");
        }

        $instructorEmails = Instructor::where('is_active', true)->whereNotNull('email')->pluck('email')->all();
        $businessEmail = SiteSetting::first()?->email;
        if ($businessEmail) {
            $instructorEmails[] = $businessEmail;
        }

        $instructorEmails = array_unique(array_filter($instructorEmails));

        foreach ($instructorEmails as $email) {
            try {
                Mail::to($email)->send(new BlendedCourseCompletedMail($student, $service, false));
            } catch (\Throwable $e) {
                Log::warning('Blended completion instructor email failed', ['email' => $email, 'error' => $e->getMessage()]);
            }
        }

        if ($student->phone) {
            foreach (Instructor::where('is_active', true)->whereNotNull('phone')->get() as $instructor) {
                $this->smsService->send($instructor->phone, $message);
            }
        }
    }
}
