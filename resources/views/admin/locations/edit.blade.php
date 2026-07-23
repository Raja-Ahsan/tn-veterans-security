@extends('admin.layouts.master')

@section('title', 'Edit Location')
@section('page-title', 'Edit Location')

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.locations.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-green-700">
        <i class="fas fa-arrow-left text-xs"></i> Back to locations
    </a>
</div>

<div class="mx-auto max-w-3xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
        <h3 class="text-lg font-bold text-gray-900">Edit location</h3>
        <p class="mt-0.5 text-sm text-gray-500">{{ $location->name }}</p>
    </div>
    <form method="POST" action="{{ route('admin.locations.update', $location) }}" class="space-y-6 p-5 sm:p-6">
        @csrf
        @method('PUT')
        @include('admin.locations._form')
        <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-5">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                <i class="fas fa-save"></i> Update Location
            </button>
            <a href="{{ route('admin.locations.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
