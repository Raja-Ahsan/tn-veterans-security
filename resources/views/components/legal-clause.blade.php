@props(['n'])

<div {{ $attributes->class('flex flex-col gap-1 sm:flex-row sm:gap-3 sm:items-start') }}>
    <span class="font-bold text-[var(--text-color)] shrink-0 sm:min-w-[2.85rem]">{{ $n }}</span>
    <div class="min-w-0 flex-1">{{ $slot }}</div>
</div>
