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
        @if($moduleVideos->count() > 1)
            <p class="mb-2 text-xs text-gray-500">Drag the <i class="fas fa-grip-vertical text-gray-400"></i> handle to rearrange videos. Order saves automatically and students see the same order.</p>
        @endif
        <ul id="module-videos-sortable"
            class="divide-y divide-gray-100 rounded-md border border-gray-200"
            @if($moduleVideos->count() > 1)
                data-reorder-url="{{ route('admin.classes.course-modules.videos.reorder', [$service, $courseModule]) }}"
            @endif>
            @foreach($moduleVideos as $video)
                <li class="module-video-row flex flex-col gap-2 px-3 py-3 sm:flex-row sm:items-center sm:justify-between bg-white"
                    data-video-id="{{ $video->id }}">
                    <div class="flex min-w-0 items-start gap-2 sm:items-center">
                        @if($moduleVideos->count() > 1)
                            <button type="button"
                                    class="video-drag-handle mt-0.5 shrink-0 cursor-grab active:cursor-grabbing rounded border border-gray-200 bg-gray-50 px-1.5 py-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                    title="Drag to reorder"
                                    aria-label="Drag to reorder {{ $video->displayTitle() }}">
                                <i class="fas fa-grip-vertical"></i>
                            </button>
                        @endif
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900">
                                <span class="video-order-num">{{ $loop->iteration }}</span>. {{ $video->displayTitle() }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $video->quizQuestions->count() }} {{ Str::plural('question', $video->quizQuestions->count()) }}
                                @if($video->video_path) · uploaded file @endif
                                @if($video->video_url) · URL @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:pl-0 {{ $moduleVideos->count() > 1 ? 'pl-9' : '' }}">
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

@if(($courseModule->videos ?? collect())->count() > 1)
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
(function () {
    var list = document.getElementById('module-videos-sortable');
    if (!list || typeof Sortable === 'undefined') {
        return;
    }

    var reorderUrl = list.getAttribute('data-reorder-url');
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('input[name="_token"]')?.value;

    function renumberVideos() {
        list.querySelectorAll('.module-video-row').forEach(function (row, index) {
            var num = row.querySelector('.video-order-num');
            if (num) {
                num.textContent = String(index + 1);
            }
        });
    }

    function persistOrder() {
        if (!reorderUrl || !csrfToken) {
            return;
        }

        var order = Array.prototype.map.call(list.querySelectorAll('.module-video-row'), function (row) {
            return row.getAttribute('data-video-id');
        });

        fetch(reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ order: order })
        }).catch(function () {
            // Keep local order; admin can refresh if save failed.
        });
    }

    Sortable.create(list, {
        handle: '.video-drag-handle',
        animation: 180,
        ghostClass: 'opacity-40',
        chosenClass: 'bg-slate-50',
        dragClass: 'shadow-lg',
        onEnd: function () {
            renumberVideos();
            persistOrder();
        }
    });
})();
</script>
@endif
@endsection
