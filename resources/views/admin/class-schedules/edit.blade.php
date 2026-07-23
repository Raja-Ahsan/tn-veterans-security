@extends('admin.layouts.master')

@section('title', 'Edit Class Schedule')
@section('page-title', 'Edit Class Schedule')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Edit class schedule</h3>
        <p class="mt-0.5 text-sm text-gray-500">
            {{ $classSchedule->service?->title ?? 'Class' }}
            · {{ $classSchedule->class_date->format('M j, Y') }}
            · {{ \Carbon\Carbon::parse($classSchedule->start_time)->format('g:i A') }}
        </p>
        <p class="mt-1 text-xs text-gray-400">Fields marked <span class="text-red-500">*</span> are required.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.class-schedules.show', $classSchedule) }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            View schedule
        </a>
        <a href="{{ route('admin.class-schedules.index') }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            ← Back to list
        </a>
    </div>
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

<form method="POST" action="{{ route('admin.class-schedules.update', $classSchedule) }}" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- 1. Class & date/time --}}
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-5">
        <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">1</span>
            <div>
                <h4 class="text-base font-bold text-gray-900">Class & date/time</h4>
                <p class="text-sm text-gray-500">Course, session date, and start time.</p>
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
                    <option value="{{ $service->id }}" {{ old('service_id', $classSchedule->service_id) == $service->id ? 'selected' : '' }}>
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

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="class_date" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Class date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="class_date" name="class_date"
                       value="{{ old('class_date', $classSchedule->class_date->format('Y-m-d')) }}" required
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('class_date') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                @error('class_date')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="start_time" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Start time <span class="text-red-500">*</span>
                </label>
                <input type="time" id="start_time" name="start_time"
                       value="{{ old('start_time', \Carbon\Carbon::parse($classSchedule->start_time)->format('H:i')) }}" required
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('start_time') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                @error('start_time')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="status" class="mb-1.5 block text-sm font-bold text-gray-700">
                Status <span class="text-red-500">*</span>
            </label>
            <select id="status" name="status" required
                    class="w-full max-w-xs rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                <option value="scheduled" {{ old('status', $classSchedule->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="full" {{ old('status', $classSchedule->status) === 'full' ? 'selected' : '' }}>Full</option>
                <option value="cancelled" {{ old('status', $classSchedule->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="completed" {{ old('status', $classSchedule->status) === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- 2. Duration & capacity --}}
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-5">
        <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">2</span>
            <div>
                <h4 class="text-base font-bold text-gray-900">Duration & capacity</h4>
                <p class="text-sm text-gray-500">Session length and enrollment limits.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="duration_hours" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Duration (hours) <span class="text-red-500">*</span>
                </label>
                <input type="number" id="duration_hours" name="duration_hours"
                       value="{{ old('duration_hours', $classSchedule->duration_hours) }}" min="1" required
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('duration_hours') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                @error('duration_hours')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="max_students" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Max students <span class="text-red-500">*</span>
                </label>
                <input type="number" id="max_students" name="max_students"
                       value="{{ old('max_students', $classSchedule->max_students) }}" min="1" max="10" required
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('max_students') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                @error('max_students')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="min_students" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Min students <span class="text-red-500">*</span>
                </label>
                <input type="number" id="min_students" name="min_students"
                       value="{{ old('min_students', $classSchedule->min_students) }}" min="1" max="4" required
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('min_students') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                @error('min_students')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-gray-700">Current students</label>
                <input type="text" value="{{ $classSchedule->current_students }}" readonly
                       class="w-full cursor-not-allowed rounded-md border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-500">
                <p class="mt-1 text-xs text-gray-500">Currently enrolled</p>
            </div>
        </div>

        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <input type="checkbox" name="admin_override_capacity" value="1"
                   {{ old('admin_override_capacity', $classSchedule->admin_override_capacity) ? 'checked' : '' }}
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
                <p class="text-sm text-gray-500">Where the class meets and who teaches it.</p>
            </div>
        </div>

        @php
            $selectedLocationId = old('location_id', $classSchedule->location_id);
            $locationText = old('location', $classSchedule->location);
            $selectedLocationName = optional($locations->firstWhere('id', (int) $selectedLocationId))->display_name;
            $showCustomLocation = filled($locationText) && (blank($selectedLocationId) || $locationText !== $selectedLocationName);

            $selectedInstructorId = old('instructor_id', $classSchedule->instructor_id);
            $instructorText = old('instructor', $classSchedule->instructor);
            $selectedInstructorName = optional($instructors->firstWhere('id', (int) $selectedInstructorId))->name;
            $showCustomInstructor = filled($instructorText) && (blank($selectedInstructorId) || $instructorText !== $selectedInstructorName);
        @endphp

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label for="location_id" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Location <span class="font-normal text-gray-400">(optional)</span>
                </label>
                <select id="location_id" name="location_id"
                        class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    <option value="">Select location</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ (string) $selectedLocationId === (string) $loc->id ? 'selected' : '' }}>{{ $loc->display_name }}</option>
                    @endforeach
                </select>
                <div id="custom-location-wrap" class="mt-2 {{ $showCustomLocation ? '' : 'hidden' }}">
                    <input type="text" id="location" name="location" value="{{ $showCustomLocation ? $locationText : '' }}"
                           placeholder="Enter custom location"
                           class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <button type="button" id="toggle-custom-location"
                        class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-green-700 hover:text-green-800">
                    <i class="fas {{ $showCustomLocation ? 'fa-times' : 'fa-plus' }} text-xs"></i>
                    <span>{{ $showCustomLocation ? 'Remove custom location' : 'Add custom location' }}</span>
                </button>
                @error('location_id')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="room" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Room <span class="font-normal text-gray-400">(optional)</span>
                </label>
                <input type="text" id="room" name="room" value="{{ old('room', $classSchedule->room) }}"
                       placeholder="Room name or number"
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
                        <option value="{{ $inst->id }}" {{ (string) $selectedInstructorId === (string) $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                    @endforeach
                </select>
                <div id="custom-instructor-wrap" class="mt-2 {{ $showCustomInstructor ? '' : 'hidden' }}">
                    <input type="text" id="instructor" name="instructor" value="{{ $showCustomInstructor ? $instructorText : '' }}"
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
            <input type="checkbox" name="can_overlap" value="1"
                   {{ old('can_overlap', $classSchedule->can_overlap) ? 'checked' : '' }}
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
                      class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('travel_notes', $classSchedule->travel_notes) }}</textarea>
        </div>

        <div>
            <label for="notes" class="mb-1.5 block text-sm font-bold text-gray-700">
                General notes <span class="font-normal text-gray-400">(optional)</span>
            </label>
            <textarea id="notes" name="notes" rows="3" placeholder="Any additional notes or instructions"
                      class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('notes', $classSchedule->notes) }}</textarea>
            @error('notes')
                <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-4">
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700">
            <i class="fas fa-save"></i> Save changes
        </button>
        <a href="{{ route('admin.class-schedules.index') }}"
           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
    </div>
</form>

<script>
(function () {
    function setupCustomToggle({ toggleId, wrapId, inputId, selectId, addLabel, removeLabel }) {
        const toggle = document.getElementById(toggleId);
        const wrap = document.getElementById(wrapId);
        const input = document.getElementById(inputId);
        const select = document.getElementById(selectId);
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
                input.focus();
            }
        });

        select?.addEventListener('change', function () {
            if (select.value) {
                setOpen(false);
            }
        });
    }

    setupCustomToggle({
        toggleId: 'toggle-custom-location',
        wrapId: 'custom-location-wrap',
        inputId: 'location',
        selectId: 'location_id',
        addLabel: 'Add custom location',
        removeLabel: 'Remove custom location',
    });

    setupCustomToggle({
        toggleId: 'toggle-custom-instructor',
        wrapId: 'custom-instructor-wrap',
        inputId: 'instructor',
        selectId: 'instructor_id',
        addLabel: 'Add custom instructor',
        removeLabel: 'Remove custom instructor',
    });
})();
</script>
@endsection
