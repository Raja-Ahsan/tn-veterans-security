@extends('admin.layouts.master')

@section('title', 'QuickBooks Payments')
@section('page-title', 'Payments in QuickBooks')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-xl font-semibold">Payments in QuickBooks {{ $environment === 'sandbox' ? '(Sandbox)' : '(Production)' }}</h3>
    <a href="{{ route('admin.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to Payments
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
@endif

@if(!$success)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <p class="text-yellow-800"><strong>Could not load QuickBooks payments.</strong></p>
        <p class="text-yellow-700 mt-2">{{ $message }}</p>
        <p class="text-sm text-gray-600 mt-4">Make sure QuickBooks is connected in Site Settings and you have synced at least one payment. In sandbox, test payments appear here after you sync them from your app (e.g. after a customer pays the deposit via QuickBooks Payments and the payment is synced to QuickBooks).</p>
    </div>
@else
    <p class="text-gray-600 mb-4">{{ $message }}</p>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if(count($payments) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QB Payment ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $p)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $p['id'] ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $p['customer_name'] ?? 'Customer #' . ($p['customer_ref'] ?? '—') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">${{ number_format((float) ($p['total_amt'] ?? 0), 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p['txn_date'] ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $p['payment_method'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center">
                <i class="fab fa-quickbooks text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">No payments found in QuickBooks.</p>
                <p class="text-sm text-gray-400 mt-2">Sync payments from your app (Payments → Sync to QuickBooks) or create test transactions in the QuickBooks {{ $environment === 'sandbox' ? 'Sandbox' : '' }} company.</p>
            </div>
        @endif
    </div>

    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-gray-700">
        <strong class="text-blue-800">Where do transactions show in Intuit?</strong>
        <ul class="mt-2 list-disc list-inside space-y-1">
            <li><strong>In the company (Invoices &amp; Payments):</strong> developer.intuit.com → <strong>My Hub</strong> → <strong>Sandboxes</strong> → click your sandbox company → then <strong>Sales</strong> → <strong>Invoices</strong> or <strong>Payments</strong>. Transactions appear here only after sync from this app (Admin → Payments → Sync to QuickBooks).</li>
            <li><strong>Card charges (Payments API):</strong> The $20 card charge goes through QuickBooks Payments; it may appear in your app’s Payments list or in the Intuit merchant/Payments dashboard, not always as a line in the company’s Sales list until the payment is synced.</li>
        </ul>
        <p class="mt-3 text-gray-600">If you don’t see anything: ensure the payment is <strong>Synced</strong> (Admin → Payments → open payment → Sync to QuickBooks), then refresh the sandbox company’s Sales → Payments.</p>
    </div>
@endif
@endsection
