@extends('admin.layouts.master')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
@php
    $categoryStyles = [
        'registration' => 'text-green-700',
        'booking' => 'text-sky-700',
        'enrollment' => 'text-green-700',
        'quiz' => 'text-violet-700',
        'blended' => 'text-cyan-700',
        'contact' => 'text-amber-700',
        'payment' => 'text-emerald-700',
        'general' => 'text-gray-700',
    ];

    $iconMap = [
        'bell' => 'fa-bell',
        'user' => 'fa-user-plus',
        'calendar' => 'fa-calendar-check',
        'credit-card' => 'fa-credit-card',
        'book' => 'fa-book-open',
        'check' => 'fa-circle-check',
        'graduation' => 'fa-graduation-cap',
        'envelope' => 'fa-envelope',
        'info' => 'fa-circle-info',
    ];

    $hasNotifications = $notifications->total() > 0;
@endphp

<div class="mx-auto max-w-3xl">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Admin Notifications</h3>
            <p class="mt-1 text-sm text-gray-500">Registrations, bookings, enrollments, quizzes, and contact forms.</p>
        </div>
        @if($hasNotifications)
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                    <i class="fas fa-check"></i>
                    Clear All
                </button>
            </form>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $category = $data['category'] ?? 'general';
                $titleClass = $categoryStyles[$category] ?? $categoryStyles['general'];
                $faIcon = $iconMap[$data['icon'] ?? 'bell'] ?? 'fa-bell';
            @endphp
            <div class="flex items-stretch border-b border-gray-100 last:border-b-0">
                <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}" class="min-w-0 flex-1">
                    @csrf
                    <button type="submit" class="flex w-full gap-4 px-5 py-4 text-left transition hover:bg-gray-50">
                        <span class="mt-0.5 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-50 text-green-700">
                            <i class="fas {{ $faIcon }}"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="mb-1 flex items-center gap-2 text-sm font-semibold {{ $titleClass }}">
                                <span class="inline-block h-2 w-2 rounded-full bg-green-600"></span>
                                {{ $data['title'] ?? 'Notification' }}
                            </span>
                            <span class="block text-sm text-gray-600">{{ $data['body'] ?? '' }}</span>
                            <span class="mt-2 block text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}" class="flex items-center pr-3">
                    @csrf
                    <input type="hidden" name="dismiss" value="1">
                    <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-600" title="Dismiss">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
        @empty
            <div class="px-5 py-16 text-center">
                <span class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-gray-50 text-gray-300">
                    <i class="fas fa-bell-slash text-2xl"></i>
                </span>
                <p class="text-base font-semibold text-gray-800">You're all caught up</p>
                <p class="mx-auto mt-1 max-w-sm text-sm text-gray-500">Student activity alerts will appear here.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-5">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
