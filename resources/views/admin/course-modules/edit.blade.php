@extends('admin.layouts.master')

@section('title', 'Edit Module')
@section('page-title', 'Edit Module')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">Edit Module</h3>
        <p class="mt-1 text-sm text-gray-500">
            For class: <span class="font-medium text-gray-800">{{ $service->title }}</span>
        </p>
        <p class="mt-0.5 text-sm font-medium text-gray-700">{{ $courseModule->title }}</p>
    </div>
    <a href="{{ route('admin.classes.course-modules.index', $service) }}"
       class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ← Back to modules
    </a>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h4 class="text-base font-bold text-gray-900">Videos in this module</h4>
            <p class="text-sm text-gray-500">Students watch each video fully, pass its quiz, then unlock the next video. After every video quiz is passed they can enter the next module.</p>
        </div>
        <a href="{{ route('admin.classes.course-modules.videos.create', [$service, $courseModule]) }}"
           class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
            <i class="fas fa-plus"></i> Add another video
        </a>
    </div>
    @php $moduleVideos = $courseModule->videos ?? collect(); @endphp
    @if($moduleVideos->count() > 0)
        <ul class="divide-y divide-gray-100 rounded-md border border-gray-200">
            @foreach($moduleVideos as $video)
                <li class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $loop->iteration }}. {{ $video->displayTitle() }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $video->quizQuestions->count() }} {{ Str::plural('question', $video->quizQuestions->count()) }}
                            @if($video->video_path) · uploaded file @endif
                            @if($video->video_url) · URL @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.classes.course-modules.videos.edit', [$service, $courseModule, $video]) }}"
                           class="rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">Edit</a>
                        @if($moduleVideos->count() > 1)
                            <form method="POST" action="{{ route('admin.classes.course-modules.videos.destroy', [$service, $courseModule, $video]) }}" onsubmit="return confirm('Delete this video and its quiz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Delete</button>
                            </form>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-gray-500">Save this module with a video/quiz below, or add extra videos here after saving.</p>
    @endif
</div>

<form method="POST" action="{{ route('admin.classes.course-modules.update', [$service, $courseModule]) }}" enctype="multipart/form-data" class="w-full space-y-6 pb-8">
    @csrf
    @method('PUT')
    @include('admin.course-modules._form', ['courseModule' => $courseModule])

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700">
            <i class="fas fa-save"></i> Update Module
        </button>
        <a href="{{ route('admin.classes.course-modules.index', $service) }}"
           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
    </div>
</form>
@endsection
