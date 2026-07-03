@extends('student.layouts.master')

@section('title', 'Payment History')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Payment History</h1>
    <p class="text-gray-600 mt-2">View all your payments and deposits</p>
</div>

@if($payments->count() > 0)
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Confirmation ID</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($payments as $payment)
                <tr>
                    <td class="px-6 py-4 text-sm">{{ optional($payment->payment_date)->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">{{ $payment->booking?->service?->title ?? '—' }}</td>
                    <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $payment->payment_type) }}</td>
                    <td class="px-6 py-4 font-semibold">${{ number_format((float) $payment->amount, 2) }}</td>
                    <td class="px-6 py-4 capitalize">{{ $payment->status }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->transaction_id ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $payments->links() }}</div>
@else
<div class="bg-white rounded-lg shadow p-12 text-center text-gray-500">No payments found.</div>
@endif
@endsection
