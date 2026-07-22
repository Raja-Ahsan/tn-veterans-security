@extends('student.layouts.master')

@section('title', 'Quiz — '.$courseModule->title)

@section('content')
@php
    $pct = $totalQuestions > 0 ? (int) round(($questionNumber / $totalQuestions) * 100) : 0;
@endphp

<div class="mx-auto max-w-3xl" data-quiz-lock="1">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $service->title }}</p>
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">{{ $courseModule->title }}</h1>
        </div>
        <div id="quiz-timer"
             data-remaining="{{ $remainingSeconds }}"
             class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-900 px-4 py-2.5 font-mono text-lg font-bold text-white shadow-sm">
            <i class="fas fa-clock text-amber-300"></i>
            <span id="quiz-timer-display">--:--</span>
        </div>
    </div>

    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
        <i class="fas fa-exclamation-triangle mr-1 text-amber-600"></i>
        Quiz in progress — stay on this page. The timer keeps running even if you leave.
    </div>

    <div class="mb-5">
        <div class="mb-2 flex items-center justify-between text-sm text-gray-600">
            <span>Question {{ $questionNumber }} of {{ $totalQuestions }}</span>
            <span>{{ $pct }}%</span>
        </div>
        <div class="h-2 overflow-hidden rounded-full bg-gray-200">
            <div class="h-full rounded-full bg-[var(--brand)] transition-all" style="width: {{ $pct }}%"></div>
        </div>
    </div>

    <form id="quiz-answer-form" method="POST" action="{{ route('student.online-course.quiz.answer', [$service, $courseModule]) }}" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <input type="hidden" name="auto_submit" id="auto_submit" value="0">

        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
            {{ $question->allow_multiple ? 'Select all that apply' : 'Select one answer' }}
        </p>
        <h2 class="mb-5 text-lg font-bold text-gray-900 sm:text-xl">{{ $question->question }}</h2>

        <div class="space-y-3">
            @foreach($question->options as $option)
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3.5 transition hover:border-[var(--brand)] hover:bg-emerald-50/50 has-[:checked]:border-[var(--brand)] has-[:checked]:bg-emerald-50">
                    @if($question->allow_multiple)
                        <input type="checkbox" name="answers[]" value="{{ $option }}"
                               class="mt-0.5 rounded border-gray-400 text-[var(--brand)] focus:ring-[var(--brand)]">
                    @else
                        <input type="radio" name="answer" value="{{ $option }}" required
                               class="mt-0.5 border-gray-400 text-[var(--brand)] focus:ring-[var(--brand)]">
                    @endif
                    <span class="text-sm font-medium text-gray-800 sm:text-base">{{ $option }}</span>
                </label>
            @endforeach
        </div>

        @error('answer')
            <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
        @error('answers')
            <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror

        <div class="mt-7 flex items-center justify-between gap-3 border-t border-gray-100 pt-5">
            <p class="text-xs text-gray-500">
                <i class="fas fa-lock mr-1"></i> You cannot go back after continuing.
            </p>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-[var(--brand)] px-5 py-3 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                @if($isLast)
                    Submit quiz <i class="fas fa-check text-xs"></i>
                @else
                    Next question <i class="fas fa-arrow-right text-xs"></i>
                @endif
            </button>
        </div>
    </form>
</div>

{{-- Stay-on-quiz modal --}}
<div id="quiz-leave-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 p-4" role="dialog" aria-modal="true" aria-labelledby="quiz-leave-title">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-700">
            <i class="fas fa-hourglass-half text-lg"></i>
        </div>
        <h2 id="quiz-leave-title" class="text-center text-xl font-bold text-gray-900">Finish the quiz first</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Your countdown is still running. Complete all questions (or wait for time to end) before opening another page or tab in this portal.
        </p>
        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-center">
            <button type="button" id="quiz-leave-stay"
                    class="inline-flex items-center justify-center rounded-xl bg-[var(--brand)] px-5 py-3 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                Continue quiz
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var timerEl = document.getElementById('quiz-timer');
    var display = document.getElementById('quiz-timer-display');
    var form = document.getElementById('quiz-answer-form');
    var autoField = document.getElementById('auto_submit');
    var modal = document.getElementById('quiz-leave-modal');
    var stayBtn = document.getElementById('quiz-leave-stay');
    if (!timerEl || !display || !form || !modal) return;

    var remaining = parseInt(timerEl.getAttribute('data-remaining') || '0', 10);
    if (isNaN(remaining) || remaining < 0) remaining = 0;
    var allowLeave = false;
    var quizActive = true;

    function format(secs) {
        var m = Math.floor(secs / 60);
        var s = secs % 60;
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function paint() {
        display.textContent = format(remaining);
        if (remaining <= 60) {
            timerEl.classList.remove('bg-slate-900', 'border-slate-200', 'bg-amber-600', 'border-amber-700');
            timerEl.classList.add('bg-red-600', 'border-red-700');
        } else if (remaining <= 180) {
            timerEl.classList.remove('bg-slate-900', 'border-slate-200');
            timerEl.classList.add('bg-amber-600', 'border-amber-700');
        }
    }

    function showLeaveModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideLeaveModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function unlockNavigation() {
        quizActive = false;
        allowLeave = true;
        window.removeEventListener('beforeunload', onBeforeUnload);
    }

    function onBeforeUnload(e) {
        if (!quizActive || remaining <= 0) return;
        e.preventDefault();
        e.returnValue = '';
    }

    paint();

    var tick = setInterval(function () {
        remaining -= 1;
        if (remaining <= 0) {
            remaining = 0;
            paint();
            clearInterval(tick);
            unlockNavigation();
            autoField.value = '1';
            form.querySelectorAll('[required]').forEach(function (el) { el.required = false; });
            form.submit();
            return;
        }
        paint();
    }, 1000);

    window.addEventListener('beforeunload', onBeforeUnload);

    form.addEventListener('submit', function () {
        unlockNavigation();
    });

    stayBtn.addEventListener('click', hideLeaveModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) hideLeaveModal();
    });

    // Block in-app navigation (sidebar, header, logo, etc.)
    document.addEventListener('click', function (e) {
        if (!quizActive || allowLeave) return;
        var link = e.target.closest('a[href]');
        if (!link) return;
        var href = link.getAttribute('href') || '';
        if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
        e.preventDefault();
        e.stopPropagation();
        showLeaveModal();
    }, true);

    // Block browser Back while quiz is active
    history.pushState({ quizLock: 1 }, '', location.href);
    window.addEventListener('popstate', function () {
        if (!quizActive || allowLeave) return;
        history.pushState({ quizLock: 1 }, '', location.href);
        showLeaveModal();
    });
})();
</script>
@endsection
