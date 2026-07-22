@php
    $step = $step ?? '';
    $title = $title ?? '';
    $hint = $hint ?? null;
@endphp
<div class="mb-4 flex items-start gap-3 border-b border-gray-100 pb-3">
    @if($step !== '')
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">{{ $step }}</span>
    @endif
    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
        @if($hint)
            <p class="mt-0.5 text-sm text-gray-500">{{ $hint }}</p>
        @endif
    </div>
</div>
