<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Middleware handles authentication, so we can safely get the user
        $student = Auth::guard('student')->user();

        // Get bookings with proper relationships
        $bookings = ServiceBooking::where('student_id', $student->id)
            ->with(['service', 'classSchedule'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $upcomingBookings = $bookings->filter(function ($booking) {
            return in_array($booking->status, ['pending', 'confirmed'])
                && ($booking->booking_date === null || $booking->booking_date->gte(now()->startOfDay()));
        });

        $pastBookings = $bookings->filter(function ($booking) {
            return $booking->status === 'completed'
                || ($booking->booking_date && $booking->booking_date->lt(now()->startOfDay()));
        });

        $recentBookings = $bookings->take(5);

        $bookingDates = $bookings
            ->filter(function ($b) {
                return $b->booking_date
                    && in_array($b->status, ['pending', 'confirmed'], true);
            })
            ->groupBy(fn ($b) => $b->booking_date->format('Y-m-d'));

        $gridStart = now()->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = now()->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        $calendarWeeks = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $ds = $cursor->format('Y-m-d');
                $week[] = [
                    'day' => $cursor->day,
                    'dateStr' => $ds,
                    'inMonth' => $cursor->month === now()->month,
                    'bookings' => $bookingDates->get($ds, collect()),
                ];
                $cursor->addDay();
            }
            $calendarWeeks[] = $week;
        }

        $calendarTitle = now()->format('F Y');

        return view('student.dashboard', compact(
            'student',
            'bookings',
            'upcomingBookings',
            'pastBookings',
            'recentBookings',
            'calendarWeeks',
            'calendarTitle'
        ));
    }
}
