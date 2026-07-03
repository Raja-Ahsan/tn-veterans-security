<?php

namespace App\Services;

use App\Models\ServiceBooking;
use App\Models\SiteSetting;
use Carbon\Carbon;

class CalendarInviteService
{
    public function generateIcs(ServiceBooking $booking): string
    {
        $booking->loadMissing(['service', 'classSchedule', 'student']);
        $schedule = $booking->classSchedule;
        $service = $booking->service;

        $start = $schedule
            ? Carbon::parse($schedule->class_date->format('Y-m-d').' '.$schedule->start_time)
            : Carbon::parse($booking->booking_date.' '.($booking->booking_time ?? '09:00:00'));

        $end = $schedule && $schedule->end_time
            ? Carbon::parse($schedule->class_date->format('Y-m-d').' '.$schedule->end_time)
            : $start->copy()->addHours($schedule?->duration_hours ?? $service->duration_hours ?? 4);

        $uid = 'booking-'.$booking->id.'@tnveteranssecurity';
        $summary = $service->title.' - TN Veterans Security';
        $location = $schedule?->location_name ?? $booking->location ?? '';
        $description = 'Enrollment confirmed for '.$service->title.'.';
        $organizerEmail = SiteSetting::first()?->email ?? config('mail.from.address');

        $format = fn (Carbon $dt): string => $dt->utc()->format('Ymd\THis\Z');

        return implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//TN Veterans Security//Training//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:REQUEST',
            'BEGIN:VEVENT',
            'UID:'.$uid,
            'DTSTAMP:'.$format(now()),
            'DTSTART:'.$format($start),
            'DTEND:'.$format($end),
            'SUMMARY:'.$this->escape($summary),
            'DESCRIPTION:'.$this->escape($description),
            'LOCATION:'.$this->escape($location),
            'ORGANIZER;CN=TN Veterans Security:MAILTO:'.$organizerEmail,
            'END:VEVENT',
            'END:VCALENDAR',
        ]);
    }

    private function escape(string $value): string
    {
        return str_replace(["\r", "\n", ',', ';'], ['', '\\n', '\\,', '\\;'], $value);
    }
}
