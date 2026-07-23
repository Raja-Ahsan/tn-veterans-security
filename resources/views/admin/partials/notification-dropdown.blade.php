@php
    $headerNotifications = collect($headerNotifications ?? []);
    $unreadNotificationCount = (int) ($unreadNotificationCount ?? $headerNotifications->count());
@endphp

<div
    class="relative"
    id="admin-notification-root"
    data-poll-url="{{ route('admin.notifications.poll') }}"
>
    <button
        type="button"
        id="admin-notification-toggle"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 hover:text-green-700"
        aria-label="{{ $unreadNotificationCount > 0 ? 'Notifications, '.$unreadNotificationCount.' unread' : 'Notifications' }}"
        aria-expanded="false"
        aria-controls="admin-notification-panel"
    >
        <i class="fas fa-bell text-lg" aria-hidden="true"></i>
        @if($unreadNotificationCount > 0)
            <span id="admin-notification-badge" class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">
                {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
            </span>
        @endif
    </button>

    <div
        id="admin-notification-panel"
        class="absolute right-0 z-[80] mt-2 hidden w-[min(100vw-2rem,22rem)] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl sm:w-[24rem]"
        role="dialog"
        aria-label="Notifications"
    >
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
            <h2 class="text-base font-semibold text-gray-900">Notifications</h2>
            <button
                type="button"
                id="admin-notification-close"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-gray-800"
                aria-label="Close notifications"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="admin-notification-list" class="max-h-[22rem] overflow-y-auto">
            {{-- Filled by live polling --}}
        </div>

        <div
            id="admin-notification-footer"
            class="grid grid-cols-1 gap-2 border-t border-gray-100 bg-gray-50 p-3 {{ $unreadNotificationCount > 0 ? 'sm:grid-cols-2' : '' }}"
        >
            <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-50">
                View All
            </a>
        </div>
    </div>
</div>
