@php
    $locations = $locations ?? collect();
    $selectedLocationId = old($namePrefix.'.location_id', $selectedLocationId ?? ($schedule->location_id ?? null));
    // Fall back to matching by stored location name for legacy rows.
    if (! $selectedLocationId && ! empty($schedule->location ?? null)) {
        $selectedLocationId = optional($locations->firstWhere('name', $schedule->location))->id;
    }
@endphp
<option value="">No Specific Location</option>
@foreach($locations as $loc)
    <option value="{{ $loc->id }}" @selected((string) $selectedLocationId === (string) $loc->id)>
        {{ $loc->display_name }}
    </option>
@endforeach
