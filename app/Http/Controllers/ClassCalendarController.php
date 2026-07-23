<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassCalendarController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $monthKeys = ClassSchedule::query()
            ->whereHas('service', fn ($query) => $query->where('is_active', true))
            ->whereIn('status', ['scheduled', 'full'])
            ->orderBy('class_date')
            ->pluck('class_date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m'))
            ->unique()
            ->sort()
            ->values();

        $monthOptions = $monthKeys
            ->mapWithKeys(fn (string $monthKey) => [
                $monthKey => Carbon::createFromFormat('Y-m', $monthKey)->format('F Y'),
            ])
            ->all();

        $requestedMonth = $request->query('month');
        $month = $this->resolveMonth(is_string($requestedMonth) ? $requestedMonth : null);
        $currentMonthKey = $month->format('Y-m');

        // Default landing: if this month has no classes, open the nearest month that does.
        if (! $request->filled('month') && $monthKeys->isNotEmpty() && ! $monthKeys->contains($currentMonthKey)) {
            $upcoming = $monthKeys->first(fn (string $key) => $key >= now()->format('Y-m'));

            return redirect()->route('class-calendar', [
                'month' => $upcoming ?? $monthKeys->last(),
            ]);
        }

        // Only allow months that have scheduled classes in the dropdown navigation.
        if ($monthKeys->isNotEmpty() && ! $monthKeys->contains($currentMonthKey)) {
            $upcoming = $monthKeys->first(fn (string $key) => $key >= $currentMonthKey);

            return redirect()->route('class-calendar', [
                'month' => $upcoming ?? $monthKeys->last(),
            ]);
        }

        if ($monthOptions === []) {
            $monthOptions[$currentMonthKey] = $month->format('F Y');
        }

        $keys = array_keys($monthOptions);
        $currentIndex = array_search($currentMonthKey, $keys, true);
        $hasPrevMonth = $currentIndex !== false && $currentIndex > 0;
        $hasNextMonth = $currentIndex !== false && $currentIndex < count($keys) - 1;
        $prevMonth = $hasPrevMonth ? $keys[$currentIndex - 1] : $currentMonthKey;
        $nextMonth = $hasNextMonth ? $keys[$currentIndex + 1] : $currentMonthKey;

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
            'currentMonth' => $currentMonthKey,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'hasPrevMonth' => $hasPrevMonth,
            'hasNextMonth' => $hasNextMonth,
            'monthOptions' => $monthOptions,
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
