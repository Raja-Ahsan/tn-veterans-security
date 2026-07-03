<?php

namespace App\Services;

use App\Mail\ClassChangeNotificationMail;
use App\Models\ClassSchedule;
use App\Models\ServiceBooking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TravelClassService
{
    /**
     * @return Collection<int, ClassSchedule>
     */
    public function getSchedulesBelowTravelMinimum(): Collection
    {
        return ClassSchedule::query()
            ->with(['service'])
            ->where('class_date', '>=', now()->toDateString())
            ->whereIn('status', ['scheduled', 'full'])
            ->whereHas('service', fn ($q) => $q->where('is_travel_based', true)->whereNotNull('travel_minimum_students'))
            ->get()
            ->filter(function (ClassSchedule $schedule) {
                $minimum = (int) ($schedule->service->travel_minimum_students ?? 0);
                if ($minimum < 1) {
                    return false;
                }

                return $schedule->current_students < $minimum;
            })
            ->values();
    }

    public function notifyEnrolledStudents(ClassSchedule $schedule, string $message): int
    {
        $schedule->loadMissing(['service', 'bookings.student']);

        $bookings = $schedule->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereIn('payment_status', ['deposit_paid', 'fully_paid'])
            ->with('student')
            ->get();

        $sent = 0;
        foreach ($bookings as $booking) {
            if (! $booking->student?->email) {
                continue;
            }

            try {
                Mail::to($booking->student->email)->send(new ClassChangeNotificationMail(
                    $booking->student,
                    $schedule,
                    'travel_update',
                    $message
                ));
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('Travel class notification failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sent;
    }

    public function cancelSchedule(ClassSchedule $schedule, string $reason): void
    {
        $schedule->update(['status' => 'cancelled', 'notes' => trim(($schedule->notes ?? '')."\n\nCancelled: ".$reason)]);

        ServiceBooking::query()
            ->where('class_schedule_id', $schedule->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->update(['status' => 'cancelled']);
    }
}
