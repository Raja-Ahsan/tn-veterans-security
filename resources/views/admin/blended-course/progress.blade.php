@extends('admin.layouts.master')

@section('title', 'Blended Course Progress')
@section('page-title', 'Student Progress — '.$service->title)

@section('content')
@php
    $studentCount = $bookings->filter(fn ($b) => $b->student)->count();
    $moduleCount = $modules->count();
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Student Progress</h3>
        <p class="mt-1 text-sm text-gray-500">Class: <span class="font-medium text-gray-800">{{ $service->title }}</span></p>
        <p class="mt-0.5 text-sm text-gray-500">Track online quizzes and mark in-person test results.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.classes.edit', $service) }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            ← Back to class
        </a>
        <a href="{{ route('admin.classes.course-modules.index', $service) }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
            <i class="fas fa-book-open"></i> Manage modules
        </a>
    </div>
</div>

{{-- Summary --}}
<div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Modules</p>
        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $moduleCount }}</p>
        <p class="text-xs text-gray-500">Active online modules</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Enrolled students</p>
        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $studentCount }}</p>
        <p class="text-xs text-gray-500">With paid booking</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Pass requirement</p>
        <p class="mt-1 text-2xl font-bold text-gray-900">90%</p>
        <p class="text-xs text-gray-500">Quiz score to complete a module</p>
    </div>
</div>

@if($moduleCount === 0)
    <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 px-6 py-10 text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
            <i class="fas fa-book-open text-xl"></i>
        </div>
        <h4 class="text-base font-semibold text-amber-950">No modules yet</h4>
        <p class="mx-auto mt-1 max-w-md text-sm text-amber-800">Add online modules &amp; quizzes first. After students enroll, their progress will show here.</p>
        <a href="{{ route('admin.classes.course-modules.create', $service) }}"
           class="mt-4 inline-flex items-center gap-1.5 rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
            <i class="fas fa-plus"></i> Add first module
        </a>
    </div>
@elseif($studentCount === 0)
    <div class="space-y-4">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-gray-900">Course modules</h4>
            <p class="mt-0.5 text-xs text-gray-500">These appear as columns once students enroll.</p>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($modules as $module)
                    <div class="inline-flex max-w-xs items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-700 text-xs font-bold text-white">{{ $loop->iteration }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $module->title }}</p>
                            <p class="text-[11px] text-gray-500">Order #{{ $module->order }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.classes.course-modules.index', $service) }}" class="mt-3 inline-block text-sm font-medium text-blue-600 hover:underline">
                Edit modules →
            </a>
        </div>

        <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center shadow-sm">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <h4 class="text-base font-semibold text-gray-800">No enrolled students yet</h4>
            <p class="mx-auto mt-2 max-w-xl text-sm text-gray-500">
                Students appear here only after their booking deposit is paid (or marked paid by admin).
            </p>
            <div class="mx-auto mt-5 max-w-lg rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-left text-sm text-blue-900">
                <p class="font-semibold">Easy enrollment steps</p>
                <ol class="mt-2 list-decimal space-y-1 pl-4 text-blue-800">
                    <li>Student books this blended class (or you create a booking).</li>
                    <li>Open <strong>Bookings</strong> → open that booking.</li>
                    <li>Click <strong>Mark Deposit Paid</strong> (or student pays deposit).</li>
                    <li>Refresh this page — student appears with module quiz columns.</li>
                </ol>
            </div>
            <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                <a href="{{ route('admin.bookings.index', ['q' => $service->title]) }}"
                   class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-calendar-check"></i> Find bookings for this class
                </a>
                <a href="{{ route('admin.students.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-users"></i> View students
                </a>
            </div>
        </div>
    </div>
@else
    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
        <div class="flex gap-3">
            <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
            <ul class="list-disc space-y-0.5 pl-4 text-blue-800">
                <li>Green score = module quiz passed (90%+).</li>
                <li><strong>Override</strong> marks complete without quiz.</li>
                <li><strong>Reset</strong> clears a failed attempt so the student can re-enroll / retry (update quiz questions first).</li>
                <li>Students get <strong>one quiz attempt</strong> per module unless you reset them.</li>
                <li>Use the in-person test form to record the final on-site result.</li>
            </ul>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Student</th>
                        @foreach($modules as $module)
                            <th class="min-w-[9rem] px-3 py-3 text-left" title="{{ $module->title }}">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-700 text-[10px] font-bold text-white">{{ $loop->iteration }}</span>
                                    <span class="max-w-[7rem] truncate text-xs font-semibold text-gray-800">{{ $module->title }}</span>
                                </span>
                            </th>
                        @endforeach
                        <th class="min-w-[9rem] px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">In-person</th>
                        <th class="min-w-[15rem] px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($bookings as $booking)
                        @php
                            $student = $booking->student;
                            if (! $student) {
                                continue;
                            }
                            $progress = $progressByStudent[$student->id] ?? collect();
                            $test = $testResults[$student->id] ?? null;
                        @endphp
                        <tr class="align-top hover:bg-gray-50">
                            <td class="sticky left-0 z-10 bg-white px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $student->name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->email }}</div>
                            </td>
                            @foreach($modules as $module)
                                @php $mp = $progress->get($module->id); @endphp
                                <td class="px-3 py-3">
                                    @if($mp?->is_completed)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">
                                            <i class="fas fa-check-circle"></i> {{ $mp->best_score ?? '—' }}%
                                        </span>
                                        @if($mp->admin_override)
                                            <div class="mt-1 text-[11px] font-medium text-amber-600">Override</div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                            Incomplete
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-3">
                                @if($test)
                                    @php
                                        $resultClass = match ($test->result) {
                                            'passed' => 'bg-green-100 text-green-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            default => 'bg-amber-100 text-amber-800',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $resultClass }}">
                                        {{ str_replace('_', ' ', $test->result) }}
                                    </span>
                                    @if($test->notes)
                                        <p class="mt-1 line-clamp-2 text-xs text-gray-500" title="{{ $test->notes }}">{{ $test->notes }}</p>
                                    @endif
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">Not marked</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-2">
                                    @foreach($modules as $module)
                                        <div class="rounded-md border border-gray-100 bg-gray-50 p-2">
                                            <p class="mb-1.5 truncate text-[11px] font-semibold text-gray-600" title="{{ $module->title }}">
                                                #{{ $loop->iteration }} {{ $module->title }}
                                            </p>
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                @php $latestAttempt = $latestAttempts[$student->id][$module->id] ?? null; @endphp
                                                @if($latestAttempt)
                                                    <a href="{{ route('admin.classes.blended-progress.attempt', [$service, $student, $module, $latestAttempt]) }}"
                                                       class="inline-flex items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-2 py-1 text-[11px] font-semibold text-blue-700 hover:bg-blue-100">
                                                        Review
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('admin.classes.blended-progress.override', [$service, $student, $module]) }}" class="m-0 inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-md border border-green-200 bg-green-50 px-2 py-1 text-[11px] font-semibold text-green-700 hover:bg-green-100">
                                                        Override
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.classes.blended-progress.reset', [$service, $student, $module]) }}" class="m-0 inline js-confirm-reset">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-md border border-red-200 bg-red-50 px-2 py-1 text-[11px] font-semibold text-red-700 hover:bg-red-100">
                                                        Reset
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                    <form method="POST" action="{{ route('admin.classes.blended-progress.in-person-test', [$service, $student]) }}" class="rounded-md border border-blue-100 bg-blue-50/60 p-2">
                                        @csrf
                                        <input type="hidden" name="class_schedule_id" value="{{ $booking->class_schedule_id }}">
                                        <p class="mb-1.5 text-[11px] font-semibold text-blue-800">In-person test</p>
                                        <select name="result" class="mb-1.5 w-full rounded border border-gray-300 bg-white px-2 py-1.5 text-xs" required>
                                            <option value="">Select result…</option>
                                            <option value="passed" {{ ($test->result ?? '') === 'passed' ? 'selected' : '' }}>Passed</option>
                                            <option value="failed" {{ ($test->result ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                                            <option value="needs_remediation" {{ ($test->result ?? '') === 'needs_remediation' ? 'selected' : '' }}>Needs remediation</option>
                                        </select>
                                        <input type="text" name="notes" value="{{ $test->notes ?? '' }}" placeholder="Notes (optional)" class="mb-1.5 w-full rounded border border-gray-300 bg-white px-2 py-1.5 text-xs">
                                        <button type="submit" class="inline-flex w-full items-center justify-center gap-1 rounded-md bg-blue-600 px-2 py-1.5 text-[11px] font-semibold text-white hover:bg-blue-700">
                                            Save test result
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<script>
document.querySelectorAll('form.js-confirm-reset').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var f = form;
        if (typeof Swal === 'undefined') {
            if (confirm('Reset this module progress for the student?')) f.submit();
            return;
        }
        Swal.fire({
            title: 'Reset module progress?',
            text: 'This clears the student’s quiz progress for this module.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, reset',
        }).then(function (result) {
            if (result.isConfirmed) f.submit();
        });
    });
});
</script>
@endsection
