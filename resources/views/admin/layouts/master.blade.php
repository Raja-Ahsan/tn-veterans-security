<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - TN Veterans Security</title>

    <!-- Favicon -->
    @if($siteSettings && $siteSettings->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}?v={{ $siteSettings->updated_at->timestamp ?? time() }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}?v={{ $siteSettings->updated_at->timestamp ?? time() }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Mobile: tap outside to close -->
        <div
            id="admin-sidebar-backdrop"
            class="fixed inset-0 z-50 bg-black/50 hidden md:hidden"
            aria-hidden="true"
        ></div>

        <!-- Sidebar: drawer on small screens, column on md+ (above sticky header when open) -->
        <div
            id="admin-sidebar"
            class="fixed md:static inset-y-0 left-0 z-[60] w-64 max-w-[min(100vw,16rem)] shrink-0 -translate-x-full bg-gray-800 text-white transition-transform duration-200 ease-out md:z-auto md:translate-x-0"
        >
            <aside class="flex h-full min-h-screen flex-col md:sticky md:top-0 md:h-screen md:min-h-0 md:max-h-screen md:overflow-y-auto">
                <div class="flex items-start justify-between gap-2 p-4 sm:p-6">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold sm:text-2xl">Admin Panel</h1>
                        <p class="mt-1 text-sm text-gray-400">TN Veterans Security</p>
                    </div>
                    <button
                        type="button"
                        id="admin-sidebar-close"
                        class="rounded p-2 text-gray-300 hover:bg-gray-700 hover:text-white md:hidden"
                        aria-label="Close menu"
                    >
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <nav class="mt-2 flex-1 pb-6">
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.students.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-users mr-3"></i> Students
                    </a>
                    <a href="{{ route('admin.classes.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.classes.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-briefcase mr-3"></i> Classes
                    </a>
                    <a href="{{ route('admin.class-schedules.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.class-schedules.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-calendar-check mr-3"></i> Class Schedules
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-calendar-check mr-3"></i> Bookings
                    </a>
                    <a href="{{ route('admin.contact-submissions.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.contact-submissions.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-envelope mr-3"></i> Contact Forms
                    </a>
                    <a href="{{ route('admin.communication-logs.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.communication-logs.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-bullhorn mr-3"></i> Communication Logs
                    </a>
                    <a href="{{ route('admin.instructors.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.instructors.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-chalkboard-teacher mr-3"></i> Instructors
                    </a>
                    <a href="{{ route('admin.locations.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.locations.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-map-marker-alt mr-3"></i> Locations
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.payments.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-money-bill-wave mr-3"></i> Payments
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-cog mr-3"></i> Site Settings
                    </a>
                    <a href="{{ route('admin.profile.show') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6 {{ request()->routeIs('admin.profile.*') ? 'bg-gray-700 border-l-4 border-green-500' : '' }}">
                        <i class="fas fa-user mr-3"></i> My Profile
                    </a>
                    <a href="{{ url('/') }}" class="block px-4 py-3 hover:bg-gray-700 sm:px-6" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-external-link-alt mr-3"></i> View Website
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="mt-4 px-4 sm:px-0">
                        @csrf
                        <button type="submit" class="w-full rounded px-2 py-3 text-left hover:bg-gray-700 sm:px-6">
                            <i class="fas fa-sign-out-alt mr-3"></i> Logout
                        </button>
                    </form>
                </nav>
            </aside>
        </div>

        <!-- Main Content (overflow-x on inner wrapper so sticky header works) -->
        <main class="min-w-0 flex-1">
            <header class="z-40 border-b bg-white shadow-sm max-md:sticky max-md:top-0 md:z-30">
                <div class="flex items-center gap-3 px-4 py-3 sm:px-6 sm:py-4">
                    <button
                        type="button"
                        id="admin-sidebar-open"
                        class="shrink-0 rounded p-2 text-gray-600 hover:bg-gray-100 md:hidden"
                        aria-label="Open menu"
                        aria-expanded="false"
                        aria-controls="admin-sidebar"
                    >
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="min-w-0 flex-1 truncate text-lg font-semibold text-gray-800 sm:text-2xl">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-4">
                        <a href="{{ route('admin.profile.show') }}" class="flex max-w-[40vw] items-center gap-2 hover:opacity-80 sm:max-w-none sm:gap-3">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                     alt="Profile"
                                     class="h-9 w-9 shrink-0 rounded-full border-2 border-gray-300 object-cover sm:h-10 sm:w-10">
                            @else
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-400 sm:h-10 sm:w-10">
                                    <i class="fas fa-user text-sm text-white"></i>
                                </div>
                            @endif
                            <span class="hidden max-w-[8rem] truncate text-sm text-gray-600 sm:inline sm:max-w-none sm:text-base">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </a>
                    </div>
                </div>
            </header>

            <div class="overflow-x-hidden p-4 sm:p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
            });
            @endif

            @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: @json(session('error')),
                showConfirmButton: false,
                timer: 4500,
                timerProgressBar: true,
            });
            @endif

            @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Please fix the form',
                html: `<ul style="text-align:left;margin:0;padding-left:1.25rem;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                confirmButtonColor: '#16a34a',
            });
            @endif
        });
    </script>
    <script>
        (function () {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const openBtn = document.getElementById('admin-sidebar-open');
            const closeBtn = document.getElementById('admin-sidebar-close');

            if (!sidebar || !backdrop) {
                return;
            }

            function isMobile() {
                return window.matchMedia('(max-width: 767px)').matches;
            }

            function openDrawer() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                openBtn?.setAttribute('aria-expanded', 'true');
            }

            function closeDrawer() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                openBtn?.setAttribute('aria-expanded', 'false');
            }

            openBtn?.addEventListener('click', function () {
                openDrawer();
            });

            closeBtn?.addEventListener('click', function () {
                closeDrawer();
            });

            backdrop.addEventListener('click', function () {
                closeDrawer();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && isMobile()) {
                    closeDrawer();
                }
            });

            sidebar.querySelectorAll('nav a[href]').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (isMobile()) {
                        closeDrawer();
                    }
                });
            });

            window.addEventListener('resize', function () {
                if (!isMobile()) {
                    closeDrawer();
                }
            });
        })();
    </script>
</body>
</html>