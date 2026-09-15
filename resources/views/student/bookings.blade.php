@extends('student.layouts.master')

@section('title', 'My Bookings')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">My Bookings</h1>
    <p class="text-gray-600 mt-2">View scheduled classes, open online modules, and manage bookings</p>
    <div class="flex gap-2 mt-4">
        <a href="{{ route('student.bookings', ['filter' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ ($filter ?? 'all') === 'all' ? 'bg-green-600 text-white' : 'bg-white border text-gray-700' }}">All</a>
        <a href="{{ route('student.bookings', ['filter' => 'upcoming']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ ($filter ?? '') === 'upcoming' ? 'bg-green-600 text-white' : 'bg-white border text-gray-700' }}">Upcoming</a>
        <a href="{{ route('student.bookings', ['filter' => 'past']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ ($filter ?? '') === 'past' ? 'bg-green-600 text-white' : 'bg-white border text-gray-700' }}">Past</a>
    </div>
</div>

@if($bookings->count() > 0)
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked On</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($bookings as $booking)
                    @php
                        $schedule = $booking->classSchedule;
                        $canStartOnline = $booking->service
                            && $booking->service->has_online_parts
                            && $booking->hasPaidDeposit()
                            && in_array($booking->status, ['pending', 'confirmed'], true);
                    @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $booking->service->title }}</div>
                        @if($booking->service?->has_online_parts)
                            <div class="mt-1 text-xs font-semibold text-cyan-700">Blended · online modules available</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($schedule)
                            <div class="text-sm text-gray-900">{{ $schedule->class_date->format('M j, Y') }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                                @if($schedule->end_time)
                                    – {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                @endif
                            </div>
                            @if($schedule->location)
                                <div class="text-xs text-gray-500 mt-0.5">{{ $schedule->location }}</div>
                            @endif
                        @elseif($booking->booking_date)
                            <div class="text-sm text-gray-900">{{ $booking->booking_date->format('M j, Y') }}</div>
                            @if($booking->booking_time)
                                <div class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</div>
                            @endif
                        @else
                            <span class="text-sm text-gray-400">Date TBD</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex flex-col gap-1.5 items-start">
                            <a href="{{ route('student.bookings.show', $booking->id) }}" class="font-semibold text-[var(--brand)] hover:text-[var(--brand-dark)]">
                                View Details
                            </a>
                            @if($canStartOnline)
                                <a href="{{ route('student.online-course.index', $booking->service) }}" class="font-semibold text-cyan-700 hover:text-cyan-900">
                                    Start / continue course
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(method_exists($bookings, 'links'))
    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@else
<div class="bg-white rounded-lg shadow p-12 text-center">
    <div class="max-w-md mx-auto">
        <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">
            @if(($filter ?? 'all') === 'upcoming')
                No upcoming classes
            @elseif(($filter ?? '') === 'past')
                No past bookings
            @else
                No Bookings Found
            @endif
        </h3>
        <p class="text-gray-600 mb-6">
            @if(($filter ?? 'all') === 'upcoming')
                Active bookings (pending or confirmed) will appear here so you can open the class and take quizzes.
            @elseif(($filter ?? '') === 'past')
                Completed or cancelled bookings will appear here.
            @else
                You haven't booked any classes yet. Start by exploring our training classes.
            @endif
        </p>
        <a href="{{ route('training-classes') }}" class="inline-block text-white font-bold py-3 px-6 rounded transition-colors shadow-md" style="background-color: #3AA62C;" onmouseover="this.style.backgroundColor='#175B0E'" onmouseout="this.style.backgroundColor='#3AA62C'">
            Browse Classes
        </a>
    </div>
</div>
@endif

@if(($filter ?? 'all') !== 'past' && isset($openClassesToBook) && $openClassesToBook->count() > 0)
<div class="mt-8 rounded-xl border border-cyan-100 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Available classes to book</h2>
            <p class="mt-0.5 text-sm text-gray-500">Sessions that still have open seats — book here even if you have not enrolled yet.</p>
        </div>
        <a href="{{ route('class-calendar') }}" class="text-sm font-semibold text-[var(--brand)] hover:text-[var(--brand-dark)]">
            Full class calendar <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="space-y-3">
        @foreach($openClassesToBook as $schedule)
            <div class="flex flex-col gap-3 rounded-xl border border-cyan-100 bg-cyan-50/40 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <h3 class="font-semibold text-gray-900">{{ $schedule->service->title ?? 'Class' }}</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $schedule->class_date->format('M j, Y') }}
                        · {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                        @if($schedule->location)
                            · {{ $schedule->location }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('student.available-classes', $schedule->service_id) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--brand)] px-3 py-2 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                    Book this class
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
