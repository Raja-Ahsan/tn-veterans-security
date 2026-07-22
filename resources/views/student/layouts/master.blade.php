<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') - TN Veterans Security</title>

    @if($siteSettings && $siteSettings->favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon) }}?v={{ $siteSettings->updated_at->timestamp ?? time() }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand: #3AA62C;
            --brand-dark: #175B0E;
            --sidebar: #1a2332;
        }
        .student-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #cbd5e1;
            transition: background-color 0.15s, color 0.15s;
        }
        .student-nav-link:hover {
            background-color: rgba(255, 255, 255, 0.06);
            color: #fff;
        }
        .student-nav-link.is-active {
            background-color: rgba(58, 166, 44, 0.18);
            color: #fff;
            box-shadow: inset 3px 0 0 var(--brand);
        }
        .student-nav-link i {
            width: 1.25rem;
            text-align: center;
            opacity: 0.9;
        }
    </style>
    @stack('head')
</head>
<body class="bg-[#f3f5f7] antialiased">
    <div class="flex min-h-screen flex-col">
        <nav class="z-40 border-b border-gray-200/80 bg-white max-lg:sticky max-lg:top-0">
            <div class="mx-auto flex h-14 min-h-[3.5rem] max-w-[1600px] items-center justify-between gap-3 px-4 sm:h-16 sm:px-6 lg:px-8">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <button
                        type="button"
                        id="student-sidebar-open"
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 lg:hidden"
                        aria-label="Open menu"
                        aria-expanded="false"
                        aria-controls="student-sidebar"
                    >
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-2.5">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[var(--brand)] text-sm font-bold text-white shadow-sm">
                            TN
                        </span>
                        <span class="truncate text-base font-bold tracking-tight text-gray-900 sm:text-lg">
                            Veterans <span class="text-[var(--brand)]">Security</span>
                        </span>
                    </a>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <a
                        href="{{ url('/') }}"
                        class="hidden items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-[var(--brand)] sm:inline-flex"
                    >
                        <i class="fas fa-arrow-left text-xs"></i>
                        Website
                    </a>
                    @auth('student')
                        <div class="hidden items-center gap-2 rounded-full border border-gray-200 bg-gray-50 py-1.5 pl-1.5 pr-3 md:flex">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-[var(--brand)] text-xs font-bold text-white">
                                {{ strtoupper(substr(Auth::guard('student')->user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[10rem] truncate text-sm font-medium text-gray-700">
                                {{ Auth::guard('student')->user()->name }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('student.logout') }}" class="inline">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                            >
                                <i class="fas fa-sign-out-alt text-xs"></i>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        <div class="relative flex min-h-0 flex-1">
            <div
                id="student-sidebar-backdrop"
                class="fixed left-0 right-0 top-14 z-50 bg-black/50 sm:top-16 lg:hidden hidden"
                aria-hidden="true"
            ></div>

            <div
                id="student-sidebar"
                class="fixed bottom-0 left-0 top-14 z-[60] w-[15.5rem] max-w-[min(100vw,15.5rem)] shrink-0 -translate-x-full bg-[var(--sidebar)] text-white transition-transform duration-200 ease-out sm:top-16 lg:static lg:top-auto lg:z-auto lg:max-w-none lg:translate-x-0"
            >
                <aside class="flex h-full min-h-0 flex-col overflow-y-auto lg:sticky lg:top-0 lg:h-[calc(100vh-4rem)] lg:max-h-[calc(100vh-4rem)]">
                    <div class="flex items-start justify-between gap-2 border-b border-white/10 p-4 lg:hidden">
                        <span class="text-sm font-semibold text-slate-300">Student Menu</span>
                        <button
                            type="button"
                            id="student-sidebar-close"
                            class="rounded-lg p-2 text-slate-300 hover:bg-white/10 hover:text-white"
                            aria-label="Close menu"
                        >
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <div class="hidden border-b border-white/10 px-5 py-5 lg:block">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Student Portal</p>
                        <p class="mt-1 truncate text-sm font-medium text-white">
                            {{ Auth::guard('student')->user()->name ?? 'Account' }}
                        </p>
                    </div>

                    <nav class="flex flex-1 flex-col gap-1 p-3 sm:p-4">
                        <a href="{{ route('student.dashboard') }}" class="student-nav-link {{ request()->routeIs('student.dashboard') ? 'is-active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="{{ route('student.bookings') }}" class="student-nav-link {{ request()->routeIs('student.bookings*') ? 'is-active' : '' }}">
                            <i class="fas fa-calendar-check"></i> My Bookings
                        </a>
                        <a href="{{ route('student.online-courses.index') }}" class="student-nav-link {{ request()->routeIs('student.online-courses*') || request()->routeIs('student.online-course*') ? 'is-active' : '' }}">
                            <i class="fas fa-laptop"></i> Online Courses
                        </a>
                        <a href="{{ route('student.certificates.index') }}" class="student-nav-link {{ request()->routeIs('student.certificates*') ? 'is-active' : '' }}">
                            <i class="fas fa-certificate"></i> Certificates
                        </a>
                        <a href="{{ route('student.payment-history') }}" class="student-nav-link {{ request()->routeIs('student.payment-history') ? 'is-active' : '' }}">
                            <i class="fas fa-receipt"></i> Payment History
                        </a>
                        <a href="{{ route('student.profile') }}" class="student-nav-link {{ request()->routeIs('student.profile*') ? 'is-active' : '' }}">
                            <i class="fas fa-user"></i> My Profile
                        </a>

                        <div class="mt-auto border-t border-white/10 pt-3 lg:hidden">
                            <a href="{{ url('/') }}" class="student-nav-link">
                                <i class="fas fa-home"></i> Back to Website
                            </a>
                        </div>
                    </nav>
                </aside>
            </div>

            <main class="min-w-0 flex-1">
                <div class="w-full overflow-x-hidden p-4 sm:p-5 lg:p-6">

                @if(session('success'))
                    <div id="student-flash-alert" role="alert" class="fixed top-20 right-4 z-[70] max-w-sm rounded-xl border border-green-200 bg-white px-5 py-4 text-green-800 shadow-lg transition-opacity duration-300">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-check-circle mt-0.5 text-[var(--brand)]"></i>
                            <p class="flex-1 text-sm font-medium">{{ session('success') }}</p>
                            <button type="button" onclick="dismissStudentFlash()" class="text-green-700 hover:text-green-900" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div id="student-flash-alert" role="alert" class="fixed top-20 right-4 z-[70] max-w-sm rounded-xl border border-red-200 bg-white px-5 py-4 text-red-800 shadow-lg transition-opacity duration-300">
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
                    <div id="student-flash-alert" role="alert" class="fixed top-20 right-4 z-[70] max-w-sm rounded-xl border border-amber-200 bg-white px-5 py-4 text-amber-900 shadow-lg transition-opacity duration-300">
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
                    <div id="student-flash-alert" role="alert" class="fixed top-20 right-4 z-[70] max-w-sm rounded-xl border border-blue-200 bg-white px-5 py-4 text-blue-800 shadow-lg transition-opacity duration-300">
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
