@extends('student.layouts.master')

@section('title', 'Book Class - ' . $service->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('student.available-classes', $service->id) }}" class="text-blue-600 hover:underline inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left"></i> Back to Available Classes
    </a>
</div>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Book: {{ $service->title }}</h1>
    @if($schedule)
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center gap-4 flex-wrap">
                <div>
                    <span class="text-sm text-gray-600">Date:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ $schedule->class_date->format('F d, Y') }}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Time:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</span>
                    @if($schedule->end_time)
                        <span class="text-gray-500"> - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</span>
                    @endif
                </div>
                @if($schedule->location)
                <div>
                    <span class="text-sm text-gray-600">Location:</span>
                    <span class="font-semibold text-[var(--primary-color)] ml-2">
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $schedule->location }}
                    </span>
                </div>
                @endif
                <div>
                    <span class="text-sm text-gray-600">Available Spots:</span>
                    <span class="font-semibold text-green-600 ml-2">{{ $schedule->getAvailableSpots() }}</span>
                </div>
            </div>
        </div>
    @endif
</div>

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Booking Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Booking Information</h2>
            <p class="text-sm text-gray-600 mb-6">This booking is for your student profile only — one session per class. Anyone else who wants this class should sign up with their own account.</p>
            
            <form method="POST" action="{{ route('student.booking.store') }}">
                @csrf
                
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                
                <!-- Class Schedule Selection -->
                <div class="mb-6">
                    <label for="class_schedule_id" class="block text-gray-700 text-sm font-bold mb-2">
                        Select Class Schedule <span class="text-red-500">*</span>
                    </label>
                    @if($schedule)
                        <input type="hidden" name="class_schedule_id" value="{{ $schedule->id }}">
                        <div class="bg-gray-50 border border-gray-300 rounded px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $schedule->class_date->format('F d, Y') }}</div>
                            <div class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                                @if($schedule->end_time)
                                    - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                @endif
                                ({{ $schedule->duration_hours }} {{ Str::plural('hour', $schedule->duration_hours) }})
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $schedule->getAvailableSpots() }} spot(s) available
                            </div>
                        </div>
                    @else
                        <select id="class_schedule_id" 
                                name="class_schedule_id" 
                                required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select a class schedule</option>
                            @foreach($schedules as $scheduleOption)
                                <option value="{{ $scheduleOption->id }}" {{ old('class_schedule_id') == $scheduleOption->id ? 'selected' : '' }}>
                                    {{ $scheduleOption->class_date->format('F d, Y') }} at {{ \Carbon\Carbon::parse($scheduleOption->start_time)->format('h:i A') }}
                                    @if($scheduleOption->location) - {{ $scheduleOption->location }}@endif
                                    ({{ $scheduleOption->getAvailableSpots() }} spots available)
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('class_schedule_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Notes (Optional) -->
                <div class="mb-6">
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">
                        Additional Notes (Optional)
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="4"
                              placeholder="Any special requirements or notes..."
                              maxlength="1000"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-calendar-check mr-2"></i> Proceed to Payment
                </button>
            </form>
        </div>
    </div>
    
    <!-- Pricing Summary -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Pricing Summary</h3>
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Price:</span>
                    <span class="font-semibold text-gray-900">${{ number_format($service->price ?? 0, 2) }}</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700 font-semibold">Total Amount:</span>
                        <span class="font-bold text-gray-900 text-lg">${{ number_format($service->price ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Deposit Required:</span>
                        <span class="font-bold text-green-600">${{ number_format($service->deposit_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between mt-2 pt-2 border-t">
                        <span class="text-gray-600 text-sm">Remaining Balance:</span>
                        <span class="font-semibold text-gray-700 text-sm">${{ number_format(($service->price ?? 0) - ($service->deposit_amount ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded p-4">
                <p class="text-xs text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    A deposit is required to confirm your booking. The remaining balance can be paid later.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
