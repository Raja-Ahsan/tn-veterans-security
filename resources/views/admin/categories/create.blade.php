@extends('admin.layouts.master')

@section('title', 'Add Category')
@section('page-title', 'Add Category')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-5 flex items-center justify-between gap-3">
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-green-700">
            <i class="fas fa-arrow-left text-xs"></i> Back to categories
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
            <h3 class="text-lg font-bold text-gray-900">New category</h3>
            <p class="mt-1 text-sm text-gray-500">This can appear in the public menu and when assigning categories to a class.</p>
        </div>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="p-5 sm:p-6">
            @csrf
            @include('admin.categories._form')
            <div class="mt-6 flex flex-wrap gap-3 border-t border-gray-100 pt-5">
                <button type="submit" class="inline-flex items-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                    Create category
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
