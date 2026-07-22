@extends('admin.layouts.master')

@section('title', 'Class Schedules')
@section('page-title', 'Class Schedules Management')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">All Class Schedules</h3>
        <p class="mt-1 text-sm text-gray-500">Each row is one class. Click a row to see its individual date/time sessions.</p>
    </div>
    <a href="{{ route('admin.class-schedules.create') }}" class="inline-flex shrink-0 items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        <i class="fas fa-plus mr-2"></i> Create New Schedule
    </a>
</div>

{{-- How to read this page --}}
<div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
    <div class="flex gap-3">
        <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
        <div class="space-y-1">
            <p class="font-semibold">How to read this list</p>
            <ul class="list-disc space-y-0.5 pl-4 text-blue-800">
                <li><span class="font-medium">Sessions</span> = how many date/time slots this class has (expand the row to see each one).</li>
                <li><span class="font-medium">Enrollment</span> = enrolled students / total seats across all sessions.</li>
                <li><span class="font-medium">Min per session</span> = minimum students needed for one session to run.</li>
            </ul>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($schedules->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sessions
                            <span class="block font-normal normal-case tracking-normal text-gray-400">date/time slots</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Enrollment
                            <span class="block font-normal normal-case tracking-normal text-gray-400">enrolled / capacity</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schedules as $serviceId => $serviceSchedules)
                        @php
                            $firstSchedule = $serviceSchedules->first();
                            $totalSlots = $serviceSchedules->count();
                            $totalStudents = $serviceSchedules->sum('current_students');
                            $totalCapacity = $serviceSchedules->sum('max_students');
                            $fillPercent = $totalCapacity > 0 ? min(100, (int) round(($totalStudents / $totalCapacity) * 100)) : 0;
                            $seatsLeft = max(0, $totalCapacity - $totalStudents);
                        @endphp
                        <tr id="schedule-service-row-{{ $serviceId }}" class="bg-white hover:bg-gray-50 cursor-pointer transition-colors" onclick="toggleScheduleDetails({{ $serviceId }})" title="Click to show or hide sessions">
                            <td class="px-4 py-4">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                    <i class="fas fa-chevron-down text-xs transition-transform" id="icon-{{ $serviceId }}"></i>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $firstSchedule->service->title }}</div>
                                <div class="mt-0.5 flex items-center gap-2 text-xs text-gray-500">
                                    <span><i class="fas fa-chalkboard-teacher mr-1"></i>{{ $firstSchedule->instructor ?? 'Instructor TBD' }}</span>
                                    <span class="text-gray-300">·</span>
                                    <span class="text-blue-600 font-medium"><i class="fas fa-mouse-pointer mr-1"></i>Click to expand</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2.5 py-1 text-sm font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">
                                    <i class="fas fa-calendar-day text-blue-500"></i>
                                    {{ $totalSlots }} {{ Str::plural('session', $totalSlots) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $firstSchedule->duration_hours }} {{ Str::plural('hour', $firstSchedule->duration_hours) }}
                            </td>
                            <td class="px-6 py-4 min-w-[11rem]">
                                <div class="flex items-baseline gap-1 text-sm text-gray-900">
                                    <span class="font-bold">{{ $totalStudents }}</span>
                                    <span class="text-gray-400">/</span>
                                    <span class="text-gray-600">{{ $totalCapacity }}</span>
                                    <span class="ml-1 text-xs font-normal text-gray-400">seats</span>
                                </div>
                                <div class="mt-1.5 h-1.5 w-full max-w-[9rem] overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full {{ $fillPercent >= 100 ? 'bg-amber-500' : ($fillPercent >= 50 ? 'bg-green-500' : 'bg-blue-500') }}" style="width: {{ $fillPercent }}%"></div>
                                </div>
                                <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-500">
                                    <span>{{ $seatsLeft }} open</span>
                                    <span class="text-gray-300">·</span>
                                    <span>Min {{ $firstSchedule->min_students }}/session</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $firstSchedule->room ?? 'TBD' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.class-schedules.create', ['service_id' => $serviceId]) }}"
                                       class="inline-flex items-center gap-1 text-green-600 hover:text-green-800"
                                       title="Add another session"
                                       onclick="event.stopPropagation();">
                                        <i class="fas fa-plus"></i>
                                        <span class="hidden lg:inline">Add</span>
                                    </a>
                                    <a href="{{ route('admin.classes.edit', $firstSchedule->service) }}"
                                       class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800"
                                       title="Edit class"
                                       onclick="event.stopPropagation();">
                                        <i class="fas fa-edit"></i>
                                        <span class="hidden lg:inline">Edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        {{-- Individual sessions (hidden until expanded) --}}
                        <tr id="details-{{ $serviceId }}" style="display: none;" class="bg-slate-50">
                            <td colspan="7" class="px-6 py-4">
                                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                                    <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <h4 class="text-sm font-semibold text-gray-800">
                                            <i class="fas fa-clock text-slate-400 mr-1"></i>
                                            Individual sessions for {{ $firstSchedule->service->title }}
                                        </h4>
                                        <p class="text-xs text-gray-500">{{ $totalSlots }} {{ Str::plural('session', $totalSlots) }} · each has its own date, time &amp; enrollment</p>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time &amp; Location</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                        Enrollment
                                                        <span class="block font-normal normal-case tracking-normal text-gray-400">this session only</span>
                                                    </th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($serviceSchedules as $schedule)
                                                    @php
                                                        $slotFill = $schedule->max_students > 0
                                                            ? min(100, (int) round(($schedule->current_students / $schedule->max_students) * 100))
                                                            : 0;
                                                        $meetsMin = $schedule->current_students >= $schedule->min_students;
                                                        $isPast = $schedule->class_date < now()->toDateString();
                                                    @endphp
                                                    <tr class="{{ $isPast ? 'bg-gray-50 opacity-75' : '' }}">
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900">{{ $schedule->class_date->format('M d, Y') }}</div>
                                                            <div class="text-xs text-gray-500">{{ $schedule->class_date->format('l') }}@if($isPast) · past @endif</div>
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</div>
                                                            @if($schedule->end_time)
                                                                <div class="text-xs text-gray-500">to {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</div>
                                                            @endif
                                                            @if($schedule->location)
                                                                <div class="text-xs text-[var(--primary-color)] font-semibold mt-1">
                                                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $schedule->location }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 min-w-[10rem]">
                                                            <div class="text-sm text-gray-900">
                                                                <span class="font-semibold">{{ $schedule->current_students }}</span>
                                                                <span class="text-gray-400">/</span>
                                                                {{ $schedule->max_students }}
                                                                <span class="text-xs text-gray-400">seats</span>
                                                            </div>
                                                            <div class="mt-1.5 h-1.5 w-full max-w-[8rem] overflow-hidden rounded-full bg-gray-200">
                                                                <div class="h-full rounded-full {{ $slotFill >= 100 ? 'bg-amber-500' : 'bg-green-500' }}" style="width: {{ $slotFill }}%"></div>
                                                            </div>
                                                            <div class="mt-1 text-xs {{ $meetsMin ? 'text-green-600' : 'text-amber-600' }}">
                                                                @if($meetsMin)
                                                                    <i class="fas fa-check-circle"></i> Min met ({{ $schedule->min_students }})
                                                                @else
                                                                    <i class="fas fa-exclamation-circle"></i> Need {{ $schedule->min_students - $schedule->current_students }} more for min ({{ $schedule->min_students }})
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            @if($schedule->status === 'scheduled')
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                                                            @elseif($schedule->status === 'full')
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Full</span>
                                                            @elseif($schedule->status === 'cancelled')
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                                            @elseif($schedule->status === 'completed')
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                            <div class="flex gap-2">
                                                                <a href="{{ route('admin.class-schedules.show', $schedule) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.class-schedules.edit', $schedule) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form method="POST" action="{{ route('admin.class-schedules.destroy', $schedule) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this session?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="p-8 text-center">
            <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500">No class schedules found.</p>
            <a href="{{ route('admin.class-schedules.create') }}" class="mt-4 inline-block text-green-600 hover:underline">
                Create your first class schedule
            </a>
        </div>
    @endif
</div>

<script>
function toggleScheduleDetails(serviceId) {
    const detailsRow = document.getElementById('details-' + serviceId);
    const icon = document.getElementById('icon-' + serviceId);
    if (!detailsRow || !icon) {
        return;
    }
    const isHidden = detailsRow.style.display === 'none' || detailsRow.style.display === '';
    if (isHidden) {
        detailsRow.style.display = 'table-row';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        detailsRow.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var expandKey = @json($expandServiceKey ?? null);
    if (expandKey === null) {
        return;
    }
    var details = document.getElementById('details-' + expandKey);
    if (!details) {
        return;
    }
    var hidden = details.style.display === 'none' || details.style.display === '';
    if (hidden) {
        toggleScheduleDetails(expandKey);
    }
    var row = document.getElementById('schedule-service-row-' + expandKey);
    if (row) {
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endsection
