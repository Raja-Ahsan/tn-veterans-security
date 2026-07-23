@extends('student.layouts.master')

@section('title', $courseModule->title)

@section('content')
@php
    $embedUrl = $courseModule->embedVideoUrl();
    $hasExternalVideo = $courseModule->hasExternalVideoLink();
    $hasContent = filled(trim(strip_tags((string) $courseModule->content)));
    $quizCount = $courseModule->quizQuestions->count();
    $passed = (bool) ($moduleProgress?->is_completed);
    $quizReview = $quizReview ?? [];
    $latestAttempt = $latestAttempt ?? null;
    $hasReview = $passed && count($quizReview) > 0;
    $quizMinutes = $quizMinutes ?? 15;
    $openSession = $openSession ?? null;
    $canAttemptQuiz = $canAttemptQuiz ?? true;
    $needsReenrollment = $needsReenrollment ?? false;
    $supportEmail = $supportEmail ?? null;
    $supportPhone = $supportPhone ?? null;
    $passingScore = $passingScore ?? $courseModule->passingScore();
    $maxAttempts = $maxAttempts ?? $courseModule->maxAttempts();
    $attemptsUsed = (int) ($moduleProgress?->attempts ?? 0);
    $attemptsRemaining = max(0, $maxAttempts - $attemptsUsed);
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
            <i class="fas fa-check-circle"></i> Passed with {{ $moduleProgress->best_score }}%
        </p>
    @elseif($quizCount > 0)
        <p class="mt-2 text-sm text-gray-500">Review the material below, then take the timed quiz. Passing score: {{ $passingScore }}%.</p>
    @endif
</div>

<div class="space-y-5">
    @if($embedUrl)
        <div class="mx-auto w-full max-w-3xl overflow-hidden rounded-xl border border-gray-200 bg-black shadow-sm">
            <div class="aspect-video">
                <iframe
                    src="{{ $embedUrl }}"
                    class="h-full w-full"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"
                    title="{{ $courseModule->title }} video"
                ></iframe>
            </div>
        </div>
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
                <a href="{{ $courseModule->video_url }}" target="_blank" rel="noopener noreferrer"
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
    @elseif(! $embedUrl && ! $hasExternalVideo && $quizCount > 0 && count($materials) === 0)
        <div class="rounded-xl border border-dashed border-gray-300 bg-white px-5 py-6 text-sm text-gray-600 shadow-sm">
            No written lesson content was added for this module. You can still take the quiz below.
        </div>
    @elseif(! $embedUrl && ! $hasExternalVideo && $quizCount === 0)
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

    @if($passed)
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
    @elseif($needsReenrollment)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-bold text-amber-950">Quiz attempt used</h2>
            <p class="mt-2 text-sm text-amber-900">
                @php
                    $displayScore = $moduleProgress?->best_score ?? $latestAttempt?->score;
                    $latestScore = $latestAttempt?->score;
                @endphp
                Your best score was
                <strong>{{ $displayScore ?? '—' }}%</strong>
                ({{ $passingScore }}% required).
                @if($latestScore !== null && (int) $latestScore !== (int) $displayScore)
                    Latest attempt: <strong>{{ $latestScore }}%</strong>.
                @endif
                All {{ $maxAttempts }} {{ Str::plural('attempt', $maxAttempts) }} used. Correct answers are not shown.
            </p>
            <p class="mt-3 text-sm text-amber-900">
                To try again, contact admin to <strong>re-enroll / reset this module</strong> after they update the questions and answers.
            </p>
            @if($supportEmail || $supportPhone)
                <p class="mt-3 text-sm text-amber-950">
                    Contact:
                    @if($supportEmail)
                        <a href="mailto:{{ $supportEmail }}" class="font-semibold underline">{{ $supportEmail }}</a>
                    @endif
                    @if($supportEmail && $supportPhone) · @endif
                    @if($supportPhone)
                        <a href="tel:{{ $supportPhone }}" class="font-semibold underline">{{ $supportPhone }}</a>
                    @endif
                </p>
            @endif
            <a href="{{ route('student.online-course.index', $service) }}"
               class="mt-4 inline-flex items-center gap-2 rounded-xl border border-amber-300 bg-white px-5 py-2.5 text-sm font-semibold text-amber-950 hover:bg-amber-100">
                Back to modules
            </a>
        </div>
    @elseif($quizCount > 0)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-gray-900">Module Quiz</h2>
                <p class="text-sm text-gray-500">
                    {{ $quizCount }} {{ Str::plural('question', $quizCount) }}
                    · {{ $quizMinutes }} {{ Str::plural('minute', $quizMinutes) }} time limit
                    · {{ $passingScore }}% required to pass
                    · {{ $maxAttempts }} {{ Str::plural('attempt', $maxAttempts) }}
                    @if($attemptsUsed > 0)
                        ({{ $attemptsRemaining }} remaining)
                    @endif
                    · one question at a time (no going back)
                </p>
            </div>

            @if($openSession)
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    <i class="fas fa-hourglass-half mr-1"></i>
                    Quiz already in progress — redirecting you back to finish it.
                </div>
                <a href="{{ route('student.online-course.quiz.take', [$service, $courseModule]) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[var(--brand)] px-6 py-3 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                    <i class="fas fa-play text-xs"></i> Continue quiz
                </a>
            @elseif($canAttemptQuiz)
                <form method="POST" action="{{ route('student.online-course.quiz.start', [$service, $courseModule]) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[var(--brand)] px-6 py-3 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                        <i class="fas fa-play text-xs"></i> Start timed quiz
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>
@endsection
