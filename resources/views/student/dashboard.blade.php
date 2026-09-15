@extends('student.layouts.master')

@php
    use Illuminate\Support\Str;
    $totalBookings = $bookings->count();
    $pendingCount = $bookings->where('status', 'pending')->count();
    $confirmedCount = $bookings->where('status', 'confirmed')->count();
    $hasBookings = $totalBookings > 0;
@endphp

@section('title', 'Dashboard')

@section('content')
{{-- Welcome --}}
<div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#1a2332] via-[#1f2d3d] to-[#175B0E] p-6 text-white shadow-sm sm:p-8">
    <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-white/70">Student Dashboard</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                Welcome back, {{ $student->name }}
            </h1>
            <p class="mt-2 max-w-xl text-sm text-white/75 sm:text-base">
                Track your classes, payments, and training progress in one place.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('training-classes') }}" class="inline-flex items-center gap-2 rounded-lg bg-[var(--brand)] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--brand-dark)]">
                <i class="fas fa-search text-xs"></i>
                Browse Classes
            </a>
            <a href="{{ route('student.bookings') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15">
                <i class="fas fa-calendar-check text-xs"></i>
                My Bookings
            </a>
            <a href="{{ route('student.online-courses.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/15">
                <i class="fas fa-laptop text-xs"></i>
                Online Courses
            </a>
        </div>
    </div>
</div>

{{-- Class formats --}}
@php
    $formatOptions = [
        'blended' => [
            'label' => 'Blended',
            'blurb' => 'Online modules and quizzes plus in-person training and testing.',
            'icon' => 'fa-layer-group',
            'iconWrap' => 'bg-cyan-50 text-cyan-700',
            'hover' => 'hover:border-cyan-300 hover:bg-cyan-50/50',
        ],
        'in-person' => [
            'label' => 'In Person',
            'blurb' => 'Classroom instruction with an instructor on site.',
            'icon' => 'fa-chalkboard-user',
            'iconWrap' => 'bg-emerald-50 text-[var(--brand)]',
            'hover' => 'hover:border-emerald-300 hover:bg-emerald-50/50',
        ],
    ];
@endphp
<div class="mb-8">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-gray-900">Choose a class format</h2>
        <p class="mt-0.5 text-sm text-gray-500">Browse Blended or In Person training.</p>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach($formatOptions as $format => $option)
            @php $count = (int) ($deliveryCounts[$format] ?? 0); @endphp
            <a href="{{ route('training-classes', ['delivery' => $format]) }}"
               class="group rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm transition {{ $option['hover'] }}">
                <div class="flex items-start justify-between gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl {{ $option['iconWrap'] }}">
                        <i class="fas {{ $option['icon'] }}"></i>
                    </span>
                    <i class="fas fa-arrow-right text-xs text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-gray-500"></i>
                </div>
                <h3 class="mt-4 text-lg font-bold text-gray-900">{{ $option['label'] }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $option['blurb'] }}</p>
                <p class="mt-3 text-xs font-semibold uppercase tracking-wide {{ $count > 0 ? 'text-[var(--brand)]' : 'text-gray-400' }}">
                    {{ $count }} {{ $count === 1 ? 'class' : 'classes' }}
                </p>
            </a>
        @endforeach
    </div>
</div>

{{-- Stats --}}
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $totalBookings }}</p>
            </div>
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                <i class="fas fa-calendar-check"></i>
            </span>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $pendingCount }}</p>
            </div>
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <i class="fas fa-clock"></i>
            </span>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-gray-500">Confirmed</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $confirmedCount }}</p>
            </div>
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-[var(--brand)]">
                <i class="fas fa-check-circle"></i>
            </span>
        </div>
    </div>
</div>

@php
    $openClassesToBook = $openClassesToBook ?? collect();
@endphp

{{-- Available (not yet booked) class sessions --}}
@if($openClassesToBook->count() > 0)
<div class="mb-8 rounded-xl border border-cyan-100 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Available classes to book</h2>
            <p class="mt-0.5 text-sm text-gray-500">Open sessions you have not booked yet. Pick one to reserve your seat.</p>
        </div>
        <a href="{{ route('class-calendar') }}" class="text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-dark)]">
            Full class calendar <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="space-y-3">
        @foreach($openClassesToBook as $schedule)
            <div class="flex flex-col gap-3 rounded-xl border border-cyan-100 bg-cyan-50/40 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="truncate font-semibold text-gray-900">{{ $schedule->service->title ?? 'Class' }}</h3>
                        <span class="shrink-0 rounded-full bg-cyan-100 px-2.5 py-0.5 text-xs font-semibold text-cyan-800">Open</span>
                        @if($schedule->service?->has_online_parts)
                            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">Blended</span>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                        <i class="far fa-calendar mr-1"></i>
                        {{ $schedule->class_date->format('M d, Y') }}
                        · {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                        @if($schedule->end_time)
                            – {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                        @endif
                    </p>
                    @if($schedule->location || $schedule->room)
                        <p class="mt-0.5 text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt mr-1 text-xs"></i>
                            {{ $schedule->location ?: 'Location TBD' }}{{ $schedule->room ? ' · '.$schedule->room : '' }}
                        </p>
                    @endif
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $schedule->getAvailableSpots() === PHP_INT_MAX ? 'Seats available' : $schedule->getAvailableSpots().' seats left' }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('student.available-classes', $schedule->service_id) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--brand)] px-3 py-2 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                        <i class="fas fa-calendar-plus text-xs"></i>
                        Book this class
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Your booked classes --}}
@if($upcomingBookings->count() > 0)
<div class="mb-8 rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Your scheduled classes</h2>
            <p class="mt-0.5 text-sm text-gray-500">Open a class to review the schedule, or start online modules and quizzes when your deposit is paid.</p>
        </div>
        <a href="{{ route('student.bookings') }}" class="text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-dark)]">
            All bookings <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="space-y-3">
        @foreach($upcomingBookings->take(8) as $booking)
            @php
                $schedule = $booking->classSchedule;
                $canStartOnline = $booking->service
                    && $booking->service->has_online_parts
                    && $booking->hasPaidDeposit();
                $needsDepositForOnline = $booking->service
                    && $booking->service->has_online_parts
                    && ! $booking->hasPaidDeposit();
            @endphp
            <div class="flex flex-col gap-3 rounded-xl border border-gray-100 bg-gray-50/80 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="truncate font-semibold text-gray-900">{{ $booking->service->title ?? 'Class' }}</h3>
                        <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold
                            @if($booking->status == 'pending') bg-amber-100 text-amber-800
                            @elseif($booking->status == 'confirmed') bg-emerald-100 text-emerald-800
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                        @if($booking->service?->has_online_parts)
                            <span class="shrink-0 rounded-full bg-cyan-50 px-2.5 py-0.5 text-xs font-semibold text-cyan-800">Blended</span>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                        <i class="far fa-calendar mr-1"></i>
                        @if($schedule?->class_date)
                            {{ $schedule->class_date->format('M d, Y') }}
                        @else
                            {{ $booking->booking_date?->format('M d, Y') ?? 'Date TBD' }}
                        @endif
                        @if($schedule?->start_time)
                            · {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                            @if($schedule->end_time)
                                – {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                            @endif
                        @elseif($booking->booking_time)
                            · {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}
                        @endif
                    </p>
                    @if($schedule?->location || $schedule?->room)
                        <p class="mt-0.5 text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt mr-1 text-xs"></i>
                            {{ $schedule->location ?: 'Location TBD' }}{{ $schedule->room ? ' · '.$schedule->room : '' }}
                        </p>
                    @endif
                    @if($needsDepositForOnline)
                        <p class="mt-1 text-xs font-medium text-amber-700">
                            Pay deposit to unlock online modules &amp; quizzes.
                        </p>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <a href="{{ route('student.bookings.show', $booking->id) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Schedule details
                    </a>
                    @if($canStartOnline)
                        <a href="{{ route('student.online-course.index', $booking->service) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--brand)] px-3 py-2 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                            <i class="fas fa-play text-xs"></i>
                            Start / continue course
                        </a>
                    @elseif($needsDepositForOnline)
                        <a href="{{ route('student.booking.payment', $booking->id) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-100">
                            Pay deposit
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
    {{-- Calendar --}}
    @if(!empty($calendarWeeks))
    <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm xl:col-span-3 sm:p-6">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Class Calendar</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ $calendarTitle }} · green = your booking · blue = open to book</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('student.dashboard', ['month' => $calendarPrevMonth]) }}"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                   aria-label="Previous month">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                <a href="{{ route('student.dashboard', ['month' => $calendarNextMonth]) }}"
                   class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                   aria-label="Next month">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
                <a href="{{ route('class-calendar') }}" class="text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-dark)]">
                    Full calendar <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-center text-sm">
                <thead>
                    <tr>
                        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dayName)
                            <th class="border-b border-gray-100 pb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $dayName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($calendarWeeks as $week)
                    <tr>
                        @foreach($week as $cell)
                        @php
                            $isToday = $cell['dateStr'] === now()->format('Y-m-d');
                            $dayBookings = $cell['bookings'] ?? collect();
                            $dayAvailable = $cell['available'] ?? collect();
                            $hasClass = $dayBookings->isNotEmpty() || $dayAvailable->isNotEmpty();
                        @endphp
                        <td class="align-top p-1.5 {{ $cell['inMonth'] ? '' : 'opacity-40' }}">
                            <div class="min-h-[4.5rem] rounded-lg p-1.5 {{ $hasClass ? 'bg-emerald-50/80 ring-1 ring-emerald-100' : '' }} {{ $isToday ? 'ring-2 ring-[var(--brand)] ring-offset-1' : '' }}">
                                <div class="mb-1 text-right text-xs font-semibold {{ $isToday ? 'text-[var(--brand)]' : 'text-gray-700' }}">
                                    {{ $cell['day'] }}
                                </div>
                                @foreach($dayBookings as $cb)
                                    @php
                                        $chipClass = match ($cb->status) {
                                            'confirmed' => 'bg-[var(--brand)] text-white',
                                            'completed' => 'bg-slate-600 text-white',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            default => 'bg-amber-400 text-amber-950',
                                        };
                                    @endphp
                                    <a
                                        href="{{ route('student.bookings.show', $cb->id) }}"
                                        class="mb-1 block truncate rounded-md px-1.5 py-1 text-left text-[10px] font-semibold leading-tight {{ $chipClass }}"
                                        title="Your booking: {{ $cb->service->title ?? 'Class' }}"
                                    >
                                        {{ Str::limit($cb->service->title ?? 'Class', 14) }}
                                    </a>
                                @endforeach
                                @foreach($dayAvailable as $open)
                                    <a
                                        href="{{ route('student.available-classes', $open->service_id) }}"
                                        class="mb-1 block truncate rounded-md bg-sky-500 px-1.5 py-1 text-left text-[10px] font-semibold leading-tight text-white"
                                        title="Open to book: {{ $open->service->title ?? 'Class' }}"
                                    >
                                        {{ Str::limit($open->service->title ?? 'Class', 14) }}
                                    </a>
                                @endforeach
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Side panels --}}
    <div class="flex flex-col gap-6 xl:col-span-2">
        @if(isset($pastBookings) && $pastBookings->count() > 0)
        <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Past Classes</h2>
            <div class="space-y-2">
                @foreach($pastBookings->take(4) as $booking)
                <a href="{{ route('student.bookings.show', $booking->id) }}" class="flex items-center justify-between gap-3 rounded-lg px-2 py-2.5 transition hover:bg-gray-50">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-gray-900">{{ $booking->service->title }}</p>
                        <p class="text-xs text-gray-500">{{ optional($booking->booking_date)->format('M d, Y') ?? '—' }}</p>
                    </div>
                    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="rounded-xl border border-cyan-100 bg-cyan-50/60 p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-bold text-gray-900">Online modules &amp; quizzes</h2>
            <p class="mt-1 text-sm text-gray-600">For blended classes, finish videos and quizzes here before your in-person session.</p>
            <a href="{{ route('student.online-courses.index') }}"
               class="mt-4 inline-flex items-center gap-2 rounded-lg bg-[var(--brand)] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                <i class="fas fa-laptop text-xs"></i>
                Go to Online Courses
            </a>
        </div>
    </div>
</div>

@if($hasBookings && $recentBookings->count() > 0)
{{-- Recent table --}}
<div class="mt-6 rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="text-lg font-bold text-gray-900">Recent Bookings</h2>
        <a href="{{ route('student.bookings') }}" class="text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-dark)]">
            View all <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100 text-left text-xs font-semibold uppercase tracking-wide text-gray-400">
                    <th class="pb-3 pr-4">Class</th>
                    <th class="pb-3 pr-4">Date</th>
                    <th class="pb-3 pr-4">Status</th>
                    <th class="pb-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentBookings as $booking)
                <tr class="text-sm">
                    <td class="py-3.5 pr-4 font-medium text-gray-900">{{ $booking->service->title }}</td>
                    <td class="py-3.5 pr-4 text-gray-500">{{ $booking->booking_date?->format('M d, Y') ?? '—' }}</td>
                    <td class="py-3.5 pr-4">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                            @if($booking->status == 'pending') bg-amber-100 text-amber-800
                            @elseif($booking->status == 'confirmed') bg-emerald-100 text-emerald-800
                            @elseif($booking->status == 'completed') bg-slate-100 text-slate-700
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="py-3.5 text-right">
                        <a href="{{ route('student.bookings.show', $booking->id) }}" class="font-semibold text-[var(--brand)] hover:text-[var(--brand-dark)]">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif(! $hasBookings && $openClassesToBook->isEmpty())
{{-- Empty state --}}
<div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center shadow-sm sm:px-10">
    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-[var(--brand)]">
        <i class="fas fa-calendar-plus text-2xl"></i>
    </div>
    <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">No open class sessions yet</h2>
    <p class="mx-auto mt-2 max-w-md text-sm text-gray-500 sm:text-base">
        When admin publishes class schedules, they will appear here and on the calendar so you can book.
    </p>
    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('training-classes') }}" class="inline-flex items-center gap-2 rounded-lg bg-[var(--brand)] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[var(--brand-dark)]">
            <i class="fas fa-search text-xs"></i>
            Browse Training Classes
        </a>
        <a href="{{ route('class-calendar') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
            <i class="far fa-calendar text-xs"></i>
            View Class Calendar
        </a>
    </div>
</div>
@endif
@endsection
