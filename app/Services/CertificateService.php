<?php

namespace App\Services;

use App\Models\CourseCertificate;
use App\Models\ServiceBooking;
use App\Models\User;
use Illuminate\Support\Str;

class CertificateService
{
    public function issueForBooking(ServiceBooking $booking, ?User $issuer = null): ?CourseCertificate
    {
        $booking->loadMissing(['service', 'student']);

        if ($booking->service->has_online_parts) {
            return null;
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

    private function generateNumber(): string
    {
        do {
            $number = 'TNV-'.now()->format('Y').'-'.strtoupper(Str::random(8));
        } while (CourseCertificate::query()->where('certificate_number', $number)->exists());

        return $number;
    }
}
