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
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-6 text-xl font-bold text-gray-800">Payment Details</h2>

            <div class="mb-6 rounded-lg border border-gray-300 bg-gray-50 p-6 text-center">
                <div class="mb-2 text-sm text-gray-600">Deposit Amount</div>
                <div class="text-4xl font-bold text-gray-900">${{ number_format($depositAmount, 2) }}</div>
                <div class="mt-2 text-sm text-gray-500">
                    Total: ${{ number_format($booking->total_amount, 2) }} |
                    Remaining: ${{ number_format($booking->remaining_amount, 2) }}
                </div>
            </div>

            <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
                <h3 class="mb-3 font-semibold text-gray-800">Booking Summary</h3>
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

            @if(!empty($qbPaymentsEnabled) && $qbPaymentsEnabled)
                <form method="POST" action="{{ route('student.booking.payment.quickbooks', $booking->id) }}" id="qb-payment-form">
                    @csrf
                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-bold text-gray-700">Card Details</label>
                        <div class="space-y-3">
                            <input type="text" name="card_number" placeholder="Card number" maxlength="19"
                                   value="{{ old('card_number') }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-green-500 focus:ring-2 focus:ring-green-500"
                                   autocomplete="cc-number" inputmode="numeric" pattern="[0-9\s]*">
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="exp_month" placeholder="MM" maxlength="2"
                                       value="{{ old('exp_month') }}"
                                       class="rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-500"
                                       autocomplete="cc-exp-month" inputmode="numeric">
                                <input type="text" name="exp_year" placeholder="YY" maxlength="4"
                                       value="{{ old('exp_year') }}"
                                       class="rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-500"
                                       autocomplete="cc-exp-year" inputmode="numeric">
                            </div>
                            <input type="text" name="cvc" placeholder="CVC" maxlength="4"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-green-500"
                                   autocomplete="cc-csc" inputmode="numeric">
                        </div>
                        @error('card_number')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        @error('exp_month')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        @error('exp_year')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        @error('cvc')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" id="qb-pay-btn" class="w-full rounded-lg bg-green-600 px-6 py-3 font-bold text-white transition-colors hover:bg-green-700">
                        <i class="fas fa-lock mr-2"></i> Pay ${{ number_format($depositAmount, 2) }} Deposit (QuickBooks)
                    </button>
                </form>
            @else
                <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-950">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Card payment unavailable right now
                    </p>
                    <p class="mt-1 text-sm text-amber-800">
                        {{ $qbPaymentsMessage ?? 'QuickBooks is not connected. An administrator must reconnect it in Site Settings.' }}
                    </p>
                </div>

                @if(!empty($allowManualDeposit))
                    <form method="POST" action="{{ route('student.booking.payment.process', $booking->id) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="payment_method" value="manual">
                        <p class="text-sm text-gray-600">
                            You can still complete the deposit to unlock your enrollment and online quizzes.
                            Admin can sync/reconcile this payment later after reconnecting QuickBooks.
                        </p>
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-6 py-3 font-bold text-white transition hover:bg-emerald-700">
                            <i class="fas fa-check-circle mr-2"></i> Complete Deposit (${{ number_format($depositAmount, 2) }})
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="sticky top-6 rounded-lg bg-white p-6 shadow">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Payment Summary</h3>
            <div class="mb-4 space-y-3">
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
                        <span class="font-semibold text-gray-700">Remaining Balance:</span>
                        <span class="font-bold text-gray-900">${{ number_format($booking->remaining_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="mb-4 rounded border border-green-200 bg-green-50 p-4">
                <p class="text-xs text-green-800">
                    <i class="fas fa-check-circle mr-1"></i>
                    Your booking is confirmed after the deposit is paid. Blended course quizzes unlock after deposit.
                </p>
            </div>
            <div class="rounded border border-blue-200 bg-blue-50 p-4">
                <p class="text-xs text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Note:</strong> Remaining balance is collected outside this checkout.
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
