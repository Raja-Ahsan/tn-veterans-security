@extends('student.layouts.master')

@section('title', $courseModule->title)

@section('content')
@php
    $videos = $videos ?? collect();
    $selectedVideo = $selectedVideo ?? null;
    $videoProgressMap = $videoProgressMap ?? collect();
    $hasWatchedVideo = $hasWatchedVideo ?? true;
    $quizQuestions = $quizQuestions ?? $courseModule->quizQuestions;
    $uploadedUrl = $selectedVideo?->uploadedVideoUrl();
    $embedUrl = $selectedVideo?->embedVideoUrl() ?? ($selectedVideo ? null : $courseModule->embedVideoUrl());
    $hasExternalVideo = $selectedVideo
        ? $selectedVideo->hasExternalVideoLink()
        : $courseModule->hasExternalVideoLink();
    $hasContent = filled(trim(strip_tags((string) $courseModule->content)));
    $quizCount = $quizQuestions->count();
    $passed = $selectedVideo
        ? app(\App\Services\BlendedCourseService::class)->isVideoComplete(auth('student')->user(), $selectedVideo)
        : (bool) ($moduleProgress?->is_completed);
    $modulePassed = $modulePassed ?? (bool) ($moduleProgress?->is_completed);
    $quizReview = $quizReview ?? [];
    $latestAttempt = $latestAttempt ?? null;
    $hasReview = $passed && count($quizReview) > 0;
    $quizMinutes = $quizMinutes ?? 15;
    $openSession = $openSession ?? null;
    $canAttemptQuiz = $canAttemptQuiz ?? true;
    $needsReenrollment = false;
    $supportEmail = $supportEmail ?? null;
    $supportPhone = $supportPhone ?? null;
    $passingScore = $passingScore ?? $courseModule->passingScore();
    $attemptsUsed = (int) ($attemptsUsed ?? 0);
    $materials = $materials ?? $courseModule->materialFiles();
@endphp

<div class="mb-5">
    <a href="{{ route('student.online-course.index', $service) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-[var(--brand)]">
        <i class="fas fa-arrow-left text-xs"></i> Back to modules
    </a>
</div>

<div class="mb-6">
    <p class="text-sm font-medium text-gray-500">{{ $service->title }}</p>
    <h1 class="mt-1 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $courseModule->title }}</h1>
    @if($passed)
        <p class="mt-2 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">
            <i class="fas fa-check-circle"></i>
            @if($selectedVideo)
                {{ $selectedVideo->displayTitle() }} passed
                @if($modulePassed) · Module complete @endif
            @else
                Passed with {{ $moduleProgress->best_score }}%
            @endif
        </p>
    @elseif($quizCount > 0)
        <p class="mt-2 text-sm text-gray-500">Watch the full video (pause is allowed; skipping ahead is not). After it ends, take the timed quiz. Passing score: {{ $passingScore }}%.</p>
    @endif
</div>

<div class="space-y-5">
    @if($videos->count() > 0)
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-500">Videos in this module</h2>
            <ol class="space-y-2">
                @foreach($videos as $video)
                    @php
                        $vp = $videoProgressMap->get($video->id);
                        $videoDone = app(\App\Services\BlendedCourseService::class)->isVideoComplete(auth('student')->user(), $video);
                        $videoLocked = ! app(\App\Services\BlendedCourseService::class)->canAccessVideo(auth('student')->user(), $courseModule, $video);
                        $isCurrent = $selectedVideo && $selectedVideo->id === $video->id;
                    @endphp
                    <li>
                        @if($videoLocked)
                            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-400">
                                <i class="fas fa-lock w-4 text-center"></i>
                                <span>{{ $loop->iteration }}. {{ $video->displayTitle() }}</span>
                                <span class="ml-auto text-xs">Locked</span>
                            </div>
                        @else
                            <a href="{{ route('student.online-course.module', [$service, $courseModule, 'video' => $video->id]) }}"
                               class="flex items-center gap-3 rounded-lg border px-3 py-2.5 text-sm {{ $isCurrent ? 'border-[var(--brand)] bg-green-50 font-semibold text-gray-900' : 'border-gray-200 bg-white text-gray-800 hover:border-green-300' }}">
                                <i class="fas {{ $videoDone ? 'fa-check-circle text-emerald-600' : 'fa-play-circle text-[var(--brand)]' }} w-4 text-center"></i>
                                <span>{{ $loop->iteration }}. {{ $video->displayTitle() }}</span>
                                @if($videoDone)
                                    <span class="ml-auto text-xs font-semibold text-emerald-700">Passed</span>
                                @elseif($vp?->video_watched)
                                    <span class="ml-auto text-xs text-blue-700">Watched</span>
                                @endif
                            </a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    @if($uploadedUrl)
        <div class="mx-auto w-full max-w-3xl overflow-hidden rounded-xl border border-gray-200 bg-black shadow-sm">
            <div class="aspect-video">
                <video
                    id="module-video-player"
                    class="h-full w-full"
                    controls
                    controlsList="nodownload noplaybackrate noremoteplayback"
                    disablePictureInPicture
                    playsinline
                    preload="metadata"
                    oncontextmenu="return false;"
                    data-watch-url="{{ route('student.online-course.video.watched', [$service, $courseModule, $selectedVideo]) }}"
                    data-already-watched="{{ $hasWatchedVideo ? '1' : '0' }}"
                    data-resume-seconds="{{ (int) ($videoProgress?->last_position_seconds ?? 0) }}"
                    data-csrf="{{ csrf_token() }}"
                >
                    <source src="{{ $uploadedUrl }}" type="video/mp4">
                    Your browser does not support this video.
                </video>
            </div>
        </div>
        @unless($hasWatchedVideo)
            <p class="text-center text-sm text-amber-800"><i class="fas fa-info-circle mr-1"></i> Pause is allowed. Skipping ahead is disabled. Finish the video to unlock the quiz.</p>
        @endunless
    @elseif($embedUrl)
        <div class="mx-auto w-full max-w-3xl overflow-hidden rounded-xl border border-gray-200 bg-black shadow-sm">
            <div class="aspect-video">
                <iframe
                    id="module-embed-player"
                    src="{{ $embedUrl }}"
                    class="h-full w-full"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"
                    title="{{ $selectedVideo?->displayTitle() ?? $courseModule->title }} video"
                    data-watch-url="{{ $selectedVideo ? route('student.online-course.video.watched', [$service, $courseModule, $selectedVideo]) : '' }}"
                    data-already-watched="{{ $hasWatchedVideo ? '1' : '0' }}"
                    data-csrf="{{ csrf_token() }}"
                ></iframe>
            </div>
        </div>
        @if($selectedVideo && $selectedVideo->requiresWatchCompletion() && ! $hasWatchedVideo)
            <p class="text-center text-sm text-amber-800"><i class="fas fa-info-circle mr-1"></i> Watch this video to the end to unlock the quiz.</p>
        @endif
    @elseif($hasExternalVideo)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-semibold text-amber-950">
                        <i class="fas fa-external-link-alt mr-1"></i> External video / resource
                    </p>
                    <p class="mt-1 text-sm text-amber-800">
                        This link opens in a new tab (not an embeddable YouTube/Vimeo player).
                    </p>
                </div>
                <a href="{{ $selectedVideo->video_url ?? $courseModule->video_url }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
                    Open resource <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                </a>
            </div>
        </div>
    @endif

    @if($courseModule->image_path)
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <img src="{{ asset('storage/'.$courseModule->image_path) }}" alt="{{ $courseModule->title }}" class="max-h-[28rem] w-full object-contain">
        </div>
    @endif

    @if(count($materials) > 0)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="mb-3 text-lg font-bold text-gray-900">Downloadable materials</h2>
            <ul class="space-y-2">
                @foreach($materials as $file)
                    <li>
                        <a href="{{ $file['url'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:underline">
                            <i class="fas fa-file-download"></i>
                            {{ $file['original_name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($hasContent)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="mb-3 text-lg font-bold text-gray-900">Lesson content</h2>
            <div class="prose max-w-none text-gray-700">{!! nl2br(e($courseModule->content)) !!}</div>
        </div>
    @elseif(! $uploadedUrl && ! $embedUrl && ! $hasExternalVideo && $quizCount > 0 && count($materials) === 0)
        <div class="rounded-xl border border-dashed border-gray-300 bg-white px-5 py-6 text-sm text-gray-600 shadow-sm">
            No written lesson content was added for this module. You can still take the quiz below.
        </div>
    @elseif(! $uploadedUrl && ! $embedUrl && ! $hasExternalVideo && $quizCount === 0)
        <div class="rounded-xl border border-dashed border-gray-300 bg-white px-5 py-10 text-center shadow-sm">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                <i class="fas fa-book-open text-xl"></i>
            </div>
            <p class="font-semibold text-gray-800">This module has no content yet</p>
            <p class="mx-auto mt-1 max-w-md text-sm text-gray-500">Ask your instructor/admin to add video, lesson text, or quiz questions.</p>
        </div>
    @endif

    @if($hasReview)
        <div id="quiz-review" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Quiz Review</h2>
                    <p class="text-sm text-gray-500">
                        Latest attempt:
                        <strong class="{{ ($latestAttempt?->passed ?? false) ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ $latestAttempt?->score ?? '—' }}%
                        </strong>
                        · Green = correct · Red = wrong
                    </p>
                </div>
                @php
                    $correctCount = collect($quizReview)->where('is_correct', true)->count();
                    $wrongCount = collect($quizReview)->where('is_correct', false)->count();
                @endphp
                <div class="flex gap-2 text-xs font-semibold">
                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-800">{{ $correctCount }} correct</span>
                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-red-800">{{ $wrongCount }} wrong</span>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($quizReview as $item)
                    <div class="rounded-lg border p-4 {{ $item['is_correct'] ? 'border-emerald-200 bg-emerald-50/60' : 'border-red-200 bg-red-50/60' }}">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <p class="font-semibold text-gray-900">{{ $loop->iteration }}. {{ $item['question'] }}</p>
                            @if($item['is_correct'])
                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">
                                    <i class="fas fa-check"></i> Correct
                                </span>
                            @else
                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-800">
                                    <i class="fas fa-times"></i> Wrong
                                </span>
                            @endif
                        </div>

                        <div class="space-y-2">
                            @foreach($item['options'] as $option)
                                @php
                                    $isSelected = in_array($option, $item['selected'] ?? [], true);
                                    $isCorrectOption = in_array($option, $item['correct_answer'] ?? [], true);
                                @endphp
                                <div @class([
                                    'flex items-center gap-3 rounded-lg border px-3 py-2.5 text-sm',
                                    'border-emerald-400 bg-emerald-100 text-emerald-950 font-semibold' => $isCorrectOption,
                                    'border-red-400 bg-red-100 text-red-950 font-semibold' => $isSelected && ! $isCorrectOption,
                                    'border-gray-200 bg-white text-gray-700' => ! $isSelected && ! $isCorrectOption,
                                ])>
                                    <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center">
                                        @if($isCorrectOption)
                                            <i class="fas fa-check-circle text-emerald-600"></i>
                                        @elseif($isSelected)
                                            <i class="fas fa-times-circle text-red-600"></i>
                                        @else
                                            <i class="far fa-circle text-gray-300"></i>
                                        @endif
                                    </span>
                                    <span class="flex-1">{{ $option }}</span>
                                    @if($isSelected && $isCorrectOption)
                                        <span class="text-xs font-bold text-emerald-700">Your answer</span>
                                    @elseif($isSelected)
                                        <span class="text-xs font-bold text-red-700">Your answer</span>
                                    @elseif($isCorrectOption)
                                        <span class="text-xs font-bold text-emerald-700">Correct answer</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if(! $item['is_correct'])
                            <p class="mt-3 text-sm text-red-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                You selected
                                <strong>{{ count($item['selected'] ?? []) ? implode(', ', $item['selected']) : 'no answer' }}</strong>.
                                Correct answer{{ count($item['correct_answer'] ?? []) > 1 ? 's' : '' }}:
                                <strong>{{ implode(', ', $item['correct_answer'] ?? []) }}</strong>.
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($modulePassed)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="font-semibold text-emerald-900">Module completed</p>
            <p class="mt-1 text-sm text-emerald-800">Best score: {{ $moduleProgress->best_score }}%. You can continue to the next unlocked module.</p>
            <div class="mt-3 flex flex-wrap gap-3">
                @if($hasReview)
                    <a href="#quiz-review" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-800 hover:underline">
                        <i class="fas fa-list-check"></i> Jump to answer review
                    </a>
                @endif
                <a href="{{ route('student.online-course.index', $service) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-800 hover:underline">
                    Back to module list <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    @elseif($quizCount > 0 && ! $passed)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900">{{ $selectedVideo ? $selectedVideo->displayTitle().' quiz' : 'Module Quiz' }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $quizCount }} {{ Str::plural('question', $quizCount) }}
                    · {{ $quizMinutes }} {{ Str::plural('minute', $quizMinutes) }} time limit
                    · {{ $passingScore }}% required to pass
                    @if($attemptsUsed > 0)
                        · {{ $attemptsUsed }} {{ Str::plural('attempt', $attemptsUsed) }} so far — unlimited free retries
                    @else
                        · unlimited free retries until you pass
                    @endif
                    · one question at a time (no going back)
                </p>
            </div>

            @if($latestAttempt && ! $passed)
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <p class="font-semibold">Last score: {{ $latestAttempt->score }}% ({{ $passingScore }}% required).</p>
                    <p class="mt-1">
                        @if($selectedVideo)
                            Rewatch the video fully, then start the quiz again. No admin reset needed.
                        @else
                            You can start the quiz again for free. No admin reset needed.
                        @endif
                    </p>
                </div>
            @endif

            @if($openSession)
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <i class="fas fa-hourglass-half mr-1"></i>
                    Quiz already in progress — redirecting you back to finish it.
                </div>
                <a href="{{ route('student.online-course.quiz.take', [$service, $courseModule]) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[var(--brand)] px-6 py-3 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                    <i class="fas fa-play text-xs"></i> Continue quiz
                </a>
            @elseif($selectedVideo && $selectedVideo->requiresWatchCompletion() && ! $hasWatchedVideo)
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <i class="fas fa-lock mr-1"></i>
                    Finish watching the video to unlock this quiz. You can pause, but you cannot skip ahead.
                </div>
            @elseif($canAttemptQuiz)
                <form method="POST" action="{{ route('student.online-course.quiz.start', [$service, $courseModule]) }}">
                    @csrf
                    @if($selectedVideo)
                        <input type="hidden" name="video_id" value="{{ $selectedVideo->id }}">
                    @endif
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[var(--brand)] px-6 py-3 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                        <i class="fas fa-play text-xs"></i> {{ $attemptsUsed > 0 ? 'Retake timed quiz' : 'Start timed quiz' }}
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>

@if(($uploadedUrl || $embedUrl) && $selectedVideo && ! $hasWatchedVideo)
<script>
(function () {
    var video = document.getElementById('module-video-player');
    var embed = document.getElementById('module-embed-player');
    var watchUrl = (video || embed) ? (video || embed).getAttribute('data-watch-url') : '';
    var csrf = (video || embed) ? (video || embed).getAttribute('data-csrf') : '';
    var already = (video || embed) && (video || embed).getAttribute('data-already-watched') === '1';
    if (already || !watchUrl) return;

    var markedComplete = false;
    var lastSavedPosition = 0;

    function postWatch(payload) {
        return fetch(watchUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        });
    }

    function markComplete(duration, position) {
        if (markedComplete) return;
        markedComplete = true;
        postWatch({
            completed: true,
            duration_seconds: Math.round(duration || 0),
            position_seconds: Math.round(position || duration || 0)
        }).then(function () {
            window.location.reload();
        }).catch(function () {
            markedComplete = false;
        });
    }

    function saveProgress(position, duration) {
        var rounded = Math.floor(position || 0);
        if (rounded < lastSavedPosition + 5) return;
        lastSavedPosition = rounded;
        postWatch({
            completed: false,
            position_seconds: rounded,
            duration_seconds: Math.round(duration || 0)
        });
    }

    if (video) {
        var maxWatched = Math.max(0, parseFloat(video.getAttribute('data-resume-seconds') || '0') || 0);
        var seekingLock = false;
        lastSavedPosition = Math.floor(maxWatched);

        function clampForwardSeek() {
            if (video.currentTime > maxWatched + 0.35) {
                seekingLock = true;
                video.currentTime = maxWatched;
            }
        }

        video.addEventListener('loadedmetadata', function () {
            if (maxWatched > 0 && maxWatched < (video.duration || Infinity)) {
                video.currentTime = maxWatched;
            }
        });

        video.addEventListener('timeupdate', function () {
            if (seekingLock || video.seeking) return;
            if (video.currentTime > maxWatched) {
                maxWatched = video.currentTime;
            }
            saveProgress(maxWatched, video.duration || 0);
        });

        video.addEventListener('seeking', clampForwardSeek);
        video.addEventListener('seeked', function () {
            clampForwardSeek();
            seekingLock = false;
        });

        video.addEventListener('ratechange', function () {
            if (video.playbackRate > 1) {
                video.playbackRate = 1;
            }
        });

        video.addEventListener('keydown', function (event) {
            // Block forward skip keys while locked.
            var blocked = ['ArrowRight', 'ArrowUp', '.', '>', 'MediaFastForward'];
            if (blocked.indexOf(event.key) !== -1) {
                event.preventDefault();
            }
        });

        video.addEventListener('ended', function () {
            maxWatched = Math.max(maxWatched, video.duration || 0);
            markComplete(video.duration || 0, maxWatched);
        });
    }

    if (embed && /youtube\.com\/embed/.test(embed.src || '')) {
        var tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(tag);
        window.onYouTubeIframeAPIReady = function () {
            new YT.Player('module-embed-player', {
                events: {
                    onStateChange: function (event) {
                        if (event.data === YT.PlayerState.ENDED) {
                            markComplete(0, 0);
                        }
                    }
                }
            });
        };
    }
})();
</script>
@endif
@endsection
