@extends('admin.layouts.master')

@section('title', 'Quiz Attempt Review')
@section('page-title', 'Quiz Attempt Review')

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.classes.blended-progress', $service) }}" class="text-sm font-medium text-gray-600 hover:text-green-700">
        ← Back to student progress
    </a>
    <h3 class="mt-3 text-xl font-semibold text-gray-900">{{ $student->name }} — {{ $courseModule->title }}</h3>
    <p class="mt-1 text-sm text-gray-500">
        Attempt score: <strong class="{{ $attempt->passed ? 'text-emerald-700' : 'text-red-600' }}">{{ $attempt->score }}%</strong>
        · {{ $attempt->passed ? 'Passed' : 'Failed' }}
        · {{ optional($attempt->created_at)->format('M j, Y g:i A') }}
    </p>
</div>

@if($attempts->count() > 1)
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach($attempts as $item)
            <a href="{{ route('admin.classes.blended-progress.attempt', [$service, $student, $courseModule, $item]) }}"
               class="rounded-full px-3 py-1 text-xs font-semibold {{ $item->id === $attempt->id ? 'bg-slate-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                #{{ $loop->iteration }} — {{ $item->score }}%
            </a>
        @endforeach
    </div>
@endif

<div class="space-y-4">
    @foreach($review as $item)
        <div class="rounded-xl border p-4 {{ $item['is_correct'] ? 'border-emerald-200 bg-emerald-50/50' : 'border-red-200 bg-red-50/50' }}">
            <div class="mb-3 flex items-start justify-between gap-3">
                <p class="font-semibold text-gray-900">{{ $loop->iteration }}. {{ $item['question'] }}</p>
                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $item['is_correct'] ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                    {{ $item['is_correct'] ? 'Correct' : 'Wrong' }}
                </span>
            </div>
            <div class="space-y-2">
                @foreach($item['options'] as $option)
                    @php
                        $selected = in_array($option, $item['selected'] ?? [], true);
                        $correctOpt = in_array($option, $item['correct_answer'] ?? [], true);
                    @endphp
                    <div @class([
                        'rounded-lg border px-3 py-2 text-sm',
                        'border-emerald-400 bg-emerald-100 font-semibold' => $correctOpt,
                        'border-red-400 bg-red-100 font-semibold' => $selected && ! $correctOpt,
                        'border-gray-200 bg-white' => ! $selected && ! $correctOpt,
                    ])>
                        {{ $option }}
                        @if($selected)<span class="ml-2 text-xs">Student answer</span>@endif
                        @if($correctOpt && ! $selected)<span class="ml-2 text-xs">Correct</span>@endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
