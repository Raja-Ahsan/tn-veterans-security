@php
    $instructors = $instructors ?? collect();
    $selectedInstructorId = old($namePrefix.'.instructor_id', $selectedInstructorId ?? ($schedule->instructor_id ?? null));
    if (! $selectedInstructorId && ! empty($schedule->instructor ?? null)) {
        $selectedInstructorId = optional($instructors->firstWhere('name', $schedule->instructor))->id;
    }
@endphp
<option value="">Select instructor</option>
@foreach($instructors as $instructor)
    <option value="{{ $instructor->id }}" @selected((string) $selectedInstructorId === (string) $instructor->id)>
        {{ $instructor->name }}
    </option>
@endforeach
