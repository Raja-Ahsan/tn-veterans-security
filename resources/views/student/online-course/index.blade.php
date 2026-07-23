@extends('student.layouts.master')

@section('title', 'Online Course — '.$service->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('student.online-courses.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-[var(--brand)]">
        <i class="fas fa-arrow-left text-xs"></i> My Online Courses
    </a>
    <h1 class="mt-3 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $service->title }}</h1>
    <p class="mt-1 text-sm text-gray-500 sm:text-base">Complete all modules and pass each quiz (see each module for the required score).</p>

    @php
        $progressSummary = $progressSummary ?? ['completed' => 0, 'total' => $modules->count(), 'percent' => 0];
        $continueModule = $continueModule ?? null;
    @endphp

    <div class="mt-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-gray-900">
                    Course progress:
                    {{ $progressSummary['completed'] }} / {{ $progressSummary['total'] }} modules
                    ({{ $progressSummary['percent'] }}%)
                </p>
                <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full rounded-full bg-[var(--brand)] transition-all" style="width: {{ $progressSummary['percent'] }}%"></div>
                </div>
            </div>
            @if($continueModule)
                <a href="{{ route('student.online-course.module', [$service, $continueModule]) }}"
                   class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[var(--brand)] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                    <i class="fas fa-play text-xs"></i> Continue
                </a>
            @endif
        </div>
    </div>

    @if($eligible)
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            <i class="fas fa-check-circle mr-1"></i>
            You completed all modules and are eligible for in-person testing.
            @if(! empty($certificate))
                <a href="{{ route('student.certificates.show', $certificate) }}" class="ml-1 font-bold underline hover:no-underline">
                    View your certificate
                </a>
            @endif
        </div>
    @endif
</div>

<div class="space-y-3">
    @forelse($modules as $index => $module)
        @php
            $mp = $progress->get($module->id);
            $locked = ! app(\App\Services\BlendedCourseService::class)->canAccessModule(auth('student')->user(), $module, $progress, $modules);
            $completed = (bool) ($mp?->is_completed);
            $failed = ! $completed
                && ! $locked
                && ! (bool) ($mp?->admin_override)
                && (int) ($mp?->attempts ?? 0) > 0;
        @endphp
        <div class="flex flex-col gap-3 rounded-xl border p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5
            {{ $failed ? 'border-amber-200 bg-amber-50/60' : 'border-gray-200 bg-white' }}
            {{ $locked ? 'opacity-70' : '' }}">
            <div class="flex min-w-0 items-start gap-3">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold
                    {{ $completed ? 'bg-emerald-100 text-emerald-700' : ($failed ? 'bg-amber-100 text-amber-800' : ($locked ? 'bg-gray-100 text-gray-400' : 'bg-slate-800 text-white')) }}">
                    {{ $index + 1 }}
                </span>
                <div class="min-w-0">
                    <h3 class="font-bold text-gray-900">{{ $module->title }}</h3>
                    @if($completed)
                        <p class="mt-0.5 text-sm text-emerald-700">Completed — best score {{ $mp->best_score }}%</p>
                    @elseif($failed)
                        <p class="mt-0.5 text-sm text-amber-800">
                            Failed — best score {{ $mp->best_score }}% · contact admin to re-enroll / reset
                        </p>
                    @elseif($locked)
                        <p class="mt-0.5 text-sm text-gray-500">Locked — pass previous module first</p>
                    @else
                        <p class="mt-0.5 text-sm text-blue-600">Available</p>
                    @endif
                </div>
            </div>
            @if(! $locked)
                <a href="{{ route('student.online-course.module', [$service, $module]) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold
                        {{ $failed ? 'border border-amber-300 bg-white text-amber-950 hover:bg-amber-100' : 'bg-[var(--brand)] text-white hover:bg-[var(--brand-dark)]' }}">
                    @if($completed)
                        Review
                    @elseif($failed)
                        View details
                    @else
                        Open
                    @endif
                </a>
            @endif
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center text-gray-500">
            No modules have been added for this course yet.
        </div>
    @endforelse
</div>
@endsection
