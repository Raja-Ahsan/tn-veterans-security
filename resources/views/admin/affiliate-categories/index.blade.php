@extends('admin.layouts.master')

@section('title', 'Affiliate Categories')
@section('page-title', 'Affiliate Categories')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Affiliate Categories</h3>
        <p class="mt-1 text-sm text-gray-500">Sections on the Affiliated Services and NRA pages (e.g. Veteran Owned…).</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.affiliates.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            <i class="fas fa-handshake"></i> Affiliates
        </a>
        <a href="{{ route('admin.affiliate-categories.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
            <i class="fas fa-plus"></i> Add Category
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Page</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Partners in this section</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50/80 {{ ! $category->is_active ? 'opacity-60' : '' }}">
                        <td class="px-4 py-4 font-semibold text-gray-900 sm:px-6">{{ $category->name }}</td>
                        <td class="px-4 py-4 text-gray-700">
                            {{ $category->page === 'nra' ? 'NRA Services' : 'Affiliated Services' }}
                        </td>
                        <td class="px-4 py-4">
                            @if($category->affiliates->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 max-w-md">
                                    @foreach($category->affiliates as $affiliate)
                                        <a href="{{ route('admin.affiliates.edit', $affiliate) }}"
                                           class="inline-flex items-center rounded-md border border-gray-200 bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-800 hover:border-green-300 hover:bg-green-50 hover:text-green-800"
                                           title="Edit {{ $affiliate->name }}">
                                            {{ $affiliate->name }}
                                        </a>
                                    @endforeach
                                </div>
                                <p class="mt-1.5 text-[11px] text-gray-400">{{ $category->affiliates_count }} {{ Str::plural('partner', $category->affiliates_count) }}</p>
                            @else
                                <span class="text-xs text-gray-400">No partners yet</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-gray-700">{{ $category->order }}</td>
                        <td class="px-4 py-4">
                            @if($category->is_active)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-200">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right sm:px-6">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.affiliate-categories.edit', $category) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('admin.affiliate-categories.destroy', $category) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete this category and all its affiliates?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="font-semibold text-gray-800">No categories yet</p>
                            <a href="{{ route('admin.affiliate-categories.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                <i class="fas fa-plus"></i> Add first category
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
