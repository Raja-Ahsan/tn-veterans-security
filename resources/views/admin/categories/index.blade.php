@extends('admin.layouts.master')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
@php
    $linkTypeMeta = [
        'category' => ['label' => 'Listing', 'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200'],
        'slug' => ['label' => 'Page', 'class' => 'bg-blue-50 text-blue-800 border-blue-200'],
        'route' => ['label' => 'Route', 'class' => 'bg-violet-50 text-violet-800 border-violet-200'],
    ];
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Website menu categories</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            These appear in the public header menus and in the class form when you assign a category.
        </p>
    </div>
    <a href="{{ route('admin.categories.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
        <i class="fas fa-plus text-xs"></i> Add Category
    </a>
</div>

<div class="mb-6 grid gap-3 sm:grid-cols-2">
    <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Training &amp; Classes</p>
        <p class="mt-1 text-sm text-emerald-900">Public nav dropdown under <strong>Training &amp; Classes</strong>.</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Security Training</p>
        <p class="mt-1 text-sm text-slate-800">Public nav dropdown under <strong>Security Training</strong>.</p>
    </div>
</div>

@foreach(['training' => 'Training & Classes', 'security' => 'Security Training'] as $group => $groupLabel)
    @php $groupItems = $categories[$group] ?? collect(); @endphp
    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 bg-gray-50 px-4 py-3 sm:px-5">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ $group === 'training' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                    <i class="fas {{ $group === 'training' ? 'fa-graduation-cap' : 'fa-shield-alt' }} text-sm"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 sm:text-base">{{ $groupLabel }}</h2>
                    <p class="text-xs text-gray-500">{{ $groupItems->count() }} {{ Str::plural('item', $groupItems->count()) }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-white">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-400">
                        <th class="px-4 py-2.5 sm:px-5">#</th>
                        <th class="px-4 py-2.5 sm:px-5">Category</th>
                        <th class="px-4 py-2.5 sm:px-5">Opens</th>
                        <th class="px-4 py-2.5 sm:px-5">Flags</th>
                        <th class="px-4 py-2.5 text-right sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($groupItems as $category)
                        @php
                            $type = $linkTypeMeta[$category->link_type] ?? ['label' => ucfirst($category->link_type), 'class' => 'bg-gray-50 text-gray-700 border-gray-200'];
                        @endphp
                        <tr class="hover:bg-gray-50/80 {{ ! $category->is_active ? 'opacity-60' : '' }}">
                            <td class="whitespace-nowrap px-4 py-3 align-middle text-xs font-semibold text-gray-400 sm:px-5">
                                {{ $category->sort_order }}
                            </td>
                            <td class="px-4 py-3 align-middle sm:px-5">
                                <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-gray-500">{{ $category->slug }}</p>
                            </td>
                            <td class="px-4 py-3 align-middle sm:px-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-md border px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide {{ $type['class'] }}">
                                        {{ $type['label'] }}
                                    </span>
                                    <span class="font-mono text-xs text-gray-600">{{ $category->link_value }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle sm:px-5">
                                <div class="flex flex-wrap gap-1.5">
                                    <span @class([
                                        'inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold',
                                        'bg-emerald-100 text-emerald-800' => $category->show_in_nav,
                                        'bg-gray-100 text-gray-500' => ! $category->show_in_nav,
                                    ])>Nav</span>
                                    <span @class([
                                        'inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold',
                                        'bg-blue-100 text-blue-800' => $category->assignable,
                                        'bg-gray-100 text-gray-500' => ! $category->assignable,
                                    ])>Class form</span>
                                    <span @class([
                                        'inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold',
                                        'bg-green-100 text-green-800' => $category->is_active,
                                        'bg-red-100 text-red-700' => ! $category->is_active,
                                    ])>{{ $category->is_active ? 'Active' : 'Off' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle text-right sm:px-5">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                          onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                No categories in this group yet.
                                <a href="{{ route('admin.categories.create') }}" class="font-semibold text-green-700 hover:underline">Add one</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endforeach
@endsection
