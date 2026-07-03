<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') - TN Veterans Security</title>

    <!-- Favicon -->
    @if($siteSettings && $siteSettings->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}?v={{ $siteSettings->updated_at->timestamp ?? time() }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('head')
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen flex-col">
        <!-- Top Navigation -->
        <nav class="z-40 bg-white shadow-md max-lg:sticky max-lg:top-0">
            <div class="mx-auto max-w-7xl px-4 lg:px-10">
                <div class="flex h-14 min-h-[3.5rem] items-center justify-between gap-2 sm:h-16">
                    <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                        <button
                            type="button"
                            id="student-sidebar-open"
                            class="shrink-0 rounded p-2 text-gray-600 hover:bg-gray-100 lg:hidden"
                            aria-label="Open menu"
                            aria-expanded="false"
                            aria-controls="student-sidebar"
                        >
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <a href="{{ url('/') }}" class="truncate text-lg font-bold text-[#3AA62C] sm:text-xl">
                            TN Veterans Security
                        </a>
                    </div>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-4">
                        <a href="{{ url('/') }}" class="hidden text-sm text-gray-600 hover:text-[#3AA62C] sm:inline">
                            Back to Website
                        </a>
                        @auth('student')
                            <span class="hidden max-w-[9rem] truncate text-sm text-gray-600 md:inline md:max-w-xs">
                                Welcome, {{ Auth::guard('student')->user()->name }}
                            </span>
                            <form method="POST" action="{{ route('student.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-gray-600 hover:text-red-600">Logout</button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="relative flex min-h-0 flex-1">
            <!-- Below header on mobile; full column height on lg+ -->
            <div
                id="student-sidebar-backdrop"
                class="fixed left-0 right-0 top-14 z-50 bg-black/50 sm:top-16 lg:hidden hidden"
                aria-hidden="true"
            ></div>

            <!-- Sidebar: drawer below sticky nav; above nav when open -->
            <div
                id="student-sidebar"
                class="fixed bottom-0 left-0 top-14 z-[60] w-64 max-w-[min(100vw,16rem)] shrink-0 -translate-x-full bg-gray-800 text-white transition-transform duration-200 ease-out sm:top-16 lg:static lg:top-auto lg:z-auto lg:max-w-none lg:translate-x-0"
            >
                <aside class="flex h-full min-h-0 flex-col overflow-y-auto lg:sticky lg:top-0 lg:h-screen lg:max-h-screen">
                    <div class="flex items-start justify-between gap-2 border-b border-gray-700/80 p-4 lg:hidden">
                        <span class="text-sm font-semibold text-gray-300">Menu</span>
                        <button
                            type="button"
                            id="student-sidebar-close"
                            class="rounded p-2 text-gray-300 hover:bg-gray-700 hover:text-white"
                            aria-label="Close menu"
                        >
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <nav class="space-y-2 p-4 sm:p-6">
                        <a href="{{ route('student.dashboard') }}" class="block rounded px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('student.dashboard') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                        </a>
                        <a href="{{ route('student.bookings') }}" class="block rounded px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('student.bookings*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-calendar-check mr-3"></i> My Bookings
                        </a>
                        <a href="{{ route('student.online-courses.index') }}" class="block rounded px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('student.online-courses*') || request()->routeIs('student.online-course*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-laptop mr-3"></i> Online Courses
                        </a>
                        <a href="{{ route('student.certificates.index') }}" class="block rounded px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('student.certificates*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-certificate mr-3"></i> Certificates
                        </a>
                        <a href="{{ route('student.payment-history') }}" class="block rounded px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('student.payment-history') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-receipt mr-3"></i> Payment History
                        </a>
                        <a href="{{ route('student.profile') }}" class="block rounded px-4 py-3 hover:bg-gray-700 {{ request()->routeIs('student.profile*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-user mr-3"></i> My Profile
                        </a>
                        <a href="{{ url('/') }}" class="block rounded px-4 py-3 hover:bg-gray-700 lg:hidden">
                            <i class="fas fa-home mr-3"></i> Back to Website
                        </a>
                    </nav>
                </aside>
            </div>

            <main class="min-w-0 flex-1">
                <div class="overflow-x-hidden p-4 sm:p-6">

                @if(session('success'))
                    <div id="student-flash-alert" role="alert" class="fixed top-20 right-4 z-[70] max-w-sm rounded-lg border border-green-400 bg-green-100 px-5 py-4 text-green-800 shadow-lg transition-opacity duration-300">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle mt-0.5 text-green-600"></i>
                            <p class="flex-1 text-sm font-medium">{{ session('success') }}</p>
                            <button type="button" onclick="dismissStudentFlash()" class="text-green-700 hover:text-green-900" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div id="student-flash-alert" role="alert" class="fixed top-20 right-4 z-[70] max-w-sm rounded-lg border border-red-400 bg-red-100 px-5 py-4 text-red-800 shadow-lg transition-opacity duration-300">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle mt-0.5 text-red-600"></i>
                            <p class="flex-1 text-sm font-medium">{{ session('error') }}</p>
                            <button type="button" onclick="dismissStudentFlash()" class="text-red-700 hover:text-red-900" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div id="student-flash-alert" role="alert" class="fixed top-20 right-4 z-[70] max-w-sm rounded-lg border border-amber-400 bg-amber-100 px-5 py-4 text-amber-900 shadow-lg transition-opacity duration-300">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle mt-0.5 text-amber-600"></i>
                            <p class="flex-1 text-sm font-medium">{{ session('warning') }}</p>
                            <button type="button" onclick="dismissStudentFlash()" class="text-amber-800 hover:text-amber-950" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div id="student-flash-alert" role="alert" class="fixed top-20 right-4 z-[70] max-w-sm rounded-lg border border-blue-400 bg-blue-100 px-5 py-4 text-blue-800 shadow-lg transition-opacity duration-300">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle mt-0.5 text-blue-600"></i>
                            <p class="flex-1 text-sm font-medium">{{ session('info') }}</p>
                            <button type="button" onclick="dismissStudentFlash()" class="text-blue-700 hover:text-blue-900" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('student-sidebar');
            const backdrop = document.getElementById('student-sidebar-backdrop');
            const openBtn = document.getElementById('student-sidebar-open');
            const closeBtn = document.getElementById('student-sidebar-close');

            if (!sidebar || !backdrop) {
                return;
            }

            function isDrawerBreakpoint() {
                return window.matchMedia('(max-width: 1023px)').matches;
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
                if (e.key === 'Escape' && isDrawerBreakpoint()) {
                    closeDrawer();
                }
            });

            sidebar.querySelectorAll('nav a[href]').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (isDrawerBreakpoint()) {
                        closeDrawer();
                    }
                });
            });

            window.addEventListener('resize', function () {
                if (!isDrawerBreakpoint()) {
                    closeDrawer();
                }
            });
        })();

        function dismissStudentFlash() {
            const alert = document.getElementById('student-flash-alert');
            if (!alert) {
                return;
            }
            alert.classList.add('opacity-0');
            setTimeout(function () {
                alert.remove();
            }, 300);
        }

        (function () {
            const alert = document.getElementById('student-flash-alert');
            if (!alert) {
                return;
            }
            setTimeout(dismissStudentFlash, 5000);
        })();
    </script>
</body>
</html>
