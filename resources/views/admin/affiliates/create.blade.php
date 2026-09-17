@extends('admin.layouts.master')

@section('title', 'Add Affiliate')
@section('page-title', 'Add Affiliate')

@section('content')
<div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="{{ route('admin.affiliates.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-green-700">
            <i class="fas fa-arrow-left text-xs"></i> Back to affiliates
        </a>
        <h3 class="mt-2 text-xl font-semibold text-gray-900">Add affiliate</h3>
        <p class="mt-1 text-sm text-gray-500">Partners appear on the Affiliated / NRA pages and optionally in the website header menu.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.affiliates.store') }}" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    @csrf
    <div class="border-b border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
        <h4 class="text-base font-bold text-gray-900">Affiliate details</h4>
    </div>
    <div class="p-5 sm:p-6">
        @include('admin.affiliates._form', ['affiliate' => null])
    </div>
    <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 bg-gray-50 px-5 py-4 sm:px-6">
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
            <i class="fas fa-save"></i> Save Affiliate
        </button>
        <a href="{{ route('admin.affiliates.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
