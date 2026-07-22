@extends('admin.layouts.master')

@section('title', 'Student Details')
@section('page-title', $student->name)

@section('content')
@php
    $bookingsCount = $student->bookings->count();
    $paymentsTotal = $payments->where('status', 'completed')->sum('amount');
    $paymentsCount = $payments->count();
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="flex items-start gap-4">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-slate-600 to-slate-800 text-xl font-bold text-white shadow-sm">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">{{ $student->name }}</h3>
            <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-500">
                <span class="inline-flex items-center gap-1"><i class="fas fa-envelope text-gray-400"></i> {{ $student->email }}</span>
                @if($student->phone)
                    <span class="inline-flex items-center gap-1"><i class="fas fa-phone text-gray-400"></i> {{ $student->phone }}</span>
                @endif
            </p>
            @if($student->has_security_registration)
                <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">
                    <i class="fas fa-id-card"></i> Security registered
                </span>
            @endif
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.students.index') }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            ← Back to students
        </a>
        <a href="{{ route('admin.students.edit', $student) }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
            <i class="fas fa-edit"></i> Edit student
        </a>
    </div>
</div>

<div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Bookings</p>
        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $bookingsCount }}</p>
        <p class="text-xs text-gray-500">Class registrations</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Payments</p>
        <p class="mt-1 text-2xl font-bold text-gray-900">${{ number_format($paymentsTotal, 2) }}</p>
        <p class="text-xs text-gray-500">{{ $paymentsCount }} recorded {{ Str::plural('payment', $paymentsCount) }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Security reg.</p>
        @if($student->has_security_registration && $student->security_registration_number)
            <p class="mt-1 text-lg font-bold text-gray-900">{{ $student->security_registration_number }}</p>
            <p class="text-xs text-gray-500">Expires {{ optional($student->security_registration_expiration)->format('M d, Y') ?? '—' }}</p>
        @else
            <p class="mt-1 text-lg font-bold text-gray-400">None</p>
            <p class="text-xs text-gray-500">No registration on file</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h4 class="text-base font-bold text-gray-900">Contact information</h4>
                <a href="{{ route('admin.students.edit', $student) }}" class="text-xs font-medium text-blue-600 hover:underline">Edit details</a>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="rounded-md border border-gray-100 bg-gray-50 p-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Email</p>
                    <p class="mt-1 break-all text-sm font-medium text-gray-900">{{ $student->email }}</p>
                </div>
                <div class="rounded-md border border-gray-100 bg-gray-50 p-3">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Phone</p>
                    <p class="mt-1 text-sm font-medium {{ $student->phone ? 'text-gray-900' : 'text-gray-400' }}">{{ $student->phone ?: 'Not provided' }}</p>
                </div>
                <div class="rounded-md border border-gray-100 bg-gray-50 p-3 sm:col-span-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Address</p>
                    <p class="mt-1 text-sm font-medium {{ $student->address ? 'text-gray-900' : 'text-gray-400' }}">{{ $student->address ?: 'Not provided' }}</p>
                </div>
            </div>

            @if($student->has_security_registration)
                <div class="mt-4 rounded-md border border-green-200 bg-green-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-green-800">Security registration</p>
                    <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <p class="text-[11px] text-green-700">Registration #</p>
                            <p class="text-sm font-bold text-green-950">{{ $student->security_registration_number ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] text-green-700">Expiration</p>
                            <p class="text-sm font-bold text-green-950">{{ optional($student->security_registration_expiration)->format('M d, Y') ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-2 border-b border-gray-100 px-6 py-4">
                <h4 class="text-base font-bold text-gray-900">Class registrations</h4>
                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">{{ $bookingsCount }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Payment</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($student->bookings as $booking)
                            @php
                                $statusClass = match ($booking->status) {
                                    'confirmed', 'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-amber-100 text-amber-800',
                                };
                                $payClass = match ($booking->payment_status) {
                                    'fully_paid' => 'bg-green-100 text-green-800',
                                    'deposit_paid' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">{{ $booking->service->title ?? '—' }}</div>
                                    @if($booking->classSchedule)
                                        <div class="mt-0.5 text-xs text-gray-500">
                                            {{ optional($booking->classSchedule->class_date)->format('M d, Y') }}
                                            @if($booking->classSchedule->start_time)
                                                · {{ \Carbon\Carbon::parse($booking->classSchedule->start_time)->format('g:i A') }}
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                    {{ optional($booking->booking_date)->format('M d, Y') ?? 'TBD' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $statusClass }}">{{ $booking->status }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $payClass }}">{{ str_replace('_', ' ', $booking->payment_status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800">
                                        View <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center">
                                    <i class="fas fa-calendar-times mb-2 text-2xl text-gray-300"></i>
                                    <p class="text-sm text-gray-500">No class registrations yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-5 py-4">
            <h4 class="text-base font-bold text-gray-900">Payment history</h4>
            <p class="mt-0.5 text-xs text-gray-500">Completed total: ${{ number_format($paymentsTotal, 2) }}</p>
        </div>
        <div class="p-5">
            @forelse($payments as $payment)
                @php
                    $payStatusClass = match ($payment->status) {
                        'completed' => 'bg-green-100 text-green-800',
                        'failed', 'refunded' => 'bg-red-100 text-red-800',
                        default => 'bg-amber-100 text-amber-800',
                    };
                @endphp
                <div class="mb-3 rounded-lg border border-gray-100 bg-gray-50 p-3 last:mb-0">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-base font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold capitalize {{ $payStatusClass }}">{{ $payment->status }}</span>
                    </div>
                    <p class="mt-1 text-xs font-medium text-gray-700">{{ $payment->booking->service->title ?? 'Booking #'.$payment->booking_id }}</p>
                    <p class="mt-0.5 text-[11px] text-gray-500">
                        {{ optional($payment->payment_date)->format('M d, Y') ?? optional($payment->created_at)->format('M d, Y') }}
                        @if($payment->payment_method)
                            · {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                        @endif
                    </p>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center">
                    <i class="fas fa-receipt mb-2 text-2xl text-gray-300"></i>
                    <p class="text-sm font-medium text-gray-600">No payments recorded</p>
                    <p class="mt-1 text-xs text-gray-500">Payments for this student’s bookings will show here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
