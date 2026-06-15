@extends('layouts.web.master')

@section('content')
    <main class="overflow-hidden">

        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px] py-8">
                    <h2 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        <span class="text-[var(--primary-color)]">Class</span> Calendar
                    </h2>
                    <p class="inner-hero-subtext" data-aos="fade-up" data-aos-delay="200">
                        View scheduled training classes and find a session that fits your schedule.
                    </p>
                </div>
            </div>
        </section>

        <section class="py-16 lg:py-24 bg-gradient-to-b from-white to-[#F8F8F8]">
            <div class="container mx-auto px-4 lg:px-10">

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8" data-aos="fade-up">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-[var(--primary-color)]">Scheduled classes</p>
                        <p class="text-gray-600 mt-1">
                            {{ $upcomingCount }} upcoming {{ Str::plural('class', $upcomingCount) }} on the calendar.
                            Click a class to view details and book.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('class-calendar', ['month' => $prevMonth]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:border-[var(--primary-color)] hover:text-[var(--primary-color)] transition-colors">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        <a href="{{ route('class-calendar') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:border-[var(--primary-color)] hover:text-[var(--primary-color)] transition-colors">
                            Today
                        </a>
                        <span class="px-4 py-2 rounded-lg bg-[var(--primary-color)] text-white font-bold min-w-[10rem] text-center">
                            {{ $calendarTitle }}
                        </span>
                        <a href="{{ route('class-calendar', ['month' => $nextMonth]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:border-[var(--primary-color)] hover:text-[var(--primary-color)] transition-colors">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>

                @if($availableLocations->count() > 1)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-8" data-aos="fade-up">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-sm font-semibold text-gray-700">Filter by location:</span>
                            <button type="button"
                                    onclick="filterCalendarByLocation('all')"
                                    class="location-filter-btn active px-4 py-2 rounded-lg border-2 border-[var(--primary-color)] bg-[var(--primary-color)] text-white font-semibold text-sm"
                                    data-location="all">
                                All locations
                            </button>
                            @foreach($availableLocations as $location)
                                <button type="button"
                                        onclick="filterCalendarByLocation(@json($location))"
                                        class="location-filter-btn px-4 py-2 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold text-sm hover:border-[var(--primary-color)] transition-colors"
                                        data-location="{{ $location }}">
                                    {{ $location }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($calendarWeeks))
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 lg:p-6 mb-10 overflow-x-auto" data-aos="fade-up">
                        <table class="min-w-full border-collapse text-center text-sm">
                            <thead>
                                <tr class="text-gray-500 text-xs uppercase tracking-wide">
                                    <th class="p-2 border border-gray-200 bg-gray-50">Sun</th>
                                    <th class="p-2 border border-gray-200 bg-gray-50">Mon</th>
                                    <th class="p-2 border border-gray-200 bg-gray-50">Tue</th>
                                    <th class="p-2 border border-gray-200 bg-gray-50">Wed</th>
                                    <th class="p-2 border border-gray-200 bg-gray-50">Thu</th>
                                    <th class="p-2 border border-gray-200 bg-gray-50">Fri</th>
                                    <th class="p-2 border border-gray-200 bg-gray-50">Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calendarWeeks as $week)
                                    <tr>
                                        @foreach($week as $cell)
                                            <td class="p-1 border border-gray-200 align-top min-w-[8rem] lg:min-w-[9rem] h-28 lg:h-32 {{ $cell['inMonth'] ? 'bg-white' : 'bg-gray-50 text-gray-400' }}">
                                                <div class="font-semibold mb-1 {{ $cell['isPast'] && $cell['inMonth'] ? 'text-gray-400' : 'text-gray-800' }}">
                                                    {{ $cell['day'] }}
                                                </div>
                                                @foreach($cell['schedules'] as $schedule)
                                                    @php
                                                        $startTime = \Carbon\Carbon::parse($schedule->start_time)->format('g:i A');
                                                        $isFull = $schedule->status === 'full' || $schedule->getAvailableSpots() === 0;
                                                    @endphp
                                                    <a href="{{ route('customer.available-classes', $schedule->service_id) }}"
                                                       class="calendar-schedule-item block text-left text-[11px] lg:text-xs rounded px-1.5 py-1 mb-1 truncate {{ $isFull ? 'bg-red-100 text-red-900' : 'bg-emerald-100 text-emerald-900' }}"
                                                       data-location="{{ $schedule->location ?? 'none' }}"
                                                       title="{{ $schedule->service->title ?? 'Class' }} · {{ $startTime }}">
                                                        <span class="font-semibold">{{ $startTime }}</span>
                                                        {{ Str::limit($schedule->service->title ?? 'Class', 16) }}
                                                    </a>
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($schedules->count() > 0)
                    <div class="space-y-4" id="schedule-list">
                        <h3 class="text-2xl font-bold text-[var(--text-color)] uppercase" style="font-family: var(--font-display);">
                            Classes in {{ $calendarTitle }}
                        </h3>

                        @foreach($schedules as $schedule)
                            @php
                                $startTime = \Carbon\Carbon::parse($schedule->start_time)->format('g:i A');
                                $endTime = $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') : null;
                                $isFull = $schedule->status === 'full' || $schedule->getAvailableSpots() === 0;
                            @endphp
                            <article class="schedule-list-item bg-white rounded-xl shadow-sm border border-gray-100 p-5 lg:p-6 hover:shadow-md transition-shadow"
                                     data-location="{{ $schedule->location ?? 'none' }}"
                                     data-aos="fade-up">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-3 mb-2">
                                            <h4 class="text-xl font-bold text-[var(--text-color)]">
                                                {{ $schedule->service->title }}
                                            </h4>
                                            @if($isFull)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase bg-red-100 text-red-700">Full</span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-100 text-emerald-700">
                                                    {{ $schedule->getAvailableSpots() }} spots left
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600">
                                            <span><i class="fas fa-calendar mr-1 text-[var(--primary-color)]"></i>{{ $schedule->class_date->format('l, M j, Y') }}</span>
                                            <span><i class="fas fa-clock mr-1 text-[var(--primary-color)]"></i>{{ $startTime }}@if($endTime) – {{ $endTime }}@endif</span>
                                            @if($schedule->duration_hours)
                                                <span><i class="fas fa-hourglass-half mr-1 text-[var(--primary-color)]"></i>{{ $schedule->duration_hours }} {{ Str::plural('hour', $schedule->duration_hours) }}</span>
                                            @endif
                                            @if($schedule->location)
                                                <span><i class="fas fa-map-marker-alt mr-1 text-[var(--primary-color)]"></i>{{ $schedule->location }}</span>
                                            @endif
                                            @if($schedule->room)
                                                <span><i class="fas fa-door-open mr-1 text-[var(--primary-color)]"></i>{{ $schedule->room }}</span>
                                            @endif
                                            @if($schedule->instructor)
                                                <span><i class="fas fa-chalkboard-teacher mr-1 text-[var(--primary-color)]"></i>{{ $schedule->instructor }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                                        <a href="{{ route('service.details', $schedule->service_id) }}"
                                           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:border-[var(--primary-color)] hover:text-[var(--primary-color)] transition-colors">
                                            View class
                                        </a>
                                        @if(! $isFull && $schedule->class_date->gte(now()->startOfDay()))
                                            <a href="{{ route('customer.available-classes', $schedule->service_id) }}"
                                               class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-[var(--primary-color)] text-white font-bold hover:opacity-90 transition-opacity">
                                                <i class="fas fa-calendar-plus"></i> Book now
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-white rounded-xl shadow-sm border border-gray-100" data-aos="fade-up">
                        <div class="max-w-md mx-auto">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-[28px] font-bold text-[var(--text-color)] mb-4 uppercase" style="font-family: var(--font-display);">
                                No classes scheduled
                            </h3>
                            <p class="text-gray-600 text-lg mb-8">
                                {{-- There are no scheduled classes for {{ $calendarTitle }}. Try another month or contact us for availability. --}}
                                If you need a class that is not currently scheduled, please contact us so we can arrange training at your preferred location.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('class-calendar', ['month' => $nextMonth]) }}" class="btn primary-button inline-block">View next month</a>
                                <a href="{{ route('contact') }}" class="btn secondary-button inline-block">Contact us</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="ready-section">
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="text-left md:text-center lg:text-left md:mx-auto lg:mx-0">
                    <h2 class="mb-5" data-aos="fade-up">
                        <span class="block text-[18px] md:text-[24px] text-white font-normal">Ready to train?</span>
                        <span class="block text-[30px] md:text-[45px] font-black leading-tight uppercase">
                            <span class="text-[#F6CB42]">BOOK</span> <span class="text-[#FFFFFF]">YOUR CLASS</span>
                        </span>
                    </h2>
                    <p class="text-[16px] md:text-[20px] text-white font-normal mb-8 md:mx-auto lg:mx-0" data-aos="fade-up" data-aos-delay="200">
                        Browse all training programs or pick a date from the calendar above.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-start md:justify-center lg:justify-start" data-aos="fade-up" data-aos-delay="400">
                        <a href="{{ route('services') }}" class="btn primary-button !text-center">All training programs</a>
                        <a href="{{ route('contact') }}" class="btn secondary-button !text-center">Contact us</a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    @if($availableLocations->count() > 1)
        <script>
            function filterCalendarByLocation(location) {
                document.querySelectorAll('.location-filter-btn').forEach(function (btn) {
                    btn.classList.remove('active', 'bg-[var(--primary-color)]', 'text-white', 'border-[var(--primary-color)]');
                    btn.classList.add('border-gray-300', 'text-gray-700');
                });

                const activeBtn = document.querySelector('.location-filter-btn[data-location="' + location + '"]');
                if (activeBtn) {
                    activeBtn.classList.add('active', 'bg-[var(--primary-color)]', 'text-white', 'border-[var(--primary-color)]');
                    activeBtn.classList.remove('border-gray-300', 'text-gray-700');
                }

                document.querySelectorAll('.calendar-schedule-item, .schedule-list-item').forEach(function (item) {
                    const itemLocation = item.getAttribute('data-location') || 'none';
                    item.style.display = (location === 'all' || itemLocation === location) ? '' : 'none';
                });
            }
        </script>
    @endif
@endsection
