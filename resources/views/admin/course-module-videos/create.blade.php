@extends('admin.layouts.master')

@section('title', 'Add Video')
@section('page-title', 'Add Video')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Add video to {{ $courseModule->title }}</h3>
        <p class="mt-1 text-sm text-gray-500">Students must watch this video all the way through before taking its quiz.</p>
    </div>
    <a href="{{ route('admin.classes.course-modules.edit', [$service, $courseModule]) }}"
       class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ← Back to module
    </a>
</div>

<form method="POST" action="{{ route('admin.classes.course-modules.videos.store', [$service, $courseModule]) }}" enctype="multipart/form-data" class="w-full space-y-6 pb-8">
    @csrf
    @include('admin.course-modules._form', [
        'courseModule' => $courseModule,
        'courseModuleVideo' => null,
        'hideModuleDetails' => true,
    ])

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700">
            <i class="fas fa-save"></i> Save Video
        </button>
        <a href="{{ route('admin.classes.course-modules.edit', [$service, $courseModule]) }}"
           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
    </div>
</form>
@endsection
