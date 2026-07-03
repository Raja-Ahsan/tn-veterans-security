@extends('admin.layouts.master')

@section('title', 'Edit Class Schedule')
@section('page-title', 'Edit Class Schedule')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.class-schedules.update', $classSchedule) }}">
        @csrf
        @method('PUT')

        <!-- Service Selection -->
        <div class="mb-4">
            <label for="service_id" class="block text-gray-700 text-sm font-bold mb-2">Service (Class) <span class="text-red-500">*</span></label>
            <select id="service_id" name="service_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Select a Service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $classSchedule->service_id) == $service->id ? 'selected' : '' }}>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Class Date -->
            <div class="mb-4">
                <label for="class_date" class="block text-gray-700 text-sm font-bold mb-2">Class Date <span class="text-red-500">*</span></label>
                <input type="date" 
                       id="class_date" 
                       name="class_date" 
                       value="{{ old('class_date', $classSchedule->class_date->format('Y-m-d')) }}"
                       required 
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
                       value="{{ old('start_time', \Carbon\Carbon::parse($classSchedule->start_time)->format('H:i')) }}"
                       required 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('start_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Duration Hours -->
            <div class="mb-4">
                <label for="duration_hours" class="block text-gray-700 text-sm font-bold mb-2">Duration (Hours) <span class="text-red-500">*</span></label>
                <input type="number" 
                       id="duration_hours" 
                       name="duration_hours" 
                       value="{{ old('duration_hours', $classSchedule->duration_hours) }}"
                       min="1"
                       required 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
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
                       value="{{ old('max_students', $classSchedule->max_students) }}"
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
                       value="{{ old('min_students', $classSchedule->min_students) }}"
                       min="1"
                       max="4"
                       required 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('min_students')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Students (Read-only) -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Current Students</label>
                <input type="text" 
                       value="{{ $classSchedule->current_students }}" 
                       readonly
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-100 cursor-not-allowed">
                <p class="text-xs text-gray-500 mt-1">Currently enrolled</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Location -->
            <div class="mb-4">
                <label for="location_id" class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                <select id="location_id" name="location_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">— Select location —</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id', $classSchedule->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->display_name }}</option>
                    @endforeach
                </select>
                <input type="text" id="location" name="location" value="{{ old('location', $classSchedule->location) }}" placeholder="Or custom location" class="mt-2 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('location_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Room -->
            <div class="mb-4">
                <label for="room" class="block text-gray-700 text-sm font-bold mb-2">Room</label>
                <input type="text" 
                       id="room" 
                       name="room" 
                       value="{{ old('room', $classSchedule->room) }}"
                       placeholder="Room name or number"
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
                        <option value="{{ $inst->id }}" {{ old('instructor_id', $classSchedule->instructor_id) == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                    @endforeach
                </select>
                <input type="text" id="instructor" name="instructor" value="{{ old('instructor', $classSchedule->instructor) }}" placeholder="Or custom instructor name" class="mt-2 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('instructor_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Status -->
        <div class="mb-4">
            <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status <span class="text-red-500">*</span></label>
            <select id="status" name="status" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="scheduled" {{ old('status', $classSchedule->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="full" {{ old('status', $classSchedule->status) === 'full' ? 'selected' : '' }}>Full</option>
                <option value="cancelled" {{ old('status', $classSchedule->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="completed" {{ old('status', $classSchedule->status) === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Can Overlap -->
        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="can_overlap" 
                       value="1"
                       {{ old('can_overlap', $classSchedule->can_overlap) ? 'checked' : '' }}
                       class="mr-2">
                <span class="text-gray-700 text-sm font-bold">Allow overlapping classes in the same room</span>
            </label>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="admin_override_capacity" value="1" {{ old('admin_override_capacity', $classSchedule->admin_override_capacity) ? 'checked' : '' }} class="mr-2">
                <span class="text-gray-700 text-sm font-bold">Admin override capacity (allow over-enrollment)</span>
            </label>
        </div>

        <div class="mb-4">
            <label for="travel_notes" class="block text-gray-700 text-sm font-bold mb-2">Travel Notes</label>
            <textarea id="travel_notes" name="travel_notes" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('travel_notes', $classSchedule->travel_notes) }}</textarea>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
            <textarea id="notes" 
                      name="notes" 
                      rows="3"
                      placeholder="Any additional notes or instructions"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes', $classSchedule->notes) }}</textarea>
            @error('notes')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                <i class="fas fa-save mr-2"></i> Update Schedule
            </button>
            <a href="{{ route('admin.class-schedules.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
