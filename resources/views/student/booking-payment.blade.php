@extends('student.layouts.master')

@section('title', 'Payment - Booking #' . $booking->id)

@section('content')
@php $depositAmount = (float) $booking->deposit_amount; @endphp
<div class="mb-6">
    <a href="{{ route('student.bookings.show', $booking->id) }}" class="text-blue-600 hover:underline inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left"></i> Back to Booking Details
    </a>
</div>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Complete Deposit Payment</h1>
    <p class="text-gray-600">Booking #{{ $booking->id }} - {{ $booking->service->title }}</p>
</div>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Payment Details</h2>
            
            <div>
                <!-- Payment Amount -->
                <div class="bg-gray-50 border border-gray-300 rounded-lg p-6 mb-6">
                    <div class="text-center">
                        <div class="text-sm text-gray-600 mb-2">Deposit Amount</div>
                        <div class="text-4xl font-bold text-gray-900">${{ number_format($depositAmount, 2) }}</div>
                        <div class="text-sm text-gray-500 mt-2">
                            Total: ${{ number_format($booking->total_amount, 2) }} | 
                            Remaining: ${{ number_format($booking->remaining_amount, 2) }}
                        </div>
                    </div>
                </div>
                
                @if(!empty($qbPaymentsEnabled) && $qbPaymentsEnabled)
                <!-- QuickBooks Payment: Card form (server-side tokenization) -->
                <form method="POST" action="{{ route('student.booking.payment.quickbooks', $booking->id) }}" id="qb-payment-form">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Card Details</label>
                        <div class="space-y-3">
                            <input type="text" name="card_number" placeholder="Card number" maxlength="19" 
                                   value="{{ old('card_number') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   autocomplete="cc-number" inputmode="numeric" pattern="[0-9\s]*">
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="exp_month" placeholder="MM" maxlength="2" 
                                       value="{{ old('exp_month') }}"
                                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                                       autocomplete="cc-exp-month" inputmode="numeric">
                                <input type="text" name="exp_year" placeholder="YY" maxlength="4" 
                                       value="{{ old('exp_year') }}"
                                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                                       autocomplete="cc-exp-year" inputmode="numeric">
                            </div>
                            <input type="text" name="cvc" placeholder="CVC" maxlength="4" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                                   autocomplete="cc-csc" inputmode="numeric">
                        </div>
                        @error('card_number')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        @error('exp_month')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        @error('exp_year')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        @error('cvc')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Booking Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service:</span>
                                <span class="font-semibold text-gray-900">{{ $booking->service->title }}</span>
                            </div>
                            @if($booking->classSchedule)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Class Date:</span>
                                <span class="font-semibold text-gray-900">{{ $booking->classSchedule->class_date->format('F d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Class Time:</span>
                                <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->classSchedule->start_time)->format('h:i A') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Number of Students:</span>
                                <span class="font-semibold text-gray-900">{{ $booking->number_of_students ?? 1 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" id="qb-pay-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-lock mr-2"></i> Pay ${{ number_format($depositAmount, 2) }} Deposit (QuickBooks)
                    </button>
                </form>
                @else
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-amber-800 text-sm">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        QuickBooks Payments is not configured. Please connect QuickBooks in Site Settings.
                    </p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Booking Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Service:</span>
                            <span class="font-semibold text-gray-900">{{ $booking->service->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Number of Students:</span>
                            <span class="font-semibold text-gray-900">{{ $booking->number_of_students ?? 1 }}</span>
                        </div>
                    </div>
                </div>
                <button type="button" disabled class="w-full bg-gray-400 text-gray-200 font-bold py-3 px-6 rounded-lg cursor-not-allowed">
                    <i class="fas fa-lock mr-2"></i> Payment Unavailable
                </button>
                @endif
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Payment Summary</h3>
            <div class="space-y-3 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="font-semibold text-gray-900">${{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Deposit Amount:</span>
                    <span class="font-bold text-green-600">${{ number_format($depositAmount, 2) }}</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between">
                        <span class="text-gray-700 font-semibold">Remaining Balance:</span>
                        <span class="font-bold text-gray-900">${{ number_format($booking->remaining_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded p-4 mb-4">
                <p class="text-xs text-green-800">
                    <i class="fas fa-check-circle mr-1"></i>
                    Your booking will be confirmed once the deposit payment is processed.
                </p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded p-4">
                <p class="text-xs text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Note:</strong> Remaining balance will be collected outside this website.
                </p>
            </div>
        </div>
    </div>
</div>

@if(!empty($qbPaymentsEnabled) && $qbPaymentsEnabled)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('qb-payment-form');
    const payBtn = document.getElementById('qb-pay-btn');
    if (form && payBtn) {
        form.addEventListener('submit', function() {
            payBtn.disabled = true;
            payBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
        });
    }
});
</script>
@endif
@endsection
