@extends('admin.layouts.master')

@section('title', 'Bookings')
@section('page-title', 'Bookings Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-xl font-semibold">All Bookings</h3>
    <div class="flex gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex gap-2">
            <select name="status" class="border rounded px-3 py-2 text-sm">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select name="payment_status" class="border rounded px-3 py-2 text-sm">
                <option value="">All Payment Statuses</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="deposit_paid" {{ request('payment_status') === 'deposit_paid' ? 'selected' : '' }}>Deposit Paid</option>
                <option value="fully_paid" {{ request('payment_status') === 'fully_paid' ? 'selected' : '' }}>Fully Paid</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                Filter
            </button>
            @if(request()->has('status') || request()->has('payment_status'))
                <a href="{{ route('admin.bookings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                    Clear
                </a>
            @endif
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($bookings->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($bookings as $booking)
                        <tr class="{{ $booking->booking_date < now()->toDateString() ? 'bg-gray-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#{{ $booking->id }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $booking->student->name }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->student->email }}</div>
                                @if($booking->student->phone)
                                    <div class="text-xs text-gray-500">{{ $booking->student->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $booking->service->title }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->booking_type === 'one-on-one' ? 'One-on-One' : 'Group' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($booking->classSchedule)
                                    <div class="text-sm text-gray-900">{{ $booking->classSchedule->class_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->classSchedule->start_time)->format('h:i A') }}</div>
                                @else
                                    <div class="text-sm text-gray-500">TBD</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($booking->location || ($booking->classSchedule && $booking->classSchedule->location))
                                    <span class="text-sm font-semibold text-[var(--primary-color)]">
                                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $booking->location ?? $booking->classSchedule->location }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $booking->number_of_students ?? 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($booking->total_amount)
                                        ${{ number_format($booking->total_amount, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                                @if($booking->payment_status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($booking->payment_status === 'deposit_paid')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Deposit Paid</span>
                                @elseif($booking->payment_status === 'fully_paid')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Fully Paid</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($booking->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Confirmed</span>
                                @elseif($booking->status === 'completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="p-8 text-center">
            <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">No bookings found.</p>
        </div>
    @endif
</div>
@endsection
