@extends('student.layouts.master')

@section('title', 'Online Courses')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">My Online Courses</h1>
    <p class="mt-1 text-sm text-gray-500">Complete modules and quizzes for your blended classes (90% pass required).</p>
</div>

<div class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50/70 px-5 py-4">
    <p class="text-sm font-semibold text-gray-900">How online quizzes unlock</p>
    <ol class="mt-2 list-decimal space-y-1 pl-5 text-sm text-gray-700">
        <li>Book a <strong>blended</strong> class (has online modules).</li>
        <li>Pay the <strong>deposit</strong> for that booking.</li>
        <li>Return here and click <strong>Continue Course</strong> to take module quizzes.</li>
    </ol>
</div>

@if($courses->isNotEmpty())
    <div class="mb-8 grid gap-4">
        @foreach($courses as $course)
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $course->title }}</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Progress: <strong>{{ $course->online_progress['completed'] }}</strong> / {{ $course->online_progress['total'] }} modules
                        </p>
                        @if($course->online_progress['eligible_in_person'])
                            <p class="mt-1 text-sm font-medium text-emerald-700">
                                <i class="fas fa-check-circle"></i> Eligible for in-person testing
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('student.online-course.index', $course) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-[var(--brand)] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                        <i class="fas fa-play text-xs"></i> Continue Course
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif

@if($pendingUnlockBookings->isNotEmpty())
    <div class="mb-8">
        <h2 class="mb-3 text-lg font-bold text-gray-900">Almost unlocked</h2>
        <p class="mb-4 text-sm text-gray-500">These blended bookings still need a deposit payment before quizzes open.</p>
        <div class="grid gap-3">
            @foreach($pendingUnlockBookings as $booking)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $booking->service->title }}</p>
                            <p class="mt-1 text-sm text-amber-800">
                                Deposit required: ${{ number_format((float) ($booking->deposit_amount ?? 0), 2) }}
                            </p>
                        </div>
                        <a href="{{ route('student.booking.payment', $booking->id) }}"
                           class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
                            <i class="fas fa-credit-card text-xs"></i> Pay Deposit to Unlock
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($courses->isEmpty() && $pendingUnlockBookings->isEmpty())
    <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center shadow-sm">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
            <i class="fas fa-laptop text-2xl"></i>
        </div>
        <h2 class="text-lg font-bold text-gray-900">No online courses yet</h2>
        <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
            Book a blended class, pay the deposit, then your modules and quizzes will appear here.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('services') }}" class="inline-flex items-center gap-2 rounded-lg bg-[var(--brand)] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[var(--brand-dark)]">
                Browse Classes
            </a>
            <a href="{{ route('student.bookings') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                My Bookings
            </a>
        </div>
    </div>
@endif
@endsection
