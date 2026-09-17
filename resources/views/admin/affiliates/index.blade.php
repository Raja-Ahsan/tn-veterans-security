@extends('admin.layouts.master')

@section('title', 'Affiliates')
@section('page-title', 'Affiliates')

@section('content')
<div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Affiliates</h3>
        <p class="mt-1 text-sm text-gray-500">Partners for the Affiliated page, NRA page, and website header menu.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.affiliate-categories.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
            <i class="fas fa-folder"></i> Categories
        </a>
        <a href="{{ route('admin.affiliates.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
            <i class="fas fa-plus"></i> Add Affiliate
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

@php
    $totalAffiliates = $categories->sum(fn ($c) => $c->affiliates->count()) + $ungrouped->count();
@endphp

@if($totalAffiliates === 0)
    <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center shadow-sm">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <i class="fas fa-handshake text-xl"></i>
        </div>
        <p class="font-semibold text-gray-800">No affiliates yet</p>
        <p class="mt-1 text-sm text-gray-500">Create a category first, then add partners.</p>
        <div class="mt-4 flex justify-center gap-2">
            <a href="{{ route('admin.affiliate-categories.create') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Add category</a>
            <a href="{{ route('admin.affiliates.create') }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Add affiliate</a>
        </div>
    </div>
@else
    @foreach($categories as $category)
        <div class="mb-5 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-gray-100 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $category->page === 'nra' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700' }}">
                        <i class="fas {{ $category->page === 'nra' ? 'fa-bullseye' : 'fa-building' }} text-sm"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 sm:text-base">{{ $category->name }}</h2>
                        <p class="text-xs text-gray-500">
                            {{ $category->page === 'nra' ? 'NRA Services page' : 'Affiliated Services page' }}
                            · {{ $category->affiliates->count() }} {{ Str::plural('partner', $category->affiliates->count()) }}
                            @unless($category->is_active)
                                · <span class="font-semibold text-amber-700">Category inactive</span>
                            @endunless
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.affiliates.create', ['affiliate_category_id' => $category->id]) }}"
                   class="inline-flex items-center gap-1.5 self-start rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100 sm:self-auto">
                    <i class="fas fa-plus"></i> Add here
                </a>
            </div>

            @if($category->affiliates->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-gray-500">No partners in this section yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                <th class="w-14 px-4 py-2.5 sm:px-5">#</th>
                                <th class="px-4 py-2.5 sm:px-5">Partner</th>
                                <th class="px-4 py-2.5">Shown in</th>
                                <th class="px-4 py-2.5">Status</th>
                                <th class="px-4 py-2.5 text-right sm:px-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($category->affiliates as $affiliate)
                                <tr class="hover:bg-gray-50/80 {{ ! $affiliate->is_active ? 'opacity-60' : '' }}">
                                    <td class="whitespace-nowrap px-4 py-3 align-middle sm:px-5">
                                        <span class="inline-flex h-7 min-w-7 items-center justify-center rounded-md bg-slate-100 px-1.5 text-xs font-bold text-slate-600" title="Sort order">
                                            {{ $affiliate->order }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 align-middle sm:px-5">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-[11px] font-bold text-emerald-800">
                                                {{ $affiliate->initials }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-gray-900">{{ $affiliate->name }}</p>
                                                <a href="{{ $affiliate->url }}" target="_blank" rel="noopener noreferrer"
                                                   class="mt-0.5 inline-flex max-w-[20rem] items-center gap-1 truncate text-xs text-blue-600 hover:underline">
                                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                                    {{ Str::limit(preg_replace('#^https?://#', '', $affiliate->url), 40) }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        <div class="flex flex-wrap gap-1">
                                            @if($affiliate->show_in_nav)
                                                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-200">Main menu</span>
                                            @endif
                                            @if($affiliate->show_in_nra_nav)
                                                <span class="rounded-full bg-violet-50 px-2 py-0.5 text-[11px] font-semibold text-violet-700 ring-1 ring-inset ring-violet-200">NRA menu</span>
                                            @endif
                                            @if(! $affiliate->show_in_nav && ! $affiliate->show_in_nra_nav)
                                                <span class="rounded-full bg-gray-50 px-2 py-0.5 text-[11px] font-semibold text-gray-500 ring-1 ring-inset ring-gray-200">Page only</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-middle">
                                        @if($affiliate->is_active)
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-200">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right align-middle sm:px-5">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.affiliates.edit', $affiliate) }}"
                                               class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                                <i class="fas fa-pen"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.affiliates.destroy', $affiliate) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Delete this affiliate?')">
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach

    @if($ungrouped->isNotEmpty())
        <div class="mb-5 overflow-hidden rounded-xl border border-amber-200 bg-white shadow-sm">
            <div class="border-b border-amber-100 bg-amber-50 px-4 py-3 sm:px-5">
                <h2 class="text-sm font-bold text-amber-900">Uncategorized</h2>
                <p class="text-xs text-amber-800">These partners need a category assigned.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($ungrouped as $affiliate)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-900 sm:px-5">{{ $affiliate->name }}</td>
                                <td class="px-4 py-3 text-right sm:px-5">
                                    <a href="{{ route('admin.affiliates.edit', $affiliate) }}" class="text-xs font-semibold text-blue-700 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif
@endsection
