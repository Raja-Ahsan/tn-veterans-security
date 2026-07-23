@extends('admin.layouts.master')

@section('title', 'Create Class Schedule')
@section('page-title', 'Create New Class Schedule')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Create class schedule</h3>
        <p class="mt-0.5 text-sm text-gray-500">Pick a class, set date/time and capacity, then choose location and instructor.</p>
        <p class="mt-1 text-xs text-gray-400">Fields marked <span class="text-red-500">*</span> are required.</p>
    </div>
    <a href="{{ route('admin.class-schedules.index') }}"
       class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ← Back to schedules
    </a>
</div>

@if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Please fix these issues before saving:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.class-schedules.store') }}" class="space-y-6">
    @csrf

    {{-- 1. Class & schedule type --}}
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-5">
        <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">1</span>
            <div>
                <h4 class="text-base font-bold text-gray-900">Class & schedule type</h4>
                <p class="text-sm text-gray-500">Choose which course this session belongs to, then one date or several dates.</p>
            </div>
        </div>

        <div>
            <label for="service_id" class="mb-1.5 block text-sm font-bold text-gray-700">
                Class <span class="text-red-500">*</span>
            </label>
            <select id="service_id" name="service_id" required
                    class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('service_id') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                <option value="">Select a class</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $selectedServiceId ?? null) == $service->id ? 'selected' : '' }}>
                        {{ $service->title }}
                        @if($service->price)
                            — ${{ number_format($service->price, 2) }}
                        @endif
                    </option>
                @endforeach
            </select>
            @error('service_id')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-gray-700">Schedule type <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                    <input type="radio" name="schedule_type" value="single" {{ old('schedule_type', 'single') === 'single' ? 'checked' : '' }}
                           onchange="toggleScheduleType()" class="mt-0.5 text-green-600 focus:ring-green-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-900">Single schedule</span>
                        <p class="mt-0.5 text-xs text-gray-500">One date and start time.</p>
                    </div>
                </label>
                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                    <input type="radio" name="schedule_type" value="multiple" {{ old('schedule_type') === 'multiple' ? 'checked' : '' }}
                           onchange="toggleScheduleType()" class="mt-0.5 text-green-600 focus:ring-green-500">
                    <div>
                        <span class="text-sm font-semibold text-gray-900">Multiple schedules</span>
                        <p class="mt-0.5 text-xs text-gray-500">Add several date/time slots at once.</p>
                    </div>
                </label>
            </div>
        </div>

        <div id="single-schedule">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="class_date" class="mb-1.5 block text-sm font-bold text-gray-700">
                        Class date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="class_date" name="class_date" value="{{ old('class_date') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('class_date') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                    @error('class_date')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="start_time" class="mb-1.5 block text-sm font-bold text-gray-700">
                        Start time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}"
                           class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('start_time') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                    @error('start_time')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div id="multiple-schedules" class="hidden">
            <label class="mb-2 block text-sm font-bold text-gray-700">
                Dates & times <span class="text-red-500">*</span>
            </label>
            <div id="schedule-slots" class="space-y-3">
                <div class="schedule-slot rounded-lg border border-gray-200 bg-slate-50 p-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-bold text-gray-700">Date</label>
                            <input type="date" name="schedules[0][class_date]" min="{{ date('Y-m-d') }}"
                                   class="schedule-date w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-bold text-gray-700">Start time</label>
                            <input type="time" name="schedules[0][start_time]"
                                   class="schedule-time w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        </div>
                    </div>
                    <button type="button" onclick="removeScheduleSlot(this)" class="mt-3 text-sm font-medium text-red-600 hover:text-red-800">
                        <i class="fas fa-trash mr-1"></i> Remove
                    </button>
                </div>
            </div>
            <button type="button" onclick="addScheduleSlot()"
                    class="mt-3 inline-flex items-center gap-2 rounded-md border border-blue-300 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                <i class="fas fa-plus"></i> Add another date/time
            </button>
            @error('schedules')
                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- 2. Duration & capacity --}}
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-5">
        <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">2</span>
            <div>
                <h4 class="text-base font-bold text-gray-900">Duration & capacity</h4>
                <p class="text-sm text-gray-500">How long the session runs and how many students can enroll.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label for="duration_hours" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Duration (hours) <span class="text-red-500">*</span>
                </label>
                <input type="number" id="duration_hours" name="duration_hours" value="{{ old('duration_hours') }}"
                       min="1" required placeholder="e.g. 8"
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('duration_hours') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                <p class="mt-1 text-xs text-gray-500">Typical: 4, 8, or 16 hours</p>
                @error('duration_hours')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="max_students" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Max students <span class="text-red-500">*</span>
                </label>
                <input type="number" id="max_students" name="max_students" value="{{ old('max_students', 10) }}"
                       min="1" max="10" required
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('max_students') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                <p class="mt-1 text-xs text-gray-500">Maximum 10</p>
                @error('max_students')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="min_students" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Min students <span class="text-red-500">*</span>
                </label>
                <input type="number" id="min_students" name="min_students" value="{{ old('min_students', 2) }}"
                       min="1" max="4" required
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('min_students') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                <p class="mt-1 text-xs text-gray-500">Usually 2 or 4</p>
                @error('min_students')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <input type="checkbox" name="admin_override_capacity" value="1" {{ old('admin_override_capacity') ? 'checked' : '' }}
                   class="mt-0.5 rounded border-gray-400 text-amber-600 focus:ring-amber-500">
            <div>
                <span class="text-sm font-semibold text-gray-900">Admin override capacity</span>
                <p class="mt-0.5 text-xs text-gray-600">Allow over-enrollment past the max students limit.</p>
            </div>
        </label>
    </div>

    {{-- 3. Location & instructor --}}
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-5">
        <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">3</span>
            <div>
                <h4 class="text-base font-bold text-gray-900">Location & instructor</h4>
                <p class="text-sm text-gray-500">Select locations from the list, or add a custom one if needed.</p>
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-gray-700">
                Location(s) <span class="font-normal text-gray-400">(select one or more)</span>
            </label>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @forelse($locations as $loc)
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-3 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                        <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}"
                               {{ in_array($loc->id, old('location_ids', [])) ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-400 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-800">{{ $loc->display_name }}</span>
                    </label>
                @empty
                    <p class="col-span-full rounded-md border border-dashed border-gray-300 bg-gray-50 px-3 py-3 text-sm text-gray-500">
                        No locations yet.
                        <a href="{{ route('admin.locations.create') }}" class="font-medium text-blue-600 hover:underline">Add a location</a>
                        or use the custom field below.
                    </p>
                @endforelse
            </div>
            @php $showCustomLocation = filled(old('location')); @endphp
            <div id="custom-location-wrap" class="mt-3 {{ $showCustomLocation ? '' : 'hidden' }}">
                <label for="location" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Custom location
                </label>
                <input type="text" id="location" name="location" value="{{ old('location') }}"
                       placeholder="Only if none selected above"
                       class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            </div>
            <button type="button" id="toggle-custom-location"
                    class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-green-700 hover:text-green-800">
                <i class="fas {{ $showCustomLocation ? 'fa-times' : 'fa-plus' }} text-xs"></i>
                <span>{{ $showCustomLocation ? 'Remove custom location' : 'Add custom location' }}</span>
            </button>
            @error('location_ids')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @php $showCustomInstructor = filled(old('instructor')); @endphp
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="room" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Room <span class="font-normal text-gray-400">(optional)</span>
                </label>
                <input type="text" id="room" name="room" value="{{ old('room') }}" placeholder="Room name or number"
                       class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                @error('room')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="instructor_id" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Instructor <span class="font-normal text-gray-400">(optional)</span>
                </label>
                <select id="instructor_id" name="instructor_id"
                        class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="">Select instructor</option>
                    @foreach($instructors as $inst)
                        <option value="{{ $inst->id }}" {{ old('instructor_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                    @endforeach
                </select>
                <div id="custom-instructor-wrap" class="mt-2 {{ $showCustomInstructor ? '' : 'hidden' }}">
                    <input type="text" id="instructor" name="instructor" value="{{ old('instructor') }}"
                           placeholder="Enter custom instructor name"
                           class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <button type="button" id="toggle-custom-instructor"
                        class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-green-700 hover:text-green-800">
                    <i class="fas {{ $showCustomInstructor ? 'fa-times' : 'fa-plus' }} text-xs"></i>
                    <span>{{ $showCustomInstructor ? 'Remove custom instructor' : 'Add custom instructor' }}</span>
                </button>
                @error('instructor_id')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
            <input type="checkbox" name="can_overlap" value="1" {{ old('can_overlap') ? 'checked' : '' }}
                   class="mt-0.5 rounded border-gray-400 text-green-600 focus:ring-green-500">
            <div>
                <span class="text-sm font-semibold text-gray-900">Allow overlapping classes in the same room</span>
                <p class="mt-0.5 text-xs text-gray-500">Check if multiple classes can run at the same time in this room.</p>
            </div>
        </label>
    </div>

    {{-- 4. Notes --}}
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-5">
        <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-600 text-sm font-bold text-white">4</span>
            <div>
                <h4 class="text-base font-bold text-gray-900">Notes</h4>
                <p class="text-sm text-gray-500">Optional notes for travel or internal use.</p>
            </div>
        </div>

        <div>
            <label for="travel_notes" class="mb-1.5 block text-sm font-bold text-gray-700">
                Travel notes <span class="font-normal text-gray-400">(optional)</span>
            </label>
            <textarea id="travel_notes" name="travel_notes" rows="2" placeholder="Travel-related details for this schedule"
                      class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('travel_notes') }}</textarea>
        </div>

        <div>
            <label for="notes" class="mb-1.5 block text-sm font-bold text-gray-700">
                General notes <span class="font-normal text-gray-400">(optional)</span>
            </label>
            <textarea id="notes" name="notes" rows="3" placeholder="Any additional notes or instructions"
                      class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-4">
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700">
            <i class="fas fa-save"></i> Create schedule(s)
        </button>
        <a href="{{ route('admin.class-schedules.index') }}"
           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
    </div>
</form>

<script>
let slotIndex = 1;

function toggleScheduleType() {
    const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
    const singleSchedule = document.getElementById('single-schedule');
    const multipleSchedules = document.getElementById('multiple-schedules');
    const singleDateInput = document.getElementById('class_date');
    const singleTimeInput = document.getElementById('start_time');

    if (scheduleType === 'single') {
        singleSchedule.classList.remove('hidden');
        multipleSchedules.classList.add('hidden');
        singleDateInput.required = true;
        singleTimeInput.required = true;
        singleDateInput.disabled = false;
        singleTimeInput.disabled = false;
        document.querySelectorAll('.schedule-date, .schedule-time').forEach(el => {
            el.required = false;
            el.disabled = true;
        });
    } else {
        singleSchedule.classList.add('hidden');
        multipleSchedules.classList.remove('hidden');
        singleDateInput.required = false;
        singleTimeInput.required = false;
        singleDateInput.disabled = true;
        singleTimeInput.disabled = true;
        singleDateInput.value = '';
        singleTimeInput.value = '';
        document.querySelectorAll('.schedule-date, .schedule-time').forEach(el => {
            el.required = true;
            el.disabled = false;
        });
    }
}

function addScheduleSlot() {
    const slotsContainer = document.getElementById('schedule-slots');
    const newSlot = document.createElement('div');
    newSlot.className = 'schedule-slot rounded-lg border border-gray-200 bg-slate-50 p-4';
    newSlot.innerHTML = `
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-bold text-gray-700">Date</label>
                <input type="date"
                       name="schedules[${slotIndex}][class_date]"
                       min="{{ date('Y-m-d') }}"
                       required
                       class="schedule-date w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-gray-700">Start time</label>
                <input type="time"
                       name="schedules[${slotIndex}][start_time]"
                       required
                       class="schedule-time w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            </div>
        </div>
        <button type="button" onclick="removeScheduleSlot(this)" class="mt-3 text-sm font-medium text-red-600 hover:text-red-800">
            <i class="fas fa-trash mr-1"></i> Remove
        </button>
    `;
    slotsContainer.appendChild(newSlot);
    slotIndex++;
}

function removeScheduleSlot(button) {
    const slots = document.querySelectorAll('.schedule-slot');
    if (slots.length <= 1) {
        return;
    }
    button.closest('.schedule-slot').remove();
}

function setupCustomToggle({ toggleId, wrapId, inputId, selectId, addLabel, removeLabel, clearCheckboxes }) {
    const toggle = document.getElementById(toggleId);
    const wrap = document.getElementById(wrapId);
    const input = document.getElementById(inputId);
    const select = selectId ? document.getElementById(selectId) : null;
    if (!toggle || !wrap || !input) {
        return;
    }

    const icon = toggle.querySelector('i');
    const label = toggle.querySelector('span');

    function setOpen(open) {
        wrap.classList.toggle('hidden', !open);
        if (icon) {
            icon.className = 'fas ' + (open ? 'fa-times' : 'fa-plus') + ' text-xs';
        }
        if (label) {
            label.textContent = open ? removeLabel : addLabel;
        }
        if (!open) {
            input.value = '';
        }
    }

    toggle.addEventListener('click', function () {
        const willOpen = wrap.classList.contains('hidden');
        setOpen(willOpen);
        if (willOpen) {
            if (select) {
                select.value = '';
            }
            if (clearCheckboxes) {
                document.querySelectorAll('input[name="location_ids[]"]').forEach(function (cb) {
                    cb.checked = false;
                });
            }
            input.focus();
        }
    });

    select?.addEventListener('change', function () {
        if (select.value) {
            setOpen(false);
        }
    });

    if (clearCheckboxes) {
        document.querySelectorAll('input[name="location_ids[]"]').forEach(function (cb) {
            cb.addEventListener('change', function () {
                if (document.querySelector('input[name="location_ids[]"]:checked')) {
                    setOpen(false);
                }
            });
        });
    }
}

setupCustomToggle({
    toggleId: 'toggle-custom-location',
    wrapId: 'custom-location-wrap',
    inputId: 'location',
    selectId: null,
    addLabel: 'Add custom location',
    removeLabel: 'Remove custom location',
    clearCheckboxes: true,
});

setupCustomToggle({
    toggleId: 'toggle-custom-instructor',
    wrapId: 'custom-instructor-wrap',
    inputId: 'instructor',
    selectId: 'instructor_id',
    addLabel: 'Add custom instructor',
    removeLabel: 'Remove custom instructor',
    clearCheckboxes: false,
});

document.addEventListener('DOMContentLoaded', toggleScheduleType);
</script>
@endsection
