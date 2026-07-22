@extends('student.layouts.master')

@section('title', 'Quiz Result — '.$courseModule->title)

@section('content')
@php
    $timedOut = $moduleQuizSession->status === \App\Models\ModuleQuizSession::STATUS_EXPIRED;
    $nextModule = null;
    $foundCurrent = false;
    foreach ($modules as $module) {
        if ($foundCurrent) {
            $nextModule = $module;
            break;
        }
        if ($module->id === $courseModule->id) {
            $foundCurrent = true;
        }
    }
    $nextUnlocked = $passed && $nextModule
        && app(\App\Services\BlendedCourseService::class)->canAccessModule(
            auth('student')->user(),
            $nextModule,
            $progress,
            $modules
        );
@endphp

<div class="mx-auto max-w-3xl space-y-5">
    <div class="mb-2">
        <a href="{{ route('student.online-course.module', [$service, $courseModule]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-[var(--brand)]">
            <i class="fas fa-arrow-left text-xs"></i> Back to module
        </a>
    </div>

    <div class="rounded-2xl border p-6 text-center shadow-sm {{ $passed ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full {{ $passed ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
            <i class="fas {{ $passed ? 'fa-trophy' : 'fa-times' }} text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold {{ $passed ? 'text-emerald-950' : 'text-red-950' }}">
            {{ $passed ? 'Module passed!' : 'Not quite there yet' }}
        </h1>
        <p class="mt-2 text-4xl font-black {{ $passed ? 'text-emerald-700' : 'text-red-700' }}">{{ $score }}%</p>
        <p class="mt-2 text-sm {{ $passed ? 'text-emerald-800' : 'text-red-800' }}">
            Passing score is 90%.
            @if($timedOut)
                Time ran out — your answered questions were submitted automatically.
            @endif
        </p>

        <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
            @if($passed && $nextUnlocked)
                <a href="{{ route('student.online-course.module', [$service, $nextModule]) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[var(--brand)] px-5 py-3 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                    Continue to next module <i class="fas fa-arrow-right text-xs"></i>
                </a>
            @elseif(! $passed)
                <a href="{{ route('student.online-course.module', [$service, $courseModule]) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[var(--brand)] px-5 py-3 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                    Back to module
                </a>
            @endif

            @if($certificate)
                <a href="{{ route('student.certificates.show', $certificate) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-white px-5 py-3 text-sm font-semibold text-emerald-800 hover:bg-emerald-50">
                    <i class="fas fa-certificate"></i> View certificate
                </a>
            @endif

            <a href="{{ route('student.online-course.index', $service) }}"
               class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                All modules
            </a>
        </div>

        @if($eligible && $certificate)
            <p class="mt-4 text-sm font-medium text-emerald-800">
                <i class="fas fa-check-circle mr-1"></i>
                All modules passed — your certificate is ready.
            </p>
        @elseif($passed && ! $nextModule)
            <p class="mt-4 text-sm font-medium text-emerald-800">
                You finished the last module for this course.
            </p>
        @elseif($passed && ! $nextUnlocked)
            <p class="mt-4 text-sm text-emerald-800">Next module unlocks after this pass is recorded.</p>
        @elseif(! $passed)
            <div class="mt-5 rounded-xl border border-amber-200 bg-white/80 px-4 py-3 text-left text-sm text-amber-950">
                <p class="font-semibold"><i class="fas fa-ban mr-1"></i> Free retake is not available</p>
                <p class="mt-1 text-amber-900">
                    Correct answers are not shown after a failed attempt. To try again you must
                    <strong>re-enroll / contact admin</strong> so they can update the module questions and reset your attempt.
                </p>
                @if(! empty($supportEmail) || ! empty($supportPhone))
                    <p class="mt-2 text-amber-800">
                        Contact:
                        @if(! empty($supportEmail))
                            <a href="mailto:{{ $supportEmail }}" class="font-semibold underline">{{ $supportEmail }}</a>
                        @endif
                        @if(! empty($supportEmail) && ! empty($supportPhone)) · @endif
                        @if(! empty($supportPhone))
                            <a href="tel:{{ $supportPhone }}" class="font-semibold underline">{{ $supportPhone }}</a>
                        @endif
                    </p>
                @endif
            </div>
        @endif
    </div>

    @if($passed && count($quizReview) > 0)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Answer review</h2>
            <div class="space-y-4">
                @foreach($quizReview as $item)
                    <div class="rounded-xl border p-4 {{ $item['is_correct'] ? 'border-emerald-200 bg-emerald-50/50' : 'border-red-200 bg-red-50/50' }}">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <p class="font-semibold text-gray-900">{{ $loop->iteration }}. {{ $item['question'] }}</p>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold {{ $item['is_correct'] ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item['is_correct'] ? 'Correct' : 'Wrong' }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            @foreach($item['options'] as $option)
                                @php
                                    $selected = in_array($option, $item['selected'] ?? [], true);
                                    $correctOpt = in_array($option, $item['correct_answer'] ?? [], true);
                                @endphp
                                <div @class([
                                    'flex items-center gap-3 rounded-lg border px-3 py-2.5 text-sm',
                                    'border-emerald-400 bg-emerald-100 text-emerald-950 font-semibold' => $correctOpt,
                                    'border-red-400 bg-red-100 text-red-950 font-semibold' => $selected && ! $correctOpt,
                                    'border-gray-200 bg-white text-gray-700' => ! $selected && ! $correctOpt,
                                ])>
                                    <span>{{ $option }}</span>
                                    @if($selected && $correctOpt)
                                        <span class="ml-auto text-xs font-bold text-emerald-700">Your answer</span>
                                    @elseif($selected)
                                        <span class="ml-auto text-xs font-bold text-red-700">Your answer</span>
                                    @elseif($correctOpt)
                                        <span class="ml-auto text-xs font-bold text-emerald-700">Correct</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if(($item['allow_multiple'] ?? false))
                            <p class="mt-2 text-xs text-gray-500">Multi-select question</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
