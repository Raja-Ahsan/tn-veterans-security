@extends('admin.layouts.master')

@section('title', 'Payments')
@section('page-title', 'Payments Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-xl font-semibold">All Payments</h3>
    <div class="flex gap-4">
        <a href="{{ route('admin.payments.quickbooks-list') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm" title="View payments in QuickBooks (sandbox/production)">
            <i class="fab fa-quickbooks mr-1"></i> View in QuickBooks
        </a>
        <!-- Bulk Sync Buttons -->
        <form method="POST" action="{{ route('admin.payments.sync-all-quickbooks') }}" class="inline">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm" title="Sync all pending payments to QuickBooks">
                <i class="fab fa-quickbooks mr-1"></i> Sync All to QuickBooks
            </button>
        </form>
        <form method="POST" action="{{ route('admin.payments.sync-all-bank') }}" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm" title="Sync all pending payments to bank">
                <i class="fas fa-university mr-1"></i> Sync All to Bank
            </button>
        </form>
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex gap-2">
            <select name="status" class="border rounded px-3 py-2 text-sm">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
            <select name="payment_type" class="border rounded px-3 py-2 text-sm">
                <option value="">All Types</option>
                <option value="deposit" {{ request('payment_type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                <option value="full_payment" {{ request('payment_type') === 'full_payment' ? 'selected' : '' }}>Full Payment</option>
                <option value="remaining_balance" {{ request('payment_type') === 'remaining_balance' ? 'selected' : '' }}>Remaining Balance</option>
                <option value="refund" {{ request('payment_type') === 'refund' ? 'selected' : '' }}>Refund</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Filter
            </button>
            @if(request()->has('status') || request()->has('payment_type') || request()->has('payment_method') || request()->has('quickbooks_synced'))
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                    Clear
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($totalAmount, 2) }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Pending Payments</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($pendingAmount, 2) }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
                <i class="fas fa-hourglass-half text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Today's Revenue</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($todayAmount, 2) }}</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QuickBooks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payments as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#{{ $payment->id }}</div>
                                @if($payment->transaction_id)
                                    <div class="text-xs text-gray-500">{{ Str::limit($payment->transaction_id, 15) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->student->name }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->student->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->booking->service->title }}</div>
                                <div class="text-xs text-gray-500">Booking #{{ $payment->booking_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->payment_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($payment->status === 'completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @elseif($payment->status === 'failed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                                @elseif($payment->status === 'refunded')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Refunded</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($payment->synced_to_quickbooks)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Synced
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-times mr-1"></i> Not Synced
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $payments->links() }}
        </div>
    @else
        <div class="p-8 text-center">
            <i class="fas fa-money-bill-wave text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">No payments found.</p>
        </div>
    @endif
</div>
@endsection
