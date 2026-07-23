@extends('student.layouts.master')

@section('title', 'Available Classes - ' . $service->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('training-classes.show', $service->id) }}" class="text-blue-600 hover:underline inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left"></i> Back to Class Details
    </a>
</div>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $service->title }}</h1>
    @if($service->short_description)
        <p class="text-gray-600">{{ $service->short_description }}</p>
    @endif
</div>

<!-- Pricing Info Card -->
@if($service->price || $service->deposit_amount)
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($service->price)
            <div>
                <span class="text-sm text-gray-600">Full Price:</span>
                <span class="text-xl font-bold text-gray-900 ml-2">${{ number_format($service->price, 2) }}</span>
                <span class="text-sm text-gray-500">per student</span>
            </div>
        @endif
        @if($service->deposit_amount)
            <div>
                <span class="text-sm text-gray-600">Deposit Required:</span>
                <span class="text-xl font-bold text-green-600 ml-2">${{ number_format($service->deposit_amount, 2) }}</span>
                <span class="text-sm text-gray-500">per student</span>
            </div>
        @endif
    </div>
</div>
@endif

<!-- Location Filter -->
@php
    $availableLocations = $schedules->pluck('location')->filter()->unique()->values();
@endphp
@if($availableLocations->count() > 1)
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex items-center gap-4 flex-wrap">
            <span class="text-sm font-semibold text-gray-700">Filter by Location:</span>
            <button onclick="filterByLocation('all')" class="location-filter-btn active px-4 py-2 rounded-lg border-2 border-[var(--primary-color)] bg-[var(--primary-color)] text-white font-semibold" data-location="all">
                All Locations
            </button>
            @foreach($availableLocations as $loc)
                <button onclick="filterByLocation('{{ $loc }}')" class="location-filter-btn px-4 py-2 rounded-lg border-2 border-gray-300 text-gray-700 font-semibold hover:border-[var(--primary-color)] transition-colors" data-location="{{ $loc }}">
                    {{ $loc }}
                </button>
            @endforeach
        </div>
    </div>
@endif

<!-- Available Classes -->
@if($schedules->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-xl font-bold text-gray-800">Available Class Schedules</h2>
            <p class="text-sm text-gray-600 mt-1">Select a class schedule to book</p>
        </div>
        
        <div class="divide-y divide-gray-200" id="schedules-container">
            @foreach($schedules as $schedule)
                <div class="schedule-item p-6 hover:bg-gray-50 transition-colors" data-location="{{ $schedule->location ?? 'none' }}">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Date</div>
                                    <div class="text-lg font-bold text-gray-900">{{ $schedule->class_date->format('F d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $schedule->class_date->format('l') }}</div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Time</div>
                                    <div class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</div>
                                    @if($schedule->end_time)
                                        <div class="text-sm text-gray-500">to {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</div>
                                    @endif
                                </div>
                                @if($schedule->duration_hours)
                                <div>
                                    <div class="text-sm font-medium text-gray-500">Duration</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ $schedule->duration_hours }} {{ Str::plural('hour', $schedule->duration_hours) }}</div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-6 flex-wrap mt-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-users text-gray-400"></i>
                                    <span class="text-sm text-gray-600">
                                        <span class="font-semibold text-gray-900">{{ $schedule->current_students }}</span> / {{ $schedule->max_students }} enrolled
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-check text-green-500"></i>
                                    <span class="text-sm text-green-600 font-semibold">
                                        {{ $schedule->getAvailableSpots() }} spot(s) available
                                    </span>
                                </div>
                                @if($schedule->min_students)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                    <span class="text-sm text-gray-600">Min: {{ $schedule->min_students }} students</span>
                                </div>
                                @endif
                                @if($schedule->location)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-[var(--primary-color)]"></i>
                                    <span class="text-sm font-semibold text-[var(--primary-color)]">{{ $schedule->location }}</span>
                                </div>
                                @endif
                                @if($schedule->room)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-door-open text-gray-400"></i>
                                    <span class="text-sm text-gray-600">Room: {{ $schedule->room }}</span>
                                </div>
                                @endif
                                @if($schedule->instructor)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-chalkboard-teacher text-gray-400"></i>
                                    <span class="text-sm text-gray-600">{{ $schedule->instructor }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2">
                            <a href="{{ route('student.booking.create.schedule', ['serviceId' => $service->id, 'scheduleId' => $schedule->id]) }}" 
                               class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors inline-flex items-center gap-2">
                                <i class="fas fa-calendar-plus"></i> Book This Class
                            </a>
                            @if($schedule->getAvailableSpots() <= 3)
                                <span class="text-xs text-orange-600 font-semibold">
                                    <i class="fas fa-exclamation-triangle"></i> Limited spots remaining
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <i class="fas fa-calendar-times text-5xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-800 mb-2">No Available Classes</h3>
        <p class="text-gray-600 mb-6">There are no open seats at this time. You may join a waitlist below if classes are full.</p>
        <a href="{{ route('training-classes.show', $service->id) }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
            Back to Class Details
        </a>
    </div>
@endif

@if(isset($fullSchedules) && $fullSchedules->count() > 0)
<div class="mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Full Classes — Join Waitlist</h2>
    <div class="space-y-4">
        @foreach($fullSchedules as $schedule)
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $schedule->class_date->format('l, M d, Y') }} at {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}</p>
                        <p class="text-sm text-gray-600">{{ $schedule->location_name ?? $schedule->location ?? 'Location TBD' }}</p>
                        <span class="text-xs text-orange-600 font-semibold">Class Full</span>
                    </div>
                    @auth('student')
                        <form method="POST" action="{{ route('student.waitlist.store', $schedule) }}" class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" name="number_of_students" value="1">
                            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg">Join Waitlist</button>
                        </form>
                    @else
                        <a href="{{ route('student.login') }}" class="text-blue-600 hover:underline">Log in to join waitlist</a>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('error') }}
    </div>
@endif

@if($availableLocations->count() > 1)
<script>
function filterByLocation(location) {
    // Update button states
    document.querySelectorAll('.location-filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-[var(--primary-color)]', 'text-white', 'border-[var(--primary-color)]');
        btn.classList.add('border-gray-300', 'text-gray-700');
    });
    
    const activeBtn = document.querySelector(`[data-location="${location}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active', 'bg-[var(--primary-color)]', 'text-white', 'border-[var(--primary-color)]');
        activeBtn.classList.remove('border-gray-300', 'text-gray-700');
    }
    
    // Filter schedule items
    const scheduleItems = document.querySelectorAll('.schedule-item');
    scheduleItems.forEach(item => {
        const itemLocation = item.getAttribute('data-location') || 'none';
        if (location === 'all' || itemLocation === location) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endif
@endsection
