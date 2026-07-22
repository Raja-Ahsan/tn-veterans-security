@extends('admin.layouts.master')

@section('title', 'Course Modules')
@section('page-title', 'Online Modules — '.$service->title)

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Online Modules &amp; Quizzes</h3>
        <p class="mt-1 text-sm text-gray-500">Class: <span class="font-medium text-gray-800">{{ $service->title }}</span></p>
        <p class="mt-0.5 text-sm text-gray-500">Students must pass each module quiz at 90% before completing the blended course.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.classes.edit', $service) }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            ← Back to class
        </a>
        <a href="{{ route('admin.classes.blended-progress', $service) }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
            <i class="fas fa-user-graduate"></i> Student progress
        </a>
        <a href="{{ route('admin.classes.course-modules.create', $service) }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
            <i class="fas fa-plus"></i> Add Module
        </a>
    </div>
</div>

@if($modules->count() > 1)
<form method="POST" action="{{ route('admin.classes.course-modules.reorder', $service) }}" class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    @csrf
    <p class="mb-1 text-sm font-semibold text-gray-800">Module order</p>
    <p class="mb-3 text-xs text-gray-500">Change the numbers, then save. Lower numbers appear first for students.</p>
    <div class="flex flex-wrap gap-3">
        @foreach($modules as $module)
            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
                <span class="font-medium text-gray-700">{{ Str::limit($module->title, 28) }}</span>
                <input type="number" name="positions[{{ $module->id }}]" value="{{ $module->order }}" min="1"
                       class="w-16 rounded border border-gray-300 px-2 py-1 text-sm" aria-label="Order for {{ $module->title }}">
            </label>
        @endforeach
    </div>
    <button type="submit" class="mt-3 rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">Save order</button>
</form>
@endif

<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-20 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Module</th>
                    <th class="w-40 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Quiz</th>
                    <th class="w-28 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="w-44 px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($modules as $module)
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-700">
                            #{{ $module->order }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-gray-900">{{ $module->title }}</div>
                                    <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        @if($module->video_url)
                                            <span class="inline-flex items-center gap-1"><i class="fas fa-video"></i> Video</span>
                                        @endif
                                        @if($module->content)
                                            <span class="inline-flex items-center gap-1"><i class="fas fa-align-left"></i> Content</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">
                                {{ $module->quiz_questions_count }} {{ Str::plural('question', $module->quiz_questions_count) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            @if($module->is_active)
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Active</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="inline-flex items-center justify-end gap-2">
                                <a href="{{ route('admin.classes.course-modules.edit', [$service, $module]) }}"
                                   class="inline-flex items-center gap-1 rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.classes.course-modules.destroy', [$service, $module]) }}"
                                      method="POST"
                                      class="m-0 inline"
                                      onsubmit="return confirm('Delete this module?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 rounded-md border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <i class="fas fa-book-open mb-3 text-3xl text-gray-300"></i>
                            <p class="text-gray-500">No modules yet. Blended courses need modules with quiz questions (90% to pass).</p>
                            <a href="{{ route('admin.classes.course-modules.create', $service) }}" class="mt-3 inline-flex items-center gap-1.5 font-medium text-green-600 hover:underline">
                                <i class="fas fa-plus"></i> Add your first module
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
