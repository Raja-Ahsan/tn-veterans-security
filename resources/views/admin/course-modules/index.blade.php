@extends('admin.layouts.master')

@section('title', 'Course Modules')
@section('page-title', 'Online Modules — '.$service->title)

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-3">
    <a href="{{ route('admin.services.edit', $service) }}" class="text-blue-600 hover:underline">← Back to class</a>
    <div class="flex gap-3">
        <a href="{{ route('admin.services.blended-progress', $service) }}" class="text-blue-600 hover:underline">Student progress</a>
        <a href="{{ route('admin.services.course-modules.create', $service) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg">Add Module</a>
    </div>
</div>
@if($modules->count() > 1)
<form method="POST" action="{{ route('admin.services.course-modules.reorder', $service) }}" class="bg-white rounded-lg shadow p-4 mb-4">
    @csrf
    <p class="text-sm font-semibold mb-2">Module order (drag numbers or edit)</p>
    <div class="flex flex-wrap gap-3">
        @foreach($modules as $module)
            <label class="text-sm flex items-center gap-2">#{{ $module->order }} {{ Str::limit($module->title, 24) }}
                <input type="number" name="positions[{{ $module->id }}]" value="{{ $module->order }}" min="1" class="border rounded w-16 px-2 py-1" aria-label="Order for {{ $module->title }}">
            </label>
        @endforeach
    </div>
    <button type="submit" class="mt-3 text-sm bg-blue-600 text-white px-3 py-1 rounded">Save order</button>
</form>
@endif
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50"><tr>
            <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Order</th>
            <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Questions</th>
            <th class="px-6 py-3 text-left text-xs text-gray-500 uppercase">Active</th>
            <th class="px-6 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($modules as $module)
                <tr>
                    <td class="px-6 py-4">{{ $module->order }}</td>
                    <td class="px-6 py-4 font-medium">{{ $module->title }}</td>
                    <td class="px-6 py-4">{{ $module->quiz_questions_count }}</td>
                    <td class="px-6 py-4">{{ $module->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-6 py-4 text-right space-x-3">
                        <a href="{{ route('admin.services.course-modules.edit', [$service, $module]) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.services.course-modules.destroy', [$service, $module]) }}" method="POST" class="inline" onsubmit="return confirm('Delete module?')">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No modules yet. Blended courses require modules with 90% quiz passing.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
