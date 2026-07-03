@extends('student.layouts.master')

@section('title', 'Booking Details #' . $booking->id)

@section('content')
<div class="mb-6">
    <a href="{{ route('student.bookings') }}" class="hover:underline inline-flex items-center gap-2 mb-4 transition-colors" style="color: #3AA62C;" onmouseover="this.style.color='#175B0E'" onmouseout="this.style.color='#3AA62C'">
        <i class="fas fa-arrow-left"></i> Back to Bookings
    </a>
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Booking Details</h1>
    <div class="px-4 py-3 rounded mb-6 font-semibold
        @if($booking->status == 'pending') bg-yellow-100 border border-yellow-400 text-yellow-800
        @elseif($booking->status == 'confirmed') bg-green-100 border border-green-400 text-green-700
        @elseif($booking->status == 'completed') bg-blue-100 border border-blue-400 text-blue-700
        @else bg-red-100 border border-red-400 text-red-700
        @endif">
        {{ ucfirst($booking->status) }}
    </div>
</div>

<!-- @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif -->

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Service Information Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Service Information</h2>
            <div class="flex gap-6">
                @if($booking->service->image)
                <div class="flex-shrink-0">
                    <img src="{{ $booking->service->image_url }}" 
                         alt="{{ $booking->service->title }}" 
                         class="w-32 h-32 object-cover rounded-lg">
                </div>
                @endif
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $booking->service->title }}</h3>
                    @if($booking->service->short_description)
                        <p class="text-gray-600 mb-4">{{ $booking->service->short_description }}</p>
                    @endif
                    <div class="flex items-center gap-4 flex-wrap">
                        @if($booking->service->class_type)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-users mr-1"></i>
                                {{ $booking->service->class_type === 'one-on-one' ? 'One-on-One' : 'Group Class' }}
                            </span>
                        @endif
                        @if($booking->service->has_online_parts)
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-globe mr-1"></i> Online Components
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Schedule Information -->
        @if($booking->classSchedule)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Class Schedule</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Class Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->classSchedule->class_date->format('F d, Y') }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->classSchedule->class_date->format('l') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Class Time</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($booking->classSchedule->start_time)->format('h:i A') }}
                        @if($booking->classSchedule->end_time)
                            - {{ \Carbon\Carbon::parse($booking->classSchedule->end_time)->format('h:i A') }}
                        @endif
                    </p>
                    @if($booking->classSchedule->duration_hours)
                        <p class="text-sm text-gray-500">{{ $booking->classSchedule->duration_hours }} {{ Str::plural('hour', $booking->classSchedule->duration_hours) }}</p>
                    @endif
                </div>
                @if($booking->location || $booking->classSchedule->location)
                <div>
                    <p class="text-sm text-gray-500 mb-1">Location</p>
                    <p class="text-lg font-semibold text-[var(--primary-color)]">
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ $booking->location ?? $booking->classSchedule->location }}
                    </p>
                </div>
                @endif
                @if($booking->classSchedule->room)
                <div>
                    <p class="text-sm text-gray-500 mb-1">Room</p>
                    <p class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-door-open mr-2 text-gray-400"></i>{{ $booking->classSchedule->room }}
                    </p>
                </div>
                @endif
                @if($booking->classSchedule->instructor)
                <div>
                    <p class="text-sm text-gray-500 mb-1">Instructor</p>
                    <p class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chalkboard-teacher mr-2 text-gray-400"></i>{{ $booking->classSchedule->instructor }}
                    </p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500 mb-1">Class Capacity</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $booking->classSchedule->current_students }} / {{ $booking->classSchedule->max_students }} students
                    </p>
                    <p class="text-xs text-gray-500">Min: {{ $booking->classSchedule->min_students }} students required</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Booking Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Booking Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Booking ID</p>
                    <p class="text-lg font-semibold text-gray-900">#{{ $booking->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Booking Type</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ ucfirst(str_replace('_', ' ', $booking->booking_type ?? 'group')) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Number of Students</p>
                    <p class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user-graduate mr-2 text-gray-400"></i>{{ $booking->number_of_students ?? 1 }}
                    </p>
                </div>
                @if($booking->group_name)
                <div>
                    <p class="text-sm text-gray-500 mb-1">Group Name</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->group_name }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500 mb-1">Booked On</p>
                    <p class="text-base font-medium text-gray-900">{{ $booking->created_at->format('F d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Last Updated</p>
                    <p class="text-base font-medium text-gray-900">{{ $booking->updated_at->format('F d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($booking->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Additional Notes</h2>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $booking->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Payment Summary Card -->
        <div class="bg-white rounded-lg shadow p-6 top-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Payment Summary</h2>
            
            <div class="space-y-4 mb-6">
                @if($booking->total_amount)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Amount</span>
                    <span class="text-xl font-bold text-gray-900">${{ number_format($booking->total_amount, 2) }}</span>
                </div>
                @endif
                
                @if($booking->deposit_amount)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Deposit Required</span>
                    <span class="text-lg font-semibold text-green-600">${{ number_format($booking->deposit_amount, 2) }}</span>
                </div>
                @endif
                
                @if($booking->remaining_amount)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Remaining Balance</span>
                    <span class="text-lg font-semibold text-red-600">${{ number_format($booking->remaining_amount, 2) }}</span>
                </div>
                @endif
                
                <div class="pt-4 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700 font-semibold">Payment Status</span>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($booking->payment_status == 'pending') bg-red-100 text-red-800
                            @elseif($booking->payment_status == 'deposit_paid') bg-yellow-100 text-yellow-800
                            @elseif($booking->payment_status == 'fully_paid') bg-green-100 text-green-800
                            @elseif($booking->payment_status == 'refunded') bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Actions -->
            @if($booking->payment_status === 'pending')
                <a href="{{ route('student.booking.payment', $booking->id) }}" 
                   class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors mb-3">
                    <i class="fas fa-credit-card mr-2"></i> Pay Deposit
                </a>
            @elseif($booking->payment_status === 'deposit_paid' && $booking->remaining_amount > 0)
                <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-3">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        Deposit paid. Remaining balance can be paid later.
                    </p>
                </div>
            @elseif($booking->payment_status === 'fully_paid')
                <div class="bg-green-50 border border-green-200 rounded p-4">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Fully paid. Thank you!
                    </p>
                </div>
            @endif
        </div>

        <!-- Payment History -->
        @if($booking->payments && $booking->payments->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Payment History</h2>
            <div class="space-y-3">
                @foreach($booking->payments as $payment)
                <div class="border-b pb-3 last:border-b-0 last:pb-0">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</p>
                        </div>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($payment->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->status == 'completed') bg-green-100 text-green-800
                            @elseif($payment->status == 'failed') bg-red-100 text-red-800
                            @elseif($payment->status == 'refunded') bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 space-y-1">
                        @if($payment->payment_method)
                            <p><i class="fas fa-credit-card mr-1"></i>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                        @endif
                        @if($payment->transaction_id)
                            <p><i class="fas fa-receipt mr-1"></i>Transaction: {{ $payment->transaction_id }}</p>
                        @endif
                        <p><i class="fas fa-calendar mr-1"></i>{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('services') }}" 
                   class="block w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                    <i class="fas fa-search mr-2"></i> Browse Services
                </a>
                @if($booking->service)
                    <a href="{{ route('service.details', $booking->service->id) }}" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                        <i class="fas fa-info-circle mr-2"></i> View Service Details
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
