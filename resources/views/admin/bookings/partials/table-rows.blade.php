@php
    $search = $search ?? '';
    $hasFilters = request()->filled('status')
        || request()->filled('payment_status')
        || request()->filled('schedule')
        || $search !== '';
@endphp

@forelse($bookings as $booking)
    @php
        $locationLabel = $booking->location ?? $booking->classSchedule?->location;
        $isPast = $booking->classSchedule
            && $booking->classSchedule->class_date->lt(now()->startOfDay());
    @endphp
    <tr class="hover:bg-gray-50 {{ $isPast ? 'bg-slate-50/60' : '' }}">
        <td class="px-4 py-3 whitespace-nowrap">
            <div class="text-sm font-bold text-gray-900">#{{ $booking->id }}</div>
            <div class="text-xs text-gray-500">{{ $booking->created_at->format('M j, Y') }}</div>
        </td>
        <td class="px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                    {{ strtoupper(substr($booking->student->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-gray-900">{{ $booking->student->name ?? '—' }}</div>
                    <div class="truncate text-xs text-gray-500">{{ $booking->student->email ?? '' }}</div>
                    @if($booking->student?->phone)
                        <div class="text-xs text-gray-400">{{ $booking->student->phone }}</div>
                    @endif
                </div>
            </div>
        </td>
        <td class="px-4 py-3">
            <div class="max-w-[14rem] truncate text-sm font-semibold text-gray-900" title="{{ $booking->service->title ?? '' }}">
                {{ $booking->service->title ?? '—' }}
            </div>
            <div class="mt-0.5">
                <span class="inline-flex rounded bg-gray-100 px-1.5 py-0.5 text-[11px] font-medium text-gray-600">
                    {{ $booking->booking_type === 'one-on-one' ? 'One-on-one' : 'Group' }}
                </span>
            </div>
            @if($locationLabel)
                <div class="mt-1 max-w-[14rem] truncate text-xs text-gray-500" title="{{ $locationLabel }}">
                    <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>{{ $locationLabel }}
                </div>
            @endif
        </td>
        <td class="px-4 py-3 whitespace-nowrap">
            @if($booking->classSchedule)
                <div class="text-sm font-medium text-gray-900">{{ $booking->classSchedule->class_date->format('M j, Y') }}</div>
                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->classSchedule->start_time)->format('g:i A') }}</div>
                @if($isPast)
                    <span class="mt-1 inline-flex text-[11px] font-medium text-gray-400">Past</span>
                @endif
            @else
                <span class="text-sm text-gray-400">TBD</span>
            @endif
        </td>
        <td class="px-4 py-3 whitespace-nowrap">
            <span class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-md bg-slate-100 px-2 text-sm font-bold text-slate-700">
                {{ $booking->number_of_students ?? 1 }}
            </span>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">
            <div class="text-sm font-bold text-gray-900">
                @if($booking->total_amount !== null)
                    ${{ number_format($booking->total_amount, 2) }}
                @else
                    —
                @endif
            </div>
            <div class="mt-1">
                @if($booking->payment_status === 'pending')
                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Payment pending</span>
                @elseif($booking->payment_status === 'deposit_paid')
                    <span class="inline-flex rounded-full bg-sky-100 px-2 py-0.5 text-xs font-semibold text-sky-800">Deposit paid</span>
                @elseif($booking->payment_status === 'fully_paid')
                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Fully paid</span>
                @else
                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">No payment</span>
                @endif
            </div>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">
            @if($booking->status === 'pending')
                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Pending</span>
            @elseif($booking->status === 'confirmed')
                <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">Confirmed</span>
            @elseif($booking->status === 'completed')
                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">Completed</span>
            @elseif($booking->status === 'cancelled')
                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">Cancelled</span>
            @else
                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">{{ ucfirst($booking->status) }}</span>
            @endif
        </td>
        <td class="px-4 py-3 whitespace-nowrap text-right">
            <a href="{{ route('admin.bookings.show', $booking) }}"
               class="inline-flex items-center gap-1.5 rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                <i class="fas fa-eye"></i> View
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-14 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                <i class="fas fa-calendar-times text-xl"></i>
            </div>
            <p class="text-base font-semibold text-gray-800">No bookings found</p>
            <p class="mt-1 text-sm text-gray-500">
                @if($hasFilters)
                    Try a different search or clear your filters.
                @else
                    Bookings will appear here when students enroll in classes.
                @endif
            </p>
        </td>
    </tr>
@endforelse
