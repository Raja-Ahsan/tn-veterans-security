@extends('admin.layouts.master')

@section('title', 'Classes')
@section('page-title', 'Classes Management')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">All Classes</h3>
        <p class="mt-1 text-sm text-gray-500">A class is the course offering. Sessions (dates/times) are managed under Class Schedules.</p>
    </div>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
        <div class="relative w-full sm:w-72">
            <label for="service-title-search" class="sr-only">Search classes by title</label>
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fas fa-search text-sm"></i>
            </span>
            <input type="search"
                id="service-title-search"
                name="q"
                value="{{ $search ?? '' }}"
                autocomplete="off"
                placeholder="Search by title…"
                class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
        </div>
        <a href="{{ route('admin.classes.create') }}" class="inline-flex shrink-0 items-center justify-center bg-green-600 px-4 py-2 text-white hover:bg-green-700 rounded">
            <i class="fas fa-plus mr-2"></i> Add New Class
        </a>
    </div>
</div>

<div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
    <div class="flex gap-3">
        <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
        <div class="space-y-1">
            <p class="font-semibold">How this page works</p>
            <ul class="list-disc space-y-0.5 pl-4 text-blue-800">
                <li><span class="font-medium">Class</span> = the course (title, price, description).</li>
                <li><span class="font-medium">Sessions</span> = bookable date/time slots — managed under Class Schedules.</li>
                <li><span class="font-medium">Blended</span> classes have online modules/quizzes. After deposit is paid, students take quizzes in their portal.</li>
                <li><span class="font-medium">Edit</span> → Modules &amp; Quizzes to add content, then Student Progress to track scores.</li>
            </ul>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Price
                        <span class="block font-normal normal-case tracking-normal text-gray-400">full / deposit</span>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sessions
                        <span class="block font-normal normal-case tracking-normal text-gray-400">on the calendar</span>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order
                        <span class="block font-normal normal-case tracking-normal text-gray-400">list sort</span>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="services-table-body" class="bg-white divide-y divide-gray-200 transition-opacity duration-150">
                @include('admin.classes.partials.table-rows', ['services' => $services, 'search' => $search ?? ''])
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    var input = document.getElementById('service-title-search');
    var tbody = document.getElementById('services-table-body');
    if (!input || !tbody) return;

    var indexUrl = @json(route('admin.classes.index'));
    var debounceMs = 300;
    var timer = null;

    function fetchRows(query) {
        tbody.setAttribute('aria-busy', 'true');
        tbody.classList.add('opacity-50', 'pointer-events-none');
        var u = new URL(indexUrl, window.location.origin);
        u.searchParams.set('q', query);
        fetch(u.toString(), {
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
                if (data && typeof data.html === 'string') {
                    tbody.innerHTML = data.html;
                }
            })
            .catch(function () {})
            .finally(function () {
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
