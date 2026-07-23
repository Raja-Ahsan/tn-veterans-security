@extends('admin.layouts.master')

@section('title', 'Locations')
@section('page-title', 'Training Locations')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Locations</h3>
        <p class="mt-1 text-sm text-gray-500">Manage class locations used when scheduling sessions.</p>
    </div>
    <a href="{{ route('admin.locations.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
        <i class="fas fa-plus"></i> Add Location
    </a>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Location</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Address</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($locations as $location)
                    <tr class="hover:bg-gray-50/80 {{ ! $location->is_active ? 'opacity-60' : '' }}">
                        <td class="px-4 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-800">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900">{{ $location->name }}</p>
                                    @if(filled($location->notes))
                                        <p class="mt-0.5 truncate text-xs text-gray-500 max-w-xs">{{ Str::limit($location->notes, 60) }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <div class="space-y-0.5 text-gray-600">
                                <p>{{ $location->address ?: '—' }}</p>
                                <p class="text-xs text-gray-500">
                                    @php
                                        $cityState = collect([$location->city, $location->state])->filter()->implode(', ');
                                        $cityLine = trim($cityState.($location->zip ? ' '.$location->zip : ''));
                                    @endphp
                                    {{ $cityLine !== '' ? $cityLine : 'No city / state' }}
                                </p>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-gray-700">{{ $location->order }}</td>
                        <td class="px-4 py-4">
                            @if($location->is_active)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-inset ring-gray-200">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right sm:px-6">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.locations.edit', $location) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete this location?')">
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
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <p class="font-semibold text-gray-800">No locations yet</p>
                            <p class="mt-1 text-sm text-gray-500">Add locations so you can assign them on class schedules.</p>
                            <a href="{{ route('admin.locations.create') }}"
                               class="mt-4 inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                <i class="fas fa-plus"></i> Add first location
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
