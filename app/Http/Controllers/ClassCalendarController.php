<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $month = $this->resolveMonth($request->query('month'));

        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $schedules = ClassSchedule::query()
            ->with('service')
            ->whereHas('service', fn ($query) => $query->where('is_active', true))
            ->whereIn('status', ['scheduled', 'full'])
            ->whereBetween('class_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();

        $schedulesByDate = $schedules->groupBy(fn (ClassSchedule $schedule) => $schedule->class_date->format('Y-m-d'));

        $gridStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);

        $calendarWeeks = [];
        $cursor = $gridStart->copy();

        while ($cursor->lte($gridEnd)) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $dateStr = $cursor->format('Y-m-d');

                $week[] = [
                    'day' => $cursor->day,
                    'dateStr' => $dateStr,
                    'inMonth' => $cursor->month === $month->month && $cursor->year === $month->year,
                    'isPast' => $cursor->lt(now()->startOfDay()),
                    'schedules' => $schedulesByDate->get($dateStr, collect()),
                ];

                $cursor->addDay();
            }

            $calendarWeeks[] = $week;
        }

        $availableLocations = $schedules
            ->pluck('location')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('class-calendar', [
            'calendarWeeks' => $calendarWeeks,
            'calendarTitle' => $month->format('F Y'),
            'currentMonth' => $month->format('Y-m'),
            'prevMonth' => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonth()->format('Y-m'),
            'schedules' => $schedules,
            'availableLocations' => $availableLocations,
            'upcomingCount' => ClassSchedule::query()
                ->whereHas('service', fn ($query) => $query->where('is_active', true))
                ->whereIn('status', ['scheduled', 'full'])
                ->where('class_date', '>=', now()->toDateString())
                ->count(),
        ]);
    }

    private function resolveMonth(?string $month): Carbon
    {
        if ($month === null || $month === '') {
            return now()->startOfMonth();
        }

        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            return now()->startOfMonth();
        }

        try {
            return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            return now()->startOfMonth();
        }
    }
}
