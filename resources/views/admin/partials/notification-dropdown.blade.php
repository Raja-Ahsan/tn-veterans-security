@php
    $headerNotifications = collect($headerNotifications ?? []);
    $unreadNotificationCount = (int) ($unreadNotificationCount ?? $headerNotifications->count());

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
@endphp

<div class="relative" id="admin-notification-root">
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
            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">
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

        <div class="max-h-[22rem] overflow-y-auto">
            @forelse($headerNotifications as $notification)
                @php
                    $data = $notification->data;
                    $category = $data['category'] ?? 'general';
                    $titleClass = $categoryStyles[$category] ?? $categoryStyles['general'];
                    $iconKey = $data['icon'] ?? 'bell';
                    $faIcon = $iconMap[$iconKey] ?? 'fa-bell';
                @endphp
                <div class="flex items-stretch border-b border-gray-100 last:border-b-0">
                    <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}" class="min-w-0 flex-1">
                        @csrf
                        <button type="submit" class="flex w-full gap-3 px-4 py-3.5 text-left transition hover:bg-gray-50">
                            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-50 text-green-700">
                                <i class="fas {{ $faIcon }}"></i>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="mb-0.5 flex items-center gap-1.5 text-sm font-semibold {{ $titleClass }}">
                                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                    {{ $data['title'] ?? 'Notification' }}
                                </span>
                                <span class="block text-sm leading-snug text-gray-600">{{ $data['body'] ?? '' }}</span>
                                <span class="mt-1.5 block text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}" class="flex items-center pr-2">
                        @csrf
                        <input type="hidden" name="dismiss" value="1">
                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-600" title="Dismiss" aria-label="Dismiss notification">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="px-4 py-10 text-center">
                    <span class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-50 text-gray-300">
                        <i class="fas fa-bell-slash text-xl"></i>
                    </span>
                    <p class="text-sm font-medium text-gray-700">You're all caught up</p>
                    <p class="mt-1 text-xs text-gray-400">No new admin alerts</p>
                </div>
            @endforelse
        </div>

        <div class="grid grid-cols-1 gap-2 border-t border-gray-100 bg-gray-50 p-3 {{ $unreadNotificationCount > 0 ? 'sm:grid-cols-2' : '' }}">
            <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 transition hover:bg-gray-50">
                View All
            </a>
            @if($unreadNotificationCount > 0)
                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700">
                        <i class="fas fa-check"></i>
                        Clear All
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
