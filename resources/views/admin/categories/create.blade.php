@extends('admin.layouts.master')

@section('title', 'Add Category')
@section('page-title', 'Add Category')

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-green-700">
        <i class="fas fa-arrow-left text-xs"></i> Back to categories
    </a>
    <h3 class="mt-2 text-xl font-semibold text-gray-900">Add category</h3>
    <p class="mt-1 text-sm text-gray-500">This can appear in the public menu and when assigning categories to a class.</p>
</div>

<form method="POST" action="{{ route('admin.categories.store') }}" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    @csrf
    <div class="border-b border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
        <h4 class="text-base font-bold text-gray-900">New category</h4>
    </div>
    <div class="p-5 sm:p-6">
        @include('admin.categories._form')
    </div>
    <div class="flex flex-wrap gap-3 border-t border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
        <button type="submit" class="inline-flex items-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
            Create category
        </button>
        <a href="{{ route('admin.categories.index') }}"
           class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
    </div>
</form>
@endsection
