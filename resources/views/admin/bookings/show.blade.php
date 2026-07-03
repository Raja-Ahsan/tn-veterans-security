@extends('admin.layouts.master')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:underline inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left"></i> Back to Bookings
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Information -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Booking Details Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Booking Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Booking ID</label>
                    <p class="text-lg font-semibold text-gray-900">#{{ $booking->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Booking Date</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->created_at->format('F d, Y h:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    @if($booking->status === 'pending')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($booking->status === 'confirmed')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Confirmed</span>
                    @elseif($booking->status === 'completed')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                    @elseif($booking->status === 'cancelled')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Booking Type</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->booking_type === 'one-on-one' ? 'One-on-One' : 'Group' }}</p>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Student Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Name</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->student->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <p class="text-base text-gray-900">{{ $booking->student->email }}</p>
                </div>
                @if($booking->student->phone)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                    <p class="text-base text-gray-900">{{ $booking->student->phone }}</p>
                </div>
                @endif
                @if($booking->student->address)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Address</label>
                    <p class="text-base text-gray-900">{{ $booking->student->address }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Service & Schedule Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Service & Schedule</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Service</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->service->title }}</p>
                </div>
                @if($booking->classSchedule)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Class Date</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->classSchedule->class_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Class Time</label>
                    <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($booking->classSchedule->start_time)->format('h:i A') }}</p>
                    @if($booking->classSchedule->end_time)
                        <p class="text-sm text-gray-500">to {{ \Carbon\Carbon::parse($booking->classSchedule->end_time)->format('h:i A') }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Duration</label>
                    <p class="text-base text-gray-900">{{ $booking->classSchedule->duration_hours }} {{ Str::plural('hour', $booking->classSchedule->duration_hours) }}</p>
                </div>
                @if($booking->location || ($booking->classSchedule && $booking->classSchedule->location))
                <div>
                    <label class="block text-sm font-medium text-gray-500">Location</label>
                    <p class="text-base font-semibold text-[var(--primary-color)]">
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $booking->location ?? $booking->classSchedule->location }}
                    </p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-500">Room</label>
                    <p class="text-base text-gray-900">{{ $booking->classSchedule->room ?? 'TBD' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Instructor</label>
                    <p class="text-base text-gray-900">{{ $booking->classSchedule->instructor ?? 'TBD' }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-500">Number of Students</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->number_of_students ?? 1 }}</p>
                </div>
                @if($booking->group_name)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Group Name</label>
                    <p class="text-base text-gray-900">{{ $booking->group_name }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($booking->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Notes</h3>
            <p class="text-gray-700">{{ $booking->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Payment Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Payment Information</h3>
            <div class="space-y-3">
                @if($booking->total_amount)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Total Amount</label>
                    <p class="text-xl font-bold text-gray-900">${{ number_format($booking->total_amount, 2) }}</p>
                </div>
                @endif
                @if($booking->deposit_amount)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Deposit Amount</label>
                    <p class="text-lg font-semibold text-gray-900">${{ number_format($booking->deposit_amount, 2) }}</p>
                </div>
                @endif
                @if($booking->remaining_amount)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Remaining Amount</label>
                    <p class="text-lg font-semibold text-gray-900">${{ number_format($booking->remaining_amount, 2) }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Status</label>
                    @if($booking->payment_status === 'pending')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($booking->payment_status === 'deposit_paid')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Deposit Paid</span>
                    @elseif($booking->payment_status === 'fully_paid')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Fully Paid</span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">N/A</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Update Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Update Status</h3>
            <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}">
                @csrf
                @method('PUT')
                <select name="status" class="w-full border rounded px-3 py-2 mb-3" required>
                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Payments History -->
        @if($booking->payments && $booking->payments->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Payment History</h3>
            <div class="space-y-3">
                @foreach($booking->payments as $payment)
                    <div class="border-b pb-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-900">${{ number_format($payment->amount, 2) }}</span>
                            <span class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ ucfirst($payment->payment_type) }} - {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                        </div>
                        <div class="mt-1">
                            @if($payment->status === 'completed')
                                <span class="text-xs text-green-600">✓ Completed</span>
                            @elseif($payment->status === 'pending')
                                <span class="text-xs text-yellow-600">Pending</span>
                            @elseif($payment->status === 'failed')
                                <span class="text-xs text-red-600">Failed</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
