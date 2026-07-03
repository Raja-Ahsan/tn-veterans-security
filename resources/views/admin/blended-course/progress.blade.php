@extends('admin.layouts.master')

@section('title', 'Blended Course Progress')
@section('page-title', 'Student Progress — '.$service->title)

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-3">
    <a href="{{ route('admin.services.edit', $service) }}" class="text-blue-600 hover:underline">← Back to class</a>
    <a href="{{ route('admin.services.course-modules.index', $service) }}" class="text-blue-600 hover:underline">Manage modules</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">Student</th>
                @foreach($modules as $module)
                    <th class="px-4 py-3 text-left">{{ $module->title }}</th>
                @endforeach
                <th class="px-4 py-3 text-left">In-Person Test</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($bookings as $booking)
                @php $student = $booking->student; $progress = $progressByStudent[$student->id] ?? collect(); $test = $testResults[$student->id] ?? null; @endphp
                @if($student)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $student->name }}<br><span class="text-gray-500 text-xs">{{ $student->email }}</span></td>
                    @foreach($modules as $module)
                        @php $mp = $progress->get($module->id); @endphp
                        <td class="px-4 py-3">
                            @if($mp?->is_completed)
                                <span class="text-green-700">{{ $mp->best_score ?? '—' }}%</span>
                                @if($mp->admin_override)<span class="text-xs text-amber-600">(override)</span>@endif
                            @else
                                <span class="text-gray-400">Locked / incomplete</span>
                            @endif
                        </td>
                    @endforeach
                    <td class="px-4 py-3">
                        @if($test)
                            <span class="font-semibold capitalize">{{ str_replace('_', ' ', $test->result) }}</span>
                        @else
                            <span class="text-gray-400">Not marked</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 space-y-2 min-w-[220px]">
                        @foreach($modules as $module)
                            <div class="flex gap-2 text-xs">
                                <form method="POST" action="{{ route('admin.services.blended-progress.override', [$service, $student, $module]) }}">@csrf<button class="text-green-600">Override {{ $module->order }}</button></form>
                                <form method="POST" action="{{ route('admin.services.blended-progress.reset', [$service, $student, $module]) }}">@csrf<button class="text-red-600" onclick="return confirm('Reset module?')">Reset</button></form>
                            </div>
                        @endforeach
                        <form method="POST" action="{{ route('admin.services.blended-progress.in-person-test', [$service, $student]) }}" class="mt-2 border-t pt-2">
                            @csrf
                            <input type="hidden" name="class_schedule_id" value="{{ $booking->class_schedule_id }}">
                            <select name="result" class="border rounded text-xs w-full mb-1" required>
                                <option value="">Mark in-person test…</option>
                                <option value="passed">Passed</option>
                                <option value="failed">Failed</option>
                                <option value="needs_remediation">Needs remediation</option>
                            </select>
                            <input type="text" name="notes" placeholder="Notes" class="border rounded text-xs w-full mb-1">
                            <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs w-full">Save test result</button>
                        </form>
                    </td>
                </tr>
                @endif
            @empty
                <tr><td colspan="{{ $modules->count() + 3 }}" class="px-6 py-8 text-center text-gray-500">No enrolled students yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
