@php
    $locations = $locations ?? collect();
    $instructors = $instructors ?? collect();
    $selectedLocation = old('location', $selectedLocation ?? '');
@endphp
<option value="">No Specific Location</option>
@foreach($locations as $loc)
    <option value="{{ $loc->name }}" @selected($selectedLocation === $loc->name)>
        {{ $loc->display_name }}{{ $loc->address ? ' — '.$loc->address : '' }}
    </option>
@endforeach
