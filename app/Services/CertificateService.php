<?php

namespace App\Services;

use App\Models\CourseCertificate;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Str;

class CertificateService
{
    public function __construct(private BlendedCourseService $blendedCourse) {}

    public function issueForBooking(ServiceBooking $booking, ?User $issuer = null): ?CourseCertificate
    {
        $booking->loadMissing(['service', 'student']);

        // Blended courses issue certificates after all online modules are passed (not on booking complete alone).
        if ($booking->service->has_online_parts) {
            return $this->issueForOnlineCourseCompletion($booking->student, $booking->service, $issuer, $booking);
        }

        if (CourseCertificate::query()->where('service_booking_id', $booking->id)->exists()) {
            return CourseCertificate::query()->where('service_booking_id', $booking->id)->first();
        }

        return CourseCertificate::create([
            'student_id' => $booking->student_id,
            'service_id' => $booking->service_id,
            'service_booking_id' => $booking->id,
            'certificate_number' => $this->generateNumber(),
            'issued_at' => now(),
            'issued_by' => $issuer?->id,
        ]);
    }

    public function issueForOnlineCourseCompletion(
        Student $student,
        Service $service,
        ?User $issuer = null,
        ?ServiceBooking $booking = null
    ): ?CourseCertificate {
        if (! $service->has_online_parts) {
            return null;
        }

        if (! $this->blendedCourse->isEligibleForInPersonTesting($student, $service)) {
            return null;
        }

        $booking ??= $this->blendedCourse->paidBookingForService($student, $service);
        if (! $booking) {
            return null;
        }

        $existing = CourseCertificate::query()
            ->where('student_id', $student->id)
            ->where('service_id', $service->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        return CourseCertificate::create([
            'student_id' => $student->id,
            'service_id' => $service->id,
            'service_booking_id' => $booking->id,
            'certificate_number' => $this->generateNumber(),
            'issued_at' => now(),
            'issued_by' => $issuer?->id,
        ]);
    }

    private function generateNumber(): string
    {
        do {
            $number = 'TNV-'.now()->format('Y').'-'.strtoupper(Str::random(8));
        } while (CourseCertificate::query()->where('certificate_number', $number)->exists());

        return $number;
    }
}
