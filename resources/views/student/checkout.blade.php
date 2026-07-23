@extends('student.layouts.master')

@section('title', 'Checkout - ' . $service->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('training-classes.show', $service->id) }}" class="text-blue-600 hover:underline inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left"></i> Back to Class
    </a>
</div>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Complete your booking</h1>
    <p class="text-gray-600">{{ $service->title }}</p>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Booking summary -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Booking details</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-semibold text-gray-900">{{ $inquiry['name'] ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-semibold text-gray-900">{{ $inquiry['email'] ?? '-' }}</span>
                </div>
                @if(!empty($inquiry['phone']))
                <div class="flex justify-between">
                    <span class="text-gray-600">Phone:</span>
                    <span class="font-semibold text-gray-900">{{ $inquiry['phone'] }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Number of students:</span>
                    <span class="font-semibold text-gray-900">{{ $numStudents }}</span>
                </div>
                @if(!empty($selectedSchedule))
                <div class="flex justify-between items-start gap-4">
                    <span class="text-gray-600 shrink-0">Class session:</span>
                    <span class="font-semibold text-gray-900 text-right">
                        {{ $selectedSchedule->class_date->format('D, M j, Y') }}
                        · {{ \Carbon\Carbon::parse($selectedSchedule->start_time)->format('g:i A') }} 
                    </span>
                </div>
                @endif
                @if(!empty($inquiry['location']) && $inquiry['location'] !== 'Any location')
                <div class="flex justify-between">
                    <span class="text-gray-600">Location:</span>
                    <span class="font-semibold text-gray-900">{{ $inquiry['location'] }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($service->refund_policy)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Refund Policy</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $service->refund_policy }}</p>
        </div>
        @endif

        @if($service->is_travel_based && ($travelFees ?? 0) > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Travel Fees</h2>
            <p class="text-sm text-gray-700">Travel fees of <strong>${{ number_format($travelFees, 2) }}</strong> are included in the total (distance, lodging, and travel time fees).</p>
            @if($service->travel_minimum_students)
                <p class="text-sm text-amber-700 mt-2">This travel class requires at least {{ $service->travel_minimum_students }} student(s) to run.</p>
            @endif
        </div>
        @endif
    </div>

    <!-- Payment step -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Payment</h3>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                <div class="text-sm text-gray-600 mb-1">Amount due</div>
                <div class="text-2xl font-bold text-gray-900">${{ number_format($amountDue, 2) }}</div>
                @if($depositAmount > 0 && $depositAmount < $totalAmount)
                <div class="text-xs text-gray-500 mt-1">Deposit ({{ $numStudents }} student{{ $numStudents > 1 ? 's' : '' }}) · Total ${{ number_format($totalAmount, 2) }}</div>
                @endif
            </div>

            {{-- UPDATE: Policy text displayed before payment --}}
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4 text-sm text-gray-700">
                <div class="font-semibold text-gray-800 mb-2">Policy</div>
                <ul class="list-disc list-inside space-y-1">
                    @if($service->refund_policy)
                        <li>{{ Str::limit($service->refund_policy, 200) }}</li>
                    @else
                        <li>All deposits are non-refundable.</li>
                        <li>If you need to reschedule your class, you must notify us at least 24 hours prior to the scheduled class date.</li>
                    @endif
                </ul>
            </div>

            @if($isLoggedIn)
                <form method="POST" action="{{ route('student.classes.checkout.process', $service->id) }}" id="checkout-form">
                    @csrf
                    {{-- UPDATE: Required policy acknowledgment checkbox - user must check before completing payment --}}
                    <label class="flex items-start gap-3 mb-4 cursor-pointer">
                        <input type="checkbox" name="policy_acknowledged" value="1" required class="mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700">I acknowledge that deposits are non-refundable and agree to the 24-hour rescheduling policy.</span>
                    </label>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i> Proceed to payment
                    </button>
                </form>
                <p class="text-xs text-gray-500 mt-3">Complete payment on the next step. You can set a password later in Profile to log in next time.</p>
            @else
                <p class="text-gray-600 text-sm mb-4">You already have an account with this email. Log in to complete payment.</p>
                <div class="space-y-3">
                    <a href="{{ route('student.login') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                        Log in to pay
                    </a>
                </div>
                <p class="text-xs text-gray-500 mt-3">After logging in you will return here to complete payment.</p>
            @endif
        </div>
    </div>
</div>
@endsection
