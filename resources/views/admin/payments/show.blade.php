@extends('admin.layouts.master')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.payments.index') }}" class="text-blue-600 hover:underline inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left"></i> Back to Payments
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Information -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Payment Details Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Payment Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment ID</label>
                    <p class="text-lg font-semibold text-gray-900">#{{ $payment->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Date</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $payment->payment_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Amount</label>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Type</label>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Method</label>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    @if($payment->status === 'pending')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($payment->status === 'completed')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                    @elseif($payment->status === 'failed')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                    @elseif($payment->status === 'refunded')
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Refunded</span>
                    @endif
                </div>
                @if($payment->transaction_id)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Transaction ID</label>
                    <p class="text-sm font-mono text-gray-900">{{ $payment->transaction_id }}</p>
                </div>
                @endif
                @if($payment->payment_gateway)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Gateway</label>
                    <p class="text-sm text-gray-900">{{ ucfirst($payment->payment_gateway) }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Student Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Name</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $payment->student->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <p class="text-base text-gray-900">{{ $payment->student->email }}</p>
                </div>
                @if($payment->student->phone)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                    <p class="text-base text-gray-900">{{ $payment->student->phone }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Booking Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Booking Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Class</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $payment->booking->service->title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Booking ID</label>
                    <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="text-blue-600 hover:underline">
                        #{{ $payment->booking_id }}
                    </a>
                </div>
                @if($payment->booking->classSchedule)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Class Date</label>
                    <p class="text-base text-gray-900">{{ $payment->booking->classSchedule->class_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Class Time</label>
                    <p class="text-base text-gray-900">{{ \Carbon\Carbon::parse($payment->booking->classSchedule->start_time)->format('h:i A') }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($payment->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Notes</h3>
            <p class="text-gray-700">{{ $payment->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- QuickBooks Sync Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">QuickBooks Sync</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Sync Status</label>
                    @if($payment->synced_to_quickbooks)
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i> Synced
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            <i class="fas fa-times mr-1"></i> Not Synced
                        </span>
                    @endif
                </div>
                @if($payment->quickbooks_invoice_id)
                <div>
                    <label class="block text-sm font-medium text-gray-500">QuickBooks Invoice ID</label>
                    <p class="text-sm text-gray-900">{{ $payment->quickbooks_invoice_id }}</p>
                </div>
                @endif
                @if($payment->quickbooks_payment_id)
                <div>
                    <label class="block text-sm font-medium text-gray-500">QuickBooks Payment ID</label>
                    <p class="text-sm text-gray-900">{{ $payment->quickbooks_payment_id }}</p>
                </div>
                @endif
                @if($payment->quickbooks_synced_at)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Synced At</label>
                    <p class="text-sm text-gray-900">{{ $payment->quickbooks_synced_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
                @if(!$payment->synced_to_quickbooks && $payment->status === 'completed')
                <div class="pt-3 border-t">
                    <p class="text-sm text-gray-600 mb-2">Transaction will appear in QuickBooks (sandbox) only after sync.</p>
                    <form method="POST" action="{{ route('admin.payments.sync-quickbooks', $payment) }}">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            <i class="fab fa-quickbooks mr-1"></i> Sync to QuickBooks
                        </button>
                    </form>
                </div>
                @endif
                @if($payment->synced_to_quickbooks)
                <p class="mt-3 pt-3 border-t text-xs text-gray-500">To see this in Intuit: developer.intuit.com → My Hub → Sandboxes → your company → Sales → Payments.</p>
                @endif
            </div>
        </div>

        <!-- Bank Sync Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Bank Sync</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Sync Status</label>
                    @if($payment->synced_to_bank)
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i> Synced
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            <i class="fas fa-times mr-1"></i> Not Synced
                        </span>
                    @endif
                </div>
                @if($payment->bank_synced_at)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Synced At</label>
                    <p class="text-sm text-gray-900">{{ $payment->bank_synced_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
                @if(!$payment->synced_to_bank && $payment->status === 'completed')
                <div class="pt-3 border-t">
                    <form method="POST" action="{{ route('admin.payments.sync-bank', $payment) }}">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                            <i class="fas fa-university mr-1"></i> Sync to Bank
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Timestamps -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Timestamps</h3>
            <div class="space-y-2 text-sm">
                <div>
                    <label class="block text-gray-500">Created At</label>
                    <p class="text-gray-900">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <label class="block text-gray-500">Updated At</label>
                    <p class="text-gray-900">{{ $payment->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
