<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Support\PublicTrainingServiceQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $student = Auth::guard('student')->user();

        $bookings = ServiceBooking::where('student_id', $student->id)
            ->with(['service', 'classSchedule'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $upcomingBookings = $bookings->filter(function (ServiceBooking $booking) {
            return in_array($booking->status, ['pending', 'confirmed'], true);
        })->values();

        $pastBookings = $bookings->filter(function (ServiceBooking $booking) {
            return in_array($booking->status, ['completed', 'cancelled'], true);
        })->values();

        $recentBookings = $bookings->take(5);

        $bookedScheduleIds = $bookings
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('class_schedule_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $calendarMonth = $this->resolveCalendarMonth($request, $bookings);
        $bookingDates = $this->groupBookingsByCalendarDate($bookings);
        $availableByDate = $this->availableSchedulesForMonth($calendarMonth, $bookedScheduleIds);
        $openClassesToBook = $this->upcomingOpenSchedules($bookedScheduleIds, 12);

        $gridStart = $calendarMonth->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        $calendarWeeks = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $ds = $cursor->format('Y-m-d');
                $week[] = [
                    'day' => $cursor->day,
                    'dateStr' => $ds,
                    'inMonth' => $cursor->month === $calendarMonth->month,
                    'bookings' => $bookingDates->get($ds, collect()),
                    'available' => $availableByDate->get($ds, collect()),
                ];
                $cursor->addDay();
            }
            $calendarWeeks[] = $week;
        }

        $calendarTitle = $calendarMonth->format('F Y');
        $calendarPrevMonth = $calendarMonth->copy()->subMonth()->format('Y-m');
        $calendarNextMonth = $calendarMonth->copy()->addMonth()->format('Y-m');

        $deliveryCounts = collect(Service::deliveryFormats())
            ->mapWithKeys(fn (string $format) => [
                $format => PublicTrainingServiceQuery::apply(
                    Service::query()->where('is_active', true)
                )->ofDelivery($format)->count(),
            ]);

        $guardForms = \App\Models\GuardTrainingForm::query()
            ->where('student_id', $student->id)
            ->where('status', \App\Models\GuardTrainingForm::STATUS_PUBLISHED)
            ->with('service:id,title')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'bookings',
            'upcomingBookings',
            'pastBookings',
            'recentBookings',
            'openClassesToBook',
            'calendarWeeks',
            'calendarTitle',
            'calendarPrevMonth',
            'calendarNextMonth',
            'deliveryCounts',
            'guardForms'
        ));
    }

    private function bookingCalendarDate(ServiceBooking $booking): ?Carbon
    {
        if ($booking->classSchedule?->class_date) {
            return $booking->classSchedule->class_date->copy()->startOfDay();
        }

        if ($booking->booking_date) {
            return $booking->booking_date->copy()->startOfDay();
        }

        return null;
    }

    /**
     * @param  Collection<int, ServiceBooking>  $bookings
     * @return Collection<string, Collection<int, ServiceBooking>>
     */
    private function groupBookingsByCalendarDate(Collection $bookings): Collection
    {
        return $bookings
            ->filter(function (ServiceBooking $booking) {
                if (in_array($booking->status, ['cancelled'], true)) {
                    return false;
                }

                return $this->bookingCalendarDate($booking) !== null;
            })
            ->groupBy(fn (ServiceBooking $booking) => $this->bookingCalendarDate($booking)->format('Y-m-d'));
    }

    /**
     * @param  Collection<int, int>  $bookedScheduleIds
     * @return Collection<string, Collection<int, ClassSchedule>>
     */
    private function availableSchedulesForMonth(Carbon $month, Collection $bookedScheduleIds): Collection
    {
        $query = $this->openScheduleQuery()
            ->whereBetween('class_date', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
            ]);

        if ($bookedScheduleIds->isNotEmpty()) {
            $query->whereNotIn('id', $bookedScheduleIds->all());
        }

        return $query
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (ClassSchedule $schedule) => $schedule->class_date->format('Y-m-d'));
    }

    /**
     * @param  Collection<int, int>  $bookedScheduleIds
     * @return Collection<int, ClassSchedule>
     */
    private function upcomingOpenSchedules(Collection $bookedScheduleIds, int $limit = 12): Collection
    {
        $query = $this->openScheduleQuery()
            ->where('class_date', '>=', now()->toDateString());

        if ($bookedScheduleIds->isNotEmpty()) {
            $query->whereNotIn('id', $bookedScheduleIds->all());
        }

        return $query
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<ClassSchedule>
     */
    private function openScheduleQuery()
    {
        return ClassSchedule::query()
            ->with('service')
            ->where('status', 'scheduled')
            ->whereHas('service', function ($query): void {
                PublicTrainingServiceQuery::apply(
                    $query->where('is_active', true)
                );
            })
            ->where(function ($query): void {
                $query->where('admin_override_capacity', true)
                    ->orWhereColumn('current_students', '<', 'max_students');
            });
    }

    /**
     * @param  Collection<int, ServiceBooking>  $bookings
     */
    private function resolveCalendarMonth(Request $request, Collection $bookings): Carbon
    {
        $requested = (string) $request->query('month', '');
        if (preg_match('/^\d{4}-\d{2}$/', $requested) === 1) {
            return Carbon::createFromFormat('Y-m', $requested)->startOfMonth();
        }

        $todayMonth = now()->startOfMonth();
        $hasOpenThisMonth = $this->openScheduleQuery()
            ->whereBetween('class_date', [
                $todayMonth->toDateString(),
                $todayMonth->copy()->endOfMonth()->toDateString(),
            ])
            ->exists();

        if ($hasOpenThisMonth) {
            return $todayMonth;
        }

        $nextOpen = $this->openScheduleQuery()
            ->where('class_date', '>=', now()->toDateString())
            ->orderBy('class_date')
            ->value('class_date');

        if ($nextOpen) {
            return Carbon::parse($nextOpen)->startOfMonth();
        }

        $dated = $bookings
            ->map(fn (ServiceBooking $booking) => $this->bookingCalendarDate($booking))
            ->filter()
            ->sortBy(fn (Carbon $date) => $date->timestamp)
            ->values();

        if ($dated->isEmpty()) {
            return $todayMonth;
        }

        $today = now()->startOfDay();
        $upcoming = $dated->first(fn (Carbon $date) => $date->gte($today));

        return ($upcoming ?? $dated->last())->copy()->startOfMonth();
    }
}
