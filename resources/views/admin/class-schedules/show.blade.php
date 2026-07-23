@extends('admin.layouts.master')

@section('title', 'Class Schedule Details')
@section('page-title', 'Class Schedule Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.class-schedules.index') }}" class="text-blue-600 hover:underline inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left"></i> Back to Schedules
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Information -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Schedule Details Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Schedule Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Class</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $classSchedule->service->title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Date</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $classSchedule->class_date->format('l, F d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Start Time</label>
                    <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($classSchedule->start_time)->format('h:i A') }}</p>
                </div>
                @if($classSchedule->end_time)
                <div>
                    <label class="block text-sm font-medium text-gray-500">End Time</label>
                    <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($classSchedule->end_time)->format('h:i A') }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-500">Duration</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $classSchedule->duration_hours }} {{ Str::plural('hour', $classSchedule->duration_hours) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    @if($classSchedule->status === 'scheduled')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                    @elseif($classSchedule->status === 'full')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Full</span>
                    @elseif($classSchedule->status === 'cancelled')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                    @elseif($classSchedule->status === 'completed')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Capacity Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Capacity Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Current Students</label>
                    <p class="text-3xl font-bold text-gray-900">{{ $classSchedule->current_students }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Max Students</label>
                    <p class="text-3xl font-bold text-gray-900">{{ $classSchedule->max_students }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Min Students</label>
                    <p class="text-3xl font-bold text-gray-900">{{ $classSchedule->min_students }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-600 h-4 rounded-full" style="width: {{ min(100, ($classSchedule->current_students / $classSchedule->max_students) * 100) }}%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    {{ $classSchedule->getAvailableSpots() }} spot(s) available
                </p>
            </div>
        </div>

        <!-- Room and Instructor -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Location & Instructor</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Room</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $classSchedule->room ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Location</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $classSchedule->location_name ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Instructor</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $classSchedule->instructor_name ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Can Overlap</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $classSchedule->can_overlap ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </div>

        @if($classSchedule->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Notes</h3>
            <p class="text-gray-700">{{ $classSchedule->notes }}</p>
        </div>
        @endif

        @php
            $travelMin = $classSchedule->service->is_travel_based ? ($classSchedule->service->travel_minimum_students ?? null) : null;
            $belowTravelMin = $travelMin && $classSchedule->current_students < $travelMin;
        @endphp
        @if($belowTravelMin)
        <div class="bg-amber-50 border border-amber-300 rounded-lg shadow p-6" role="alert">
            <h3 class="text-lg font-bold text-amber-900 mb-2">Travel class below minimum</h3>
            <p class="text-sm text-amber-800 mb-4">{{ $classSchedule->current_students }} enrolled · minimum required: {{ $travelMin }}. Notify students, cancel, or reschedule.</p>
            <form method="POST" action="{{ route('admin.class-schedules.travel-notify', $classSchedule) }}" class="mb-3">
                @csrf
                <textarea name="message" rows="2" required class="w-full border rounded px-3 py-2 text-sm mb-2" placeholder="Message to enrolled students…"></textarea>
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded text-sm">Notify enrolled students</button>
            </form>
            <form method="POST" action="{{ route('admin.class-schedules.travel-cancel', $classSchedule) }}" onsubmit="return confirm('Cancel this travel class?');">
                @csrf
                <input type="text" name="reason" required placeholder="Cancellation reason" class="w-full border rounded px-3 py-2 text-sm mb-2">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded text-sm">Cancel class</button>
            </form>
        </div>
        @endif

        @if(isset($enrolledBookings) && $enrolledBookings->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Class Roster</h3>
            <table class="min-w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Student</th><th class="pb-2">Email</th><th class="pb-2">Phone</th><th class="pb-2">Seats</th></tr></thead>
                <tbody>
                    @foreach($enrolledBookings as $booking)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">{{ $booking->student?->name }}</td>
                            <td class="py-2">{{ $booking->student?->email }}</td>
                            <td class="py-2">{{ $booking->student?->phone ?? '—' }}</td>
                            <td class="py-2">{{ $booking->number_of_students }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Actions Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.class-schedules.edit', $classSchedule) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit Schedule
                </a>
                <form method="POST" action="{{ route('admin.class-schedules.destroy', $classSchedule) }}" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
                        <i class="fas fa-trash mr-2"></i> Delete Schedule
                    </button>
                </form>
            </div>
        </div>

        <!-- Bookings Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Bookings</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $classSchedule->bookings->count() }}</p>
            <p class="text-sm text-gray-500 mt-1">Total bookings for this schedule</p>
            @if($classSchedule->bookings->count() > 0)
                <a href="{{ route('admin.bookings.index', ['schedule' => $classSchedule->id]) }}" class="mt-4 inline-block text-blue-600 hover:underline text-sm">
                    View all bookings →
                </a>
                <a href="{{ route('admin.class-schedules.roster.export', $classSchedule) }}" class="mt-2 block text-green-600 hover:underline text-sm">
                    <i class="fas fa-download mr-1"></i> Export roster (CSV)
                </a>
            @endif
        </div>

        <!-- Notify Enrolled Students -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Notify Enrolled Students</h3>
            <form method="POST" action="{{ route('admin.class-schedules.notify', $classSchedule) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notification Type</label>
                    <select name="notification_type" required class="w-full border rounded px-3 py-2">
                        <option value="class_canceled">Class Canceled</option>
                        <option value="class_rescheduled">Class Rescheduled</option>
                        <option value="class_moved">Class Moved</option>
                        <option value="time_changed">Time Changed</option>
                        <option value="instructor_changed">Instructor Changed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Method</label>
                    <select name="delivery_method" required class="w-full border rounded px-3 py-2">
                        <option value="email">Email</option>
                        <option value="sms">Text (SMS)</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" rows="4" required class="w-full border rounded px-3 py-2" placeholder="Enter the message students will receive..."></textarea>
                </div>
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded font-semibold">
                    <i class="fas fa-paper-plane mr-2"></i> Send Notification
                </button>
            </form>
        </div>

        @if($classSchedule->waitlistEntries->where('status', 'waiting')->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Notify Waitlist ({{ $classSchedule->waitlistEntries->where('status', 'waiting')->count() }})</h3>
            <form method="POST" action="{{ route('admin.class-schedules.notify-waitlist', $classSchedule) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Method</label>
                    <select name="delivery_method" required class="w-full border rounded px-3 py-2">
                        <option value="email">Email</option>
                        <option value="sms">Text (SMS)</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" rows="3" required class="w-full border rounded px-3 py-2" placeholder="A spot may be available..."></textarea>
                </div>
                <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded font-semibold">Notify Waitlist</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
