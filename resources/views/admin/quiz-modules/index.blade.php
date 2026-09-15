@extends('admin.layouts.master')

@section('title', 'Quiz Modules')
@section('page-title', 'Quiz Modules')

@section('content')
@php
    $tabMeta = [
        'blended' => [
            'label' => 'Blended',
            'icon' => 'fa-layer-group',
            'blurb' => 'Online modules plus in-person training / testing.',
            'active' => 'border-cyan-500 text-cyan-800 bg-cyan-50',
            'badge' => 'bg-cyan-100 text-cyan-800',
            'typeBadge' => 'bg-cyan-100 text-cyan-800',
            'typeIcon' => 'fa-layer-group',
        ],
        'in-person' => [
            'label' => 'In Person',
            'icon' => 'fa-chalkboard-user',
            'blurb' => 'Classroom only. Students take their test in class — no online quiz modules.',
            'active' => 'border-emerald-500 text-emerald-800 bg-emerald-50',
            'badge' => 'bg-emerald-100 text-emerald-800',
            'typeBadge' => 'bg-emerald-100 text-emerald-800',
            'typeIcon' => 'fa-chalkboard-user',
        ],
    ];
    $currentMeta = $tabMeta[$delivery] ?? $tabMeta['blended'];
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Build class modules by delivery type</h3>
        <p class="mt-1 text-sm text-gray-500">{{ $currentMeta['blurb'] }}</p>
    </div>
</div>

<div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
    <div class="flex gap-3">
        <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
        <ul class="list-disc space-y-0.5 pl-4 text-blue-800">
            <li>Check <span class="font-medium">Has online parts / quizzes</span> on a class to make it Blended, then add modules here.</li>
            <li>Uncheck that option for an In Person class — students test in the classroom.</li>
            <li>Each module can have multiple videos. Every video has its own quiz. Students must finish the video before the quiz, and pass before the next video.</li>
        </ul>
    </div>
</div>

{{-- Delivery tabs --}}
<div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2" role="tablist" aria-label="Class delivery type">
    @foreach($tabMeta as $format => $meta)
        @php $count = (int) ($deliveryCounts[$format] ?? 0); @endphp
        <a href="{{ route('admin.quiz-modules.index', ['delivery' => $format]) }}"
           role="tab"
           aria-selected="{{ $delivery === $format ? 'true' : 'false' }}"
           class="rounded-lg border-2 bg-white px-4 py-4 shadow-sm transition hover:shadow {{ $delivery === $format ? $meta['active'] : 'border-gray-200 text-gray-700 hover:border-gray-300' }}">
            <div class="flex items-center justify-between gap-3">
                <span class="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wide">
                    <i class="fas {{ $meta['icon'] }}"></i>
                    {{ $meta['label'] }}
                </span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $meta['badge'] }}">
                    {{ $count }}
                </span>
            </div>
            <p class="mt-2 text-xs {{ $delivery === $format ? 'opacity-90' : 'text-gray-500' }}">
                {{ $count }} {{ Str::plural('class', $count) }}
            </p>
        </a>
    @endforeach
</div>

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Modules</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                        {{ $delivery === 'in-person' ? 'Actions' : 'Build' }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($services as $service)
                    @php
                        $format = $service->deliveryFormat();
                        $typeMeta = $tabMeta[$format] ?? $tabMeta['blended'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $service->title }}</div>
                            @if($service->short_description)
                                <div class="mt-0.5 text-xs text-gray-500">{{ Str::limit($service->short_description, 90) }}</div>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($service->supportsOnlineModules())
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ $service->course_modules_count }} {{ Str::plural('module', $service->course_modules_count) }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">No online quiz</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $typeMeta['typeBadge'] }}">
                                <i class="fas {{ $typeMeta['typeIcon'] }} text-[10px]"></i> {{ $service->deliveryFormatLabel() }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            @if($service->supportsOnlineModules())
                                <a href="{{ route('admin.classes.course-modules.index', $service) }}"
                                   class="inline-flex items-center gap-1.5 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                    <i class="fas fa-book-open"></i>
                                    {{ $service->course_modules_count > 0 ? 'Edit modules' : 'Add module' }}
                                </a>
                            @else
                                <a href="{{ route('admin.classes.edit', $service) }}"
                                   class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-pen"></i>
                                    Edit class
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            @if($delivery === 'in-person')
                                <p>No in-person classes yet.</p>
                                <p class="mt-1 text-sm">Create a class without online parts, or turn off online quizzes on an existing class.</p>
                            @else
                                <p>No blended classes yet.</p>
                                <p class="mt-1 text-sm">Edit a class, check <span class="font-medium text-gray-700">Has online parts / quizzes</span>, then return here to add videos and quizzes.</p>
                            @endif
                            <a href="{{ route('admin.classes.index') }}" class="mt-3 inline-block font-medium text-green-600 hover:underline">Go to Classes</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
