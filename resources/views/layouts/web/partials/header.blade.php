<!-- Premium Header Section -->
@php
    $trainingCategories = \App\Models\ServiceCategory::navItems('training');
    if ($trainingCategories->isEmpty()) {
        $trainingCategories = collect([
            ['name' => 'NRA', 'url' => route('training-classes', ['category' => 'nra']), 'match' => ['type' => 'training-classes', 'category' => 'nra']],
            ['name' => 'Red Cross', 'url' => route('training-classes', ['category' => 'red_cross']), 'match' => ['type' => 'training-classes', 'category' => 'red_cross']],
            ['name' => 'Enhanced Handgun Carry Permit', 'url' => route('class.show', 'enhanced-handgun-carry-permit'), 'match' => ['type' => 'slug', 'slug' => 'enhanced-handgun-carry-permit']],
            ['name' => 'Active Shooter 8 Hours', 'url' => route('class.show', 'active-shooter'), 'match' => ['type' => 'slug', 'slug' => 'active-shooter']],
            ['name' => 'Force Science (De-Escalation)', 'url' => route('class.show', 'forced-science-de-escalation'), 'match' => ['type' => 'slug', 'slug' => 'forced-science-de-escalation']],
            ['name' => 'Handle With Care', 'url' => route('class.show', 'handle-with-care'), 'match' => ['type' => 'slug', 'slug' => 'handle-with-care']],
        ]);
    }

    $securityCategories = \App\Models\ServiceCategory::navItems('security');
    if ($securityCategories->isEmpty()) {
        $securityCategories = collect([
            ['name' => 'Initial Registration', 'url' => route('intial-security'), 'match' => ['type' => 'route', 'route' => 'intial-security']],
            ['name' => 'Renewal Registration', 'url' => route('renewals'), 'match' => ['type' => 'route', 'route' => 'renewals']],
        ]);
    }
    $path = request()->path();
    $isDallasLawPage = request()->routeIs('dallas-law')
        || (request()->routeIs('class.show') && (string) request()->route('slug') === 'dallas-law');
    $isAsp4HrPage = request()->routeIs('class.show') && (string) request()->route('slug') === 'asp-4-hr';
    $isAffiliatedServicesPage = request()->routeIs('affiliated-services')
        || $path === 'affiliated-services'
        || str_ends_with($path, '/affiliated-services');
    $isNraServicesPage = request()->routeIs('nra-services')
        || $path === 'nra-services'
        || str_ends_with($path, '/nra-services');
    $isAffiliatedPage = $isAffiliatedServicesPage || $isNraServicesPage;
    $activeNavSection = match (true) {
        $isAffiliatedPage => 'affiliated',
        (        request()->routeIs(['training-classes', 'training-classes.show', 'class.show', 'handgun.subcategories'])
            || str_starts_with($path, 'training-classes')
            || str_starts_with($path, 'training-services')) && ! $isDallasLawPage && ! $isAsp4HrPage => 'training',
        request()->routeIs(['security-training', 'intial-security', 'renewals']) || $isDallasLawPage || $isAsp4HrPage => 'security',
        request()->routeIs('about') => 'about',
        request()->routeIs('testimonials') => 'testimonials',
        request()->routeIs('contact') => 'contact',
        default => null,
    };
    $navActive = [
        'home' => $path === '' || $path === '/',
        'about' => request()->routeIs('about'),
        'class_calendar' => request()->routeIs('class-calendar'),
        'training' => (request()->routeIs(['training-classes', 'training-classes.show', 'class.show', 'handgun.subcategories'])
            || str_starts_with($path, 'training-classes')
            || str_starts_with($path, 'training-services')) && ! $isDallasLawPage && ! $isAsp4HrPage,
        'affiliated' => $isAffiliatedPage,
        'security' => request()->routeIs(['security-training', 'intial-security', 'renewals']) || $isDallasLawPage || $isAsp4HrPage,
        'testimonials' => request()->routeIs('testimonials'),
        'contact' => request()->routeIs('contact'),
        'login' => request()->routeIs('student.login'),
        'register' => request()->routeIs('student.register'),
        'dashboard' => (request()->routeIs('student.*')
            && ! request()->routeIs(['student.login', 'student.register', 'student.password.*']))
            || (request()->routeIs('admin.*') && ! request()->routeIs('admin.login')),
        'classes_all' => request()->routeIs('training-classes') && ! request()->filled('category') && ! request()->filled('subcategory'),
    ];
    $servicesAffiliates = $servicesAffiliates ?? collect();
    $nraAffiliates = $nraAffiliates ?? collect();
@endphp

<style>
    .dropdown-simple {
        position: absolute;
        left: 0;
        top: 100%;
        width: 280px;
        padding-top: 0.5rem;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease-out;
        z-index: 100;
        pointer-events: none;
    }
    .nav-group:hover .dropdown-simple {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    .category-item {
        color: #333333;
        padding: 0.75rem 1.5rem;
        padding-left: calc(1.5rem - 4px);
        display: block;
        font-weight: 500;
        font-size: 16px;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
        border-left: 4px solid transparent;
        box-sizing: border-box;
    }
    .category-item:last-child {
        border-bottom: none;
    }
    .category-item:hover {
        background-color: rgba(58, 166, 44, 0.1);
        color: var(--primary-color); 
    }
    .category-item.category-item-active {
        background-color: rgba(58, 166, 44, 0.1);
        color: var(--primary-color);
        font-weight: 600;
        border-left-color: var(--primary-color);
    }
    .category-item.category-item-active:hover {
        background-color: rgba(58, 166, 44, 0.12);
    }
    .mobile-sub-menu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out;
    }
    .mobile-sub-menu.active {
        max-height: 1000px;
        transition: max-height 0.5s ease-in;
    }
    .affiliated-nra-sub > a.category-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        flex-wrap: nowrap;
    }
    .affiliated-nra-sub {
        position: relative;
    }
    .affiliated-nra-flyout {
        position: absolute;
        left: 100%;
        top: 0;
        width: 280px;
        padding-left: 0.35rem;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease-out;
        z-index: 110;
        pointer-events: none;
    }
    .affiliated-nra-sub:hover .affiliated-nra-flyout {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
</style>

<header class="relative w-full z-50 ">
    <div class="container mx-auto px-4 xl:px-8 2xl:px-10">
        <div class="flex items-center justify-between gap-3 h-20 xl:h-24">
            
            <!-- Logo Area -->
            <div class="relative shrink-0 z-[60]">
                <a href="{{ url('/') }}" class="absolute -top-4 left-0">
                    @if($siteSettings && $siteSettings->header_logo)
                        <img src="{{ asset('storage/' . $siteSettings->header_logo) }}" 
                             alt="TN Veterans Logo" 
                             class="header-logo">
                    @else
                        <img src="{{ asset('images/securty-logo.png') }}" 
                             alt="TN Veterans Logo" 
                             class="header-logo">
                    @endif
                </a>
                <!-- Spacing block to push navigation to the right -->
                <div class="w-24 md:w-32 xl:w-40 2xl:w-48"></div>
            </div>

            <!-- Desktop Navigation (xl+ only — avoids cramped mid-size layouts) -->
            <nav class="desktop-nav hidden xl:flex flex-1 items-center justify-end gap-x-3 2xl:gap-x-5 text-[13px] 2xl:text-[14px] font-medium text-[var(--text-color)] whitespace-nowrap min-w-0">
                <a href="{{ url('/') }}" data-nav-section="home" class="destop-nav-link {{ $navActive['home'] ? 'nav-link-active' : '' }}">Home</a>
                <a href="{{ route('about') }}" data-nav-section="about" class="destop-nav-link {{ $navActive['about'] ? 'nav-link-active' : '' }}">About Us</a>
                
                <a href="{{ route('class-calendar') }}" data-nav-section="training" class="destop-nav-link {{ $navActive['class_calendar'] ? 'nav-link-active' : '' }}">Class Calendar</a>

                <!-- Training Services with Mega Menu -->
                <div class="relative nav-group h-full flex items-center">
                    <a href="{{ route('training-classes') }}" data-nav-section="training" class="destop-nav-link flex items-center gap-1 py-6 {{ $navActive['training'] ? 'nav-link-active' : '' }}">
                        Training & Classes
                        <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    
                    <div class="dropdown-simple">
                        <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden py-2">
                            @foreach($trainingCategories as $cat)
                                @php
                                    $catItemActive = false;
                                    if (($cat['match']['type'] ?? '') === 'services') {
                                        $catItemActive = request()->routeIs('training-classes') && request('category') === ($cat['match']['category'] ?? null);
                                    } elseif (($cat['match']['type'] ?? '') === 'slug') {
                                        $catItemActive = request()->routeIs('class.show') && (string) request()->route('slug') === (string) ($cat['match']['slug'] ?? '');
                                    } elseif (($cat['match']['type'] ?? '') === 'route') {
                                        $catItemActive = request()->routeIs($cat['match']['route'] ?? '');
                                    }
                                @endphp
                                <a href="{{ $cat['url'] }}" class="category-item {{ $catItemActive ? 'category-item-active' : '' }}">
                                    {{ $cat['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Affiliated dropdown -->
                <div class="relative nav-group h-full flex items-center">
                    <a href="{{ route('affiliated-services') }}" data-nav-section="affiliated" class="js-affiliated-nav destop-nav-link flex items-center gap-1 py-6 {{ $navActive['affiliated'] ? 'nav-link-active' : '' }}">
                        Affiliated
                        <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <div class="dropdown-simple">
                        <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-visible py-2">
                            @foreach($servicesAffiliates as $aff)
                                <a href="{{ $aff->url }}" data-nav-section="affiliated" class="category-item js-affiliated-trigger" @if(str_starts_with($aff->url, 'http')) target="_blank" rel="noopener noreferrer" @endif>
                                    {{ $aff->name }}
                                </a>
                            @endforeach
                            <div class="affiliated-nra-sub border-t border-gray-100">
                                <a href="{{ route('nra-services') }}" data-nav-section="affiliated" class="category-item js-affiliated-trigger {{ $isNraServicesPage ? 'category-item-active' : '' }}">
                                    <span class="min-w-0">NRA</span>
                                    <svg class="w-4 h-4 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                <div class="affiliated-nra-flyout">
                                    <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden py-2">
                                        @foreach($nraAffiliates as $aff)
                                            <a href="{{ $aff->url }}" data-nav-section="affiliated" class="category-item js-affiliated-trigger" @if(str_starts_with($aff->url, 'http')) target="_blank" rel="noopener noreferrer" @endif>
                                                {{ $aff->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Training with dropdown (Initial Security, Renewals) -->
                <div class="relative nav-group h-full flex items-center">
                    <a href="{{ route('security-training') }}" data-nav-section="security" class="destop-nav-link flex items-center gap-1 py-6 cursor-default {{ $navActive['security'] ? 'nav-link-active' : '' }}">
                        Security Training

                        <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <div class="dropdown-simple">
                        <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden py-2">
                            @foreach($securityCategories as $cat)
                                @php
                                    $secItemActive = ($cat['match']['type'] ?? '') === 'route'
                                        ? request()->routeIs($cat['match']['route'] ?? '')
                                        : false;
                                @endphp
                                <a href="{{ $cat['url'] }}" class="category-item {{ $secItemActive ? 'category-item-active' : '' }}">{{ $cat['name'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <a href="{{ route('testimonials') }}" data-nav-section="testimonials" class="destop-nav-link {{ $navActive['testimonials'] ? 'nav-link-active' : '' }}">Testimonials</a>
                <a href="{{ route('contact') }}" data-nav-section="contact" class="destop-nav-link {{ $navActive['contact'] ? 'nav-link-active' : '' }}">Contact Us</a>
            </nav>

            <!-- Desktop auth (xl+) -->
            <div class="hidden xl:flex items-center shrink-0 gap-3 pl-3 border-l border-gray-200 ml-1">
                @if(Auth::guard('web')->check())
                    <a href="{{ route('admin.dashboard') }}" class="destop-nav-link whitespace-nowrap {{ $navActive['dashboard'] ? 'nav-link-active' : '' }}">Dashboard</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="destop-nav-link">Logout</button>
                    </form>
                @elseif(Auth::guard('student')->check())
                    <a href="{{ route('student.dashboard') }}" class="destop-nav-link whitespace-nowrap {{ $navActive['dashboard'] ? 'nav-link-active' : '' }}">Dashboard</a>
                    <form method="POST" action="{{ route('student.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="destop-nav-link">Logout</button>
                    </form>
                @else
                    <a href="{{ route('student.login') }}" class="destop-nav-link whitespace-nowrap {{ $navActive['login'] ? 'nav-link-active' : '' }}">Login</a>
                    <a href="{{ route('student.register') }}" class="header-signup-btn primary-button {{ $navActive['register'] ? 'ring-2 ring-offset-2 ring-[var(--primary-color)]' : '' }}">
                        Sign Up
                    </a>
                @endif
            </div>

            <!-- Hamburger (<1280px) -->
            <button id="menuBtn" type="button" class="xl:hidden text-3xl text-gray-800 focus:outline-none p-2 shrink-0" aria-label="Open menu">
                <span id="menuIcon">☰</span>
            </button>
        </div>
    </div>

    <!-- Mobile / tablet menu -->
    <div id="mobileMenu" class="hidden xl:hidden overflow-hidden transition-all duration-300 bg-white border-t border-gray-100 shadow-lg">
        <nav class="flex flex-col px-6 pb-6 pt-4 space-y-1 mt-28 md:mt-32">
            <a href="{{ url('/') }}" data-nav-section="home" class="mobile-nav-links {{ $navActive['home'] ? 'nav-link-active' : '' }}">Home</a>
            <a href="{{ route('about') }}" data-nav-section="about" class="mobile-nav-links {{ $navActive['about'] ? 'nav-link-active' : '' }}">About Us</a>
            <a href="{{ route('class-calendar') }}" data-nav-section="training" class="mobile-nav-links {{ $navActive['class_calendar'] ? 'nav-link-active' : '' }}">Class Calendar</a>
            
            <!-- Mobile Training Services Accordion -->
            <div class="mobile-nav-group">
                <button id="mobileServiceToggle" type="button" data-nav-section="training" class="mobile-nav-links w-full flex items-center justify-between focus:outline-none {{ $navActive['training'] ? 'nav-link-active' : '' }}">
                    <span>Training & Classes</span>
                    <svg id="mobileServiceIcon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="mobileServiceMenu" class="mobile-sub-menu bg-gray-50 rounded-xl mx-2">
                    <div class="p-4 grid grid-cols-1 gap-2">
                        @foreach($trainingCategories as $cat)
                            @php
                                $mCatItemActive = false;
                                if (($cat['match']['type'] ?? '') === 'services') {
                                    $mCatItemActive = request()->routeIs('training-classes') && request('category') === ($cat['match']['category'] ?? null);
                                } elseif (($cat['match']['type'] ?? '') === 'slug') {
                                    $mCatItemActive = request()->routeIs('class.show') && (string) request()->route('slug') === (string) ($cat['match']['slug'] ?? '');
                                } elseif (($cat['match']['type'] ?? '') === 'route') {
                                    $mCatItemActive = request()->routeIs($cat['match']['route'] ?? '');
                                }
                            @endphp
                            <a href="{{ $cat['url'] }}" class="mobile-nav-links text-[16px]! py-3 px-4 hover:bg-white rounded-lg block border-l-4 {{ $mCatItemActive ? 'border-(--primary-color) bg-emerald-50 font-semibold text-(--primary-color)' : 'border-transparent hover:border-(--primary-color)' }}">
                                {{ $cat['name'] }}
                            </a>
                        @endforeach 
                    </div>
                </div>
            </div>

            <!-- Mobile Services (affiliates) accordion -->
            <div class="mobile-nav-group">

                <button id="mobileServicesToggle" type="button" data-nav-section="affiliated" class="js-affiliated-nav mobile-nav-links w-full flex items-center justify-between focus:outline-none {{ $navActive['affiliated'] ? 'nav-link-active' : '' }}">
                    <span>Affiliated</span>
                    <svg id="mobileServicesIcon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="mobileServicesMenu" class="mobile-sub-menu bg-gray-50 rounded-xl mx-2">
                    <div class="p-4 grid grid-cols-1 gap-2">
                        @foreach($servicesAffiliates as $aff)
                            <a href="{{ $aff->url }}" data-nav-section="affiliated" class="mobile-nav-links js-affiliated-trigger text-[16px]! py-3 px-4 hover:bg-white rounded-lg block border-l-4 border-transparent hover:border-(--primary-color)" @if(str_starts_with($aff->url, 'http')) target="_blank" rel="noopener noreferrer" @endif>
                                {{ $aff->name }}
                            </a>
                        @endforeach
                        <div class="border-t border-gray-200 pt-3 mt-1 col-span-1">
                            <a href="{{ route('nra-services') }}" data-nav-section="affiliated" class="mobile-nav-links js-affiliated-trigger text-[16px]! py-2 px-4 font-semibold text-gray-800 block border-l-4 {{ $isNraServicesPage ? 'border-(--primary-color) bg-emerald-50 text-(--primary-color)' : 'border-transparent' }}">NRA</a>
                            @foreach($nraAffiliates as $aff)
                                <a href="{{ $aff->url }}" data-nav-section="affiliated" class="mobile-nav-links js-affiliated-trigger text-[16px]! py-2.5 pl-6 pr-4 hover:bg-white rounded-lg block border-l-4 border-transparent hover:border-(--primary-color) text-gray-700" @if(str_starts_with($aff->url, 'http')) target="_blank" rel="noopener noreferrer" @endif>
                                    {{ $aff->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Security Training accordion -->
            <div class="mobile-nav-group">
                <button id="mobileSecurityTrainingToggle" type="button" data-nav-section="security" class="mobile-nav-links w-full flex items-center justify-between focus:outline-none {{ $navActive['security'] ? 'nav-link-active' : '' }}">
                    <span>Security Training</span>
                    <svg id="mobileSecurityTrainingIcon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="mobileSecurityTrainingMenu" class="mobile-sub-menu bg-gray-50 rounded-xl mx-2">
                    <div class="p-4 grid grid-cols-1 gap-2">
                        @foreach($securityCategories as $cat)
                            @php
                                $mSecActive = ($cat['match']['type'] ?? '') === 'route'
                                    ? request()->routeIs($cat['match']['route'] ?? '')
                                    : false;
                            @endphp
                            <a href="{{ $cat['url'] }}" class="mobile-nav-links text-[16px]! py-3 px-4 hover:bg-white rounded-lg block border-l-4 {{ $mSecActive ? 'border-(--primary-color) bg-emerald-50 font-semibold text-(--primary-color)' : 'border-transparent hover:border-(--primary-color)' }}">{{ $cat['name'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <a href="{{ route('testimonials') }}" data-nav-section="testimonials" class="mobile-nav-links {{ $navActive['testimonials'] ? 'nav-link-active' : '' }}">Testimonials</a>
            <a href="{{ route('contact') }}" data-nav-section="contact" class="mobile-nav-links {{ $navActive['contact'] ? 'nav-link-active' : '' }}">Contact Us</a>
            @if(Auth::guard('web')->check())
                <a href="{{ route('admin.dashboard') }}" class="mobile-nav-links {{ $navActive['dashboard'] ? 'nav-link-active' : '' }}">Dashboard</a>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline w-full">
                    @csrf
                    <button type="submit" class="mobile-nav-links w-full text-left">Logout</button>
                </form>
            @elseif(Auth::guard('student')->check())
                <a href="{{ route('student.dashboard') }}" class="mobile-nav-links {{ $navActive['dashboard'] ? 'nav-link-active' : '' }}">Dashboard</a>
                <form method="POST" action="{{ route('student.logout') }}" class="inline w-full">
                    @csrf
                    <button type="submit" class="mobile-nav-links w-full text-left">Logout</button>
                </form>
            @else
                <a href="{{ route('student.login') }}" class="mobile-nav-links {{ $navActive['login'] ? 'nav-link-active' : '' }}">Login</a>
                <a href="{{ route('student.register') }}" class="mobile-nav-links {{ $navActive['register'] ? 'nav-link-active' : '' }}">Sign Up</a>
            @endif
            <div class="pt-6">
                <a href="{{ route('contact') }}" class="block w-full bg-(--primary-color) text-white text-center py-4 rounded-xl font-bold uppercase tracking-widest shadow-lg">
                    Contact Us
                </a>
            </div>
        </nav>
    </div>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const menuBtn = document.getElementById("menuBtn");
        const mobileMenu = document.getElementById("mobileMenu");
        const menuIcon = document.getElementById("menuIcon");

        menuBtn.addEventListener("click", () => {
            mobileMenu.classList.toggle("hidden");
            
            if (mobileMenu.classList.contains("hidden")) {
                menuIcon.innerText = "☰";
            } else {
                menuIcon.innerText = "✕";
            }
        });

        // Close menu when a link is clicked
        const links = mobileMenu.querySelectorAll('nav > a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuIcon.innerText = "☰";
            });
        });

        // Mobile Service Sub-menu Toggle
        const mobileServiceToggle = document.getElementById("mobileServiceToggle");
        const mobileServiceMenu = document.getElementById("mobileServiceMenu");
        const mobileServiceIcon = document.getElementById("mobileServiceIcon");

        if (mobileServiceToggle) {
            mobileServiceToggle.addEventListener("click", () => {
                mobileServiceMenu.classList.toggle("active");
                mobileServiceIcon.classList.toggle("rotate-180");
            });
        }

        // Mobile Services (affiliates) sub-menu toggle
        const mobileServicesToggle = document.getElementById("mobileServicesToggle");
        const mobileServicesMenu = document.getElementById("mobileServicesMenu");
        const mobileServicesIcon = document.getElementById("mobileServicesIcon");
        if (mobileServicesToggle) {
            mobileServicesToggle.addEventListener("click", () => {
                mobileServicesMenu.classList.toggle("active");
                mobileServicesIcon.classList.toggle("rotate-180");
            });
            @if($isAffiliatedPage)
            mobileServicesMenu.classList.add("active");
            mobileServicesIcon.classList.add("rotate-180");
            @endif
        }

        // Mobile Security Training sub-menu toggle
        const mobileSecurityTrainingToggle = document.getElementById("mobileSecurityTrainingToggle");
        const mobileSecurityTrainingMenu = document.getElementById("mobileSecurityTrainingMenu");
        const mobileSecurityTrainingIcon = document.getElementById("mobileSecurityTrainingIcon");
        if (mobileSecurityTrainingToggle) {
            mobileSecurityTrainingToggle.addEventListener("click", () => {
                mobileSecurityTrainingMenu.classList.toggle("active");
                mobileSecurityTrainingIcon.classList.toggle("rotate-180");
            });
        }

        // Keep Affiliated nav active when partner links open in a new tab
        const navActiveStorageKey = 'tnvs-nav-active-section';
        const serverNavSection = @json($activeNavSection);
        const affiliatedNavEls = document.querySelectorAll('.js-affiliated-nav');

        function setAffiliatedNavActive(active) {
            affiliatedNavEls.forEach(function(el) {
                el.classList.toggle('nav-link-active', active);
            });
        }

        function persistNavSection(section) {
            if (section) {
                sessionStorage.setItem(navActiveStorageKey, section);
            } else {
                sessionStorage.removeItem(navActiveStorageKey);
            }
        }

        if (serverNavSection === 'affiliated') {
            persistNavSection('affiliated');
            setAffiliatedNavActive(true);
        } else if (serverNavSection) {
            persistNavSection(serverNavSection);
            setAffiliatedNavActive(false);
        } else if (sessionStorage.getItem(navActiveStorageKey) === 'affiliated') {
            setAffiliatedNavActive(true);
        }

        document.querySelectorAll('.js-affiliated-trigger, .js-affiliated-nav[data-nav-section="affiliated"]').forEach(function(el) {
            el.addEventListener('click', function() {
                persistNavSection('affiliated');
                setAffiliatedNavActive(true);
            });
        });

        document.querySelectorAll('[data-nav-section]:not(.js-affiliated-trigger):not(.js-affiliated-nav)').forEach(function(el) {
            el.addEventListener('click', function() {
                const section = el.getAttribute('data-nav-section');
                if (section && section !== 'affiliated') {
                    persistNavSection(section);
                    setAffiliatedNavActive(false);
                }
            });
        });
    });
</script>



