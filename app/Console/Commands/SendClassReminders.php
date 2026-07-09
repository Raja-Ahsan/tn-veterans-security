<?php

namespace App\Console\Commands;

use App\Mail\ClassReminderMail;
use App\Models\ServiceBooking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendClassReminders extends Command
{
    protected $signature = 'classes:send-reminders {--days=1 : Days before class to send reminder}';

    protected $description = 'Send class reminder emails to confirmed students for upcoming classes';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $targetDate = now()->addDays($days)->toDateString();

        $bookings = ServiceBooking::query()
            ->with(['student', 'service', 'classSchedule'])
            ->where('status', 'confirmed')
            ->whereHas('classSchedule', fn ($query) => $query->whereDate('class_date', $targetDate))
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            if (! $booking->student?->email) {
                continue;
            }

            Mail::to($booking->student->email)->send(new ClassReminderMail($booking));
            $sent++;
        }

        $this->info("Sent {$sent} class reminder(s) for classes on {$targetDate}.");

        return self::SUCCESS;
    }
}
