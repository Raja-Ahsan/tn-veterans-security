@extends('admin.layouts.master')

@section('title', 'State Forms')
@section('page-title', 'Guard Training Forms (IN-1144)')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Certificate of Successful Completion</h3>
        <p class="mt-1 text-sm text-gray-500">TN Form IN-1144 style. Auto-fills student details; admin completes the rest, then publishes to the student.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <form method="GET" class="flex gap-2">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search student, form #…"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">All statuses</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="published" @selected(request('status') === 'published')>Published</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Search</button>
        </form>
        <a href="{{ route('admin.guard-training-forms.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
            <i class="fas fa-plus"></i> New Form
        </a>
    </div>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Form</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($forms as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-mono text-xs font-semibold text-gray-800">{{ $item->form_number }}</p>
                            <p class="text-xs text-gray-500">{{ optional($item->updated_at)->format('M j, Y g:i A') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900">{{ $item->studentDisplayName() ?: $item->student?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->student?->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->registrationTypeLabel() }}</td>
                        <td class="px-4 py-3">
                            @if($item->isPublished())
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Published</span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">Draft</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.guard-training-forms.show', $item) }}" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">View</a>
                                <a href="{{ route('admin.guard-training-forms.edit', $item) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                                <a href="{{ route('admin.guard-training-forms.print', $item) }}" target="_blank" class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100">Download</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No state forms yet.
                            <a href="{{ route('admin.guard-training-forms.create') }}" class="mt-2 block font-medium text-green-600 hover:underline">Create the first form</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($forms->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">{{ $forms->links() }}</div>
    @endif
</div>
@endsection
