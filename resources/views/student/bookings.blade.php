@extends('student.layouts.master')

@section('title', 'My Bookings')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">My Bookings</h1>
    <p class="text-gray-600 mt-2">View and manage your service bookings</p>
    <div class="flex gap-2 mt-4">
        <a href="{{ route('student.bookings', ['filter' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ ($filter ?? 'all') === 'all' ? 'bg-green-600 text-white' : 'bg-white border text-gray-700' }}">All</a>
        <a href="{{ route('student.bookings', ['filter' => 'upcoming']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ ($filter ?? '') === 'upcoming' ? 'bg-green-600 text-white' : 'bg-white border text-gray-700' }}">Upcoming</a>
        <a href="{{ route('student.bookings', ['filter' => 'past']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold {{ ($filter ?? '') === 'past' ? 'bg-green-600 text-white' : 'bg-white border text-gray-700' }}">Past</a>
    </div>
</div>

@if($bookings->count() > 0)
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked On</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $booking->service->title }}</div>
                        @if($booking->classSchedule)
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $booking->classSchedule->class_date->format('M j, Y') }}
                                · {{ \Carbon\Carbon::parse($booking->classSchedule->start_time)->format('g:i A') }}
                                @if($booking->classSchedule->location)
                                    · {{ $booking->classSchedule->location }}
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $booking->booking_date->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">
                            @if($booking->booking_time)
                                {{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('student.bookings.show', $booking->id) }}" class="transition-colors" style="color: #3AA62C;" onmouseover="this.style.color='#175B0E'" onmouseout="this.style.color='#3AA62C'">
                            View Details
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($bookings, 'links'))
    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@else
<div class="bg-white rounded-lg shadow p-12 text-center">
    <div class="max-w-md mx-auto">
        <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">No Bookings Found</h3>
        <p class="text-gray-600 mb-6">You haven't booked any services yet. Start by exploring our training services.</p>
        <a href="{{ route('services') }}" class="inline-block text-white font-bold py-3 px-6 rounded transition-colors shadow-md" style="background-color: #3AA62C;" onmouseover="this.style.backgroundColor='#175B0E'" onmouseout="this.style.backgroundColor='#3AA62C'">
            Browse Services
        </a>
    </div>
</div>
@endif
@endsection
