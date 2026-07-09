@extends('admin.layouts.master')

@section('title', 'Student Details')
@section('page-title', $student->name)

@section('content')
<a href="{{ route('admin.students.index') }}" class="text-blue-600 hover:underline mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Students</a>

<div class="flex justify-end mb-4">
    <a href="{{ route('admin.students.edit', $student) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
        <i class="fas fa-edit mr-1"></i> Edit Student
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Contact Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><dt class="text-sm text-gray-500">Email</dt><dd class="font-medium">{{ $student->email }}</dd></div>
                <div><dt class="text-sm text-gray-500">Phone</dt><dd class="font-medium">{{ $student->phone ?? '—' }}</dd></div>
                <div class="md:col-span-2"><dt class="text-sm text-gray-500">Address</dt><dd class="font-medium">{{ $student->address ?? '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Security Registration</dt><dd class="font-medium">{{ $student->has_security_registration ? 'Yes' : 'No' }}</dd></div>
                @if($student->has_security_registration)
                    <div><dt class="text-sm text-gray-500">Registration #</dt><dd class="font-medium">{{ $student->security_registration_number }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Expiration</dt><dd class="font-medium">{{ optional($student->security_registration_expiration)->format('M d, Y') }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Class Registrations</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b"><th class="text-left py-2">Class</th><th class="text-left py-2">Date</th><th class="text-left py-2">Status</th><th class="text-left py-2">Payment</th></tr></thead>
                    <tbody>
                        @forelse($student->bookings as $booking)
                            <tr class="border-b">
                                <td class="py-2">{{ $booking->service->title ?? '—' }}</td>
                                <td class="py-2">{{ optional($booking->booking_date)->format('M d, Y') ?? 'TBD' }}</td>
                                <td class="py-2">{{ ucfirst($booking->status) }}</td>
                                <td class="py-2">{{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-gray-500">No bookings.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4">Payment History</h3>
        @forelse($payments as $payment)
            <div class="border-b py-3 last:border-0">
                <p class="font-semibold">${{ number_format($payment->amount, 2) }}</p>
                <p class="text-sm text-gray-600">{{ $payment->booking->service->title ?? 'Booking #'.$payment->booking_id }}</p>
                <p class="text-xs text-gray-500">{{ optional($payment->payment_date)->format('M d, Y') }} · {{ ucfirst($payment->status) }}</p>
            </div>
        @empty
            <p class="text-gray-500">No payments.</p>
        @endforelse
    </div>
</div>
@endsection
