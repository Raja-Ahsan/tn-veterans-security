@extends('admin.layouts.master')

@section('title', 'Bookings')
@section('page-title', 'Bookings Management')

@section('content')
@php
    $search = $search ?? '';
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Bookings</h3>
        <p class="mt-1 text-sm text-gray-500">Search by student, class, location, or booking ID.</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white px-4 py-2 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total</p>
        <p id="bookings-total" class="text-lg font-bold text-gray-900">{{ $bookings->total() }}</p>
    </div>
</div>

<div class="mb-6 space-y-3">
    <div class="relative max-w-xl">
        <label for="booking-search" class="sr-only">Search bookings</label>
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
            <i class="fas fa-search text-sm"></i>
        </span>
        <input type="search"
               id="booking-search"
               name="q"
               value="{{ $search }}"
               autocomplete="off"
               placeholder="Search student, email, phone, class, location, booking #…"
               class="w-full rounded-lg border border-gray-300 py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
    </div>

    @if(request()->filled('schedule'))
        <p class="text-xs text-blue-700">
            <i class="fas fa-info-circle mr-1"></i>
            Showing bookings for schedule #{{ request('schedule') }}.
            <a href="{{ route('admin.bookings.index', request()->except('schedule')) }}" class="font-medium underline hover:no-underline">Remove schedule filter</a>
        </p>
    @endif
</div>

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Booking</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Class</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Schedule</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Seats
                        <span class="block font-normal normal-case tracking-normal text-gray-400">students</span>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody id="bookings-table-body" class="divide-y divide-gray-200 bg-white transition-opacity duration-150">
                @include('admin.bookings.partials.table-rows', ['bookings' => $bookings, 'search' => $search])
            </tbody>
        </table>
    </div>

    <div id="bookings-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6 {{ $bookings->hasPages() ? '' : 'hidden' }}">
        {{ $bookings->links() }}
    </div>
</div>

<script>
(function () {
    var input = document.getElementById('booking-search');
    var tbody = document.getElementById('bookings-table-body');
    var pagination = document.getElementById('bookings-pagination');
    var totalEl = document.getElementById('bookings-total');
    if (!input || !tbody) return;

    var indexUrl = @json(route('admin.bookings.index'));
    var scheduleId = @json(request('schedule'));
    var debounceMs = 300;
    var timer = null;
    var requestId = 0;

    function buildUrl(query) {
        var u = new URL(indexUrl, window.location.origin);
        if (query) {
            u.searchParams.set('q', query);
        }
        if (scheduleId) {
            u.searchParams.set('schedule', scheduleId);
        }
        return u;
    }

    function syncBrowserUrl(query) {
        var u = buildUrl(query);
        window.history.replaceState({}, '', u.pathname + u.search);
    }

    function fetchRows(query) {
        var currentRequest = ++requestId;
        tbody.setAttribute('aria-busy', 'true');
        tbody.classList.add('opacity-50', 'pointer-events-none');

        fetch(buildUrl(query).toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
            .then(function (r) {
                if (!r.ok) throw new Error('Search failed');
                return r.json();
            })
            .then(function (data) {
                if (currentRequest !== requestId) return;
                if (data && typeof data.html === 'string') {
                    tbody.innerHTML = data.html;
                }
                if (pagination) {
                    if (data.pagination) {
                        pagination.innerHTML = data.pagination;
                        pagination.classList.toggle('hidden', !data.pagination.trim());
                    } else {
                        pagination.innerHTML = '';
                        pagination.classList.add('hidden');
                    }
                }
                if (totalEl && typeof data.total !== 'undefined') {
                    totalEl.textContent = data.total;
                }
                syncBrowserUrl(query);
            })
            .catch(function () {})
            .finally(function () {
                if (currentRequest !== requestId) return;
                tbody.removeAttribute('aria-busy');
                tbody.classList.remove('opacity-50', 'pointer-events-none');
            });
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        var q = input.value.trim();
        timer = setTimeout(function () {
            fetchRows(q);
        }, debounceMs);
    });
})();
</script>
@endsection
