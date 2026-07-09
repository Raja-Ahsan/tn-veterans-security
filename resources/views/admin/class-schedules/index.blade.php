@extends('admin.layouts.master')

@section('title', 'Class Schedules')
@section('page-title', 'Class Schedules Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h3 class="text-xl font-semibold">All Class Schedules</h3>
    <a href="{{ route('admin.class-schedules.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        <i class="fas fa-plus mr-2"></i> Create New Schedule
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    @if($schedules->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Slots</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
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
                        @endphp
                        <tr id="schedule-service-row-{{ $serviceId }}" class="bg-gray-50 hover:bg-gray-100 cursor-pointer" onclick="toggleScheduleDetails({{ $serviceId }})">
                            <td class="px-6 py-4">
                                <i class="fas fa-chevron-down text-gray-400" id="icon-{{ $serviceId }}"></i>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $firstSchedule->service->title }}</div>
                                <div class="text-xs text-gray-500">{{ $firstSchedule->instructor ?? 'Instructor TBD' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $totalSlots }} {{ Str::plural('slot', $totalSlots) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $firstSchedule->duration_hours }} {{ Str::plural('hour', $firstSchedule->duration_hours) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span class="font-semibold">{{ $totalStudents }}</span> / {{ $totalCapacity }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Min: {{ $firstSchedule->min_students }} per slot
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $firstSchedule->room ?? 'TBD' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.class-schedules.create', ['service_id' => $serviceId]) }}" 
                                       class="text-green-600 hover:text-green-900" 
                                       title="Add More Slots"
                                       onclick="event.stopPropagation();">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <a href="{{ route('admin.classes.edit', $firstSchedule->service) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="Edit Service"
                                       onclick="event.stopPropagation();">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- Time Slots Details (Hidden by default) -->
                        <tr id="details-{{ $serviceId }}" style="display: none;" class="bg-white">
                            <td colspan="7" class="px-6 py-4">
                                <div class="border-t border-gray-200 pt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Time Slots ({{ $totalSlots }})</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($serviceSchedules as $schedule)
                                                    <tr class="{{ $schedule->class_date < now()->toDateString() ? 'bg-gray-50' : '' }}">
                                                        <td class="px-4 py-2 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900">{{ $schedule->class_date->format('M d, Y') }}</div>
                                                            <div class="text-xs text-gray-500">{{ $schedule->class_date->format('l') }}</div>
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap">
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
                                                        <td class="px-4 py-2 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900">
                                                                <span class="font-semibold">{{ $schedule->current_students }}</span> / {{ $schedule->max_students }}
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap">
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
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                                            <div class="flex gap-2">
                                                                <a href="{{ route('admin.class-schedules.show', $schedule) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.class-schedules.edit', $schedule) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form method="POST" action="{{ route('admin.class-schedules.destroy', $schedule) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this time slot?');">
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
