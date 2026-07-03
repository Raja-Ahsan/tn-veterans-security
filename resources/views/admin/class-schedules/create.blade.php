@extends('admin.layouts.master')

@section('title', 'Create Class Schedule')
@section('page-title', 'Create New Class Schedule')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.class-schedules.store') }}">
        @csrf

        <!-- Service Selection -->
        <div class="mb-4">
            <label for="service_id" class="block text-gray-700 text-sm font-bold mb-2">Class Schedule for<span class="text-red-500">*</span></label>
            <select id="service_id" name="service_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Select a Class</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $selectedServiceId ?? null) == $service->id ? 'selected' : '' }}>
                        {{ $service->title }}
                        @if($service->price)
                            - ${{ number_format($service->price, 2) }}
                        @endif
                    </option>
                @endforeach
            </select>
            @error('service_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Schedule Type -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Schedule Type <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
                <label class="flex items-center">
                    <input type="radio" name="schedule_type" value="single" checked onchange="toggleScheduleType()" class="mr-2">
                    <span>Single Schedule</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="schedule_type" value="multiple" onchange="toggleScheduleType()" class="mr-2">
                    <span>Multiple Schedules</span>
                </label>
            </div>
        </div>

        <!-- Single Schedule -->
        <div id="single-schedule">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Class Date -->
                <div class="mb-4">
                    <label for="class_date" class="block text-gray-700 text-sm font-bold mb-2">Class Date <span class="text-red-500">*</span></label>
                    <input type="date" 
                           id="class_date" 
                           name="class_date" 
                           value="{{ old('class_date') }}"
                           min="{{ date('Y-m-d') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('class_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Time -->
                <div class="mb-4">
                    <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Start Time <span class="text-red-500">*</span></label>
                    <input type="time" 
                           id="start_time" 
                           name="start_time" 
                           value="{{ old('start_time') }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('start_time')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Multiple Schedules -->
        <div id="multiple-schedules" style="display: none;">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Select Multiple Dates and Times <span class="text-red-500">*</span></label>
                <div id="schedule-slots" class="space-y-4">
                    <!-- First slot -->
                    <div class="schedule-slot border rounded p-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                                <input type="date" 
                                       name="schedules[0][class_date]" 
                                       min="{{ date('Y-m-d') }}"
                                       class="schedule-date shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Start Time</label>
                                <input type="time" 
                                       name="schedules[0][start_time]" 
                                       class="schedule-time shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                        <button type="button" onclick="removeScheduleSlot(this)" class="mt-2 text-red-600 hover:text-red-800 text-sm">
                            <i class="fas fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </div>
                <button type="button" onclick="addScheduleSlot()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-plus mr-2"></i> Add Another Date/Time
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Duration Hours -->
            <div class="mb-4">
                <label for="duration_hours" class="block text-gray-700 text-sm font-bold mb-2">Duration (Hours) <span class="text-red-500">*</span></label>
                <input type="number" 
                       id="duration_hours" 
                       name="duration_hours" 
                       value="{{ old('duration_hours') }}"
                       min="1"
                       required 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">e.g., 4, 8, 16 hours</p>
                @error('duration_hours')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Max Students -->
            <div class="mb-4">
                <label for="max_students" class="block text-gray-700 text-sm font-bold mb-2">Max Students <span class="text-red-500">*</span></label>
                <input type="number" 
                       id="max_students" 
                       name="max_students" 
                       value="{{ old('max_students', 10) }}"
                       min="1"
                       max="10"
                       required 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('max_students')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Min Students -->
            <div class="mb-4">
                <label for="min_students" class="block text-gray-700 text-sm font-bold mb-2">Min Students <span class="text-red-500">*</span></label>
                <input type="number" 
                       id="min_students" 
                       name="min_students" 
                       value="{{ old('min_students', 2) }}"
                       min="1"
                       max="4"
                       required 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">2 or 4 depending on course</p>
                @error('min_students')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Location Selection (from database) -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Location(s) <span class="text-gray-500 text-xs font-normal">(Select one or more)</span></label>
            <div class="flex flex-col gap-2 mt-2">
                @forelse($locations as $loc)
                    <label class="flex items-start gap-2">
                        <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}"
                               {{ in_array($loc->id, old('location_ids', [])) ? 'checked' : '' }}
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="text-gray-700">{{ $loc->display_name }}</span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">No locations in database. <a href="{{ route('admin.locations.create') }}" class="text-blue-600">Add locations</a> or enter a custom location below.</p>
                @endforelse
            </div>
            <input type="text" name="location" value="{{ old('location') }}" placeholder="Custom location (if none selected above)" class="mt-3 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            <p class="text-xs text-gray-500 mt-1">A schedule is created for each selected location.</p>
            @error('location_ids')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Room -->
            <div class="mb-4">
                <label for="room" class="block text-gray-700 text-sm font-bold mb-2">Room</label>
                <input type="text" id="room" name="room" value="{{ old('room') }}" placeholder="Room name or number"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('room')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Instructor -->
            <div class="mb-4">
                <label for="instructor_id" class="block text-gray-700 text-sm font-bold mb-2">Instructor</label>
                <select id="instructor_id" name="instructor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">— Select instructor —</option>
                    @foreach($instructors as $inst)
                        <option value="{{ $inst->id }}" {{ old('instructor_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                    @endforeach
                </select>
                <input type="text" id="instructor" name="instructor" value="{{ old('instructor') }}" placeholder="Or custom instructor name" class="mt-2 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('instructor_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="admin_override_capacity" value="1" {{ old('admin_override_capacity') ? 'checked' : '' }} class="mr-2">
                <span class="text-gray-700 text-sm font-bold">Admin override capacity (allow over-enrollment)</span>
            </label>
        </div>

        <div class="mb-4">
            <label for="travel_notes" class="block text-gray-700 text-sm font-bold mb-2">Travel Notes</label>
            <textarea id="travel_notes" name="travel_notes" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('travel_notes') }}</textarea>
        </div>

        <!-- Can Overlap -->
        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="can_overlap" 
                       value="1"
                       {{ old('can_overlap') ? 'checked' : '' }}
                       class="mr-2">
                <span class="text-gray-700 text-sm font-bold">Allow overlapping classes in the same room</span>
            </label>
            <p class="text-xs text-gray-500 mt-1">Check if multiple classes can run simultaneously in the same room</p>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
            <textarea id="notes" 
                      name="notes" 
                      rows="3"
                      placeholder="Any additional notes or instructions"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                <i class="fas fa-save mr-2"></i> Create Schedule(s)
            </button>
            <a href="{{ route('admin.class-schedules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
let slotIndex = 1;

function toggleScheduleType() {
    const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
    const singleSchedule = document.getElementById('single-schedule');
    const multipleSchedules = document.getElementById('multiple-schedules');
    const singleDateInput = document.getElementById('class_date');
    const singleTimeInput = document.getElementById('start_time');
    
    if (scheduleType === 'single') {
        singleSchedule.style.display = 'block';
        multipleSchedules.style.display = 'none';
        // Make single schedule fields required and enabled
        singleDateInput.required = true;
        singleTimeInput.required = true;
        singleDateInput.disabled = false;
        singleTimeInput.disabled = false;
        // Remove required and disable multiple schedules
        document.querySelectorAll('.schedule-date, .schedule-time').forEach(el => {
            el.required = false;
            el.disabled = true;
        });
    } else {
        singleSchedule.style.display = 'none';
        multipleSchedules.style.display = 'block';
        // Remove required and disable single schedule fields
        singleDateInput.required = false;
        singleTimeInput.required = false;
        singleDateInput.disabled = true;
        singleTimeInput.disabled = true;
        singleDateInput.value = '';
        singleTimeInput.value = '';
        // Make multiple schedule fields required and enabled
        document.querySelectorAll('.schedule-date, .schedule-time').forEach(el => {
            el.required = true;
            el.disabled = false;
        });
    }
}

function addScheduleSlot() {
    const slotsContainer = document.getElementById('schedule-slots');
    const newSlot = document.createElement('div');
    newSlot.className = 'schedule-slot border rounded p-4 bg-gray-50';
    newSlot.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                <input type="date" 
                       name="schedules[${slotIndex}][class_date]" 
                       min="{{ date('Y-m-d') }}"
                       required
                       class="schedule-date shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Start Time</label>
                <input type="time" 
                       name="schedules[${slotIndex}][start_time]" 
                       required
                       class="schedule-time shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>
        <button type="button" onclick="removeScheduleSlot(this)" class="mt-2 text-red-600 hover:text-red-800 text-sm">
            <i class="fas fa-trash mr-1"></i> Remove
        </button>
    `;
    slotsContainer.appendChild(newSlot);
    slotIndex++;
}

function removeScheduleSlot(button) {
    const slot = button.closest('.schedule-slot');
    slot.remove();
}
</script>
@endsection
