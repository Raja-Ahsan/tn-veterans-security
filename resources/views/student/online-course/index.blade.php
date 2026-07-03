@extends('student.layouts.master')

@section('title', 'Online Course — '.$service->title)

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">{{ $service->title }}</h1>
    <p class="text-gray-600 mt-2">Complete all modules and score 90% or higher on each quiz.</p>
    @if($eligible)
        <div class="mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">You have completed the online portion and are eligible for in-person testing.</div>
    @endif
</div>

<div class="space-y-4">
    @foreach($modules as $index => $module)
        @php $mp = $progress->get($module->id); $locked = !app(\App\Services\BlendedCourseService::class)->canAccessModule(auth('student')->user(), $module, $progress, $modules); @endphp
        <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between {{ $locked ? 'opacity-60' : '' }}">
            <div>
                <h3 class="font-bold text-lg">Module {{ $index + 1 }}: {{ $module->title }}</h3>
                @if($mp?->is_completed)
                    <span class="text-green-600 text-sm">Completed — Best score: {{ $mp->best_score }}%</span>
                @elseif($locked)
                    <span class="text-gray-500 text-sm">Locked — pass previous module first</span>
                @else
                    <span class="text-blue-600 text-sm">Available</span>
                @endif
            </div>
            @if(!$locked)
                <a href="{{ route('student.online-course.module', [$service, $module]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg">Open</a>
            @endif
        </div>
    @endforeach
</div>
@endsection
