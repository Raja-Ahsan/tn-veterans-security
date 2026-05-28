<!-- Premium Header Section -->
@php
    $trainingCategories = [
        ['name' => 'NRA', 'url' => route('services', ['category' => 'nra']), 'match' => ['type' => 'services', 'category' => 'nra']],
        ['name' => 'Red Cross', 'url' => route('services', ['category' => 'red_cross']), 'match' => ['type' => 'services', 'category' => 'red_cross']],
        ['name' => 'Enhanced Handgun Carry Permit', 'url' => route('service.by.slug', 'enhanced-handgun-carry-permit'), 'match' => ['type' => 'slug', 'slug' => 'enhanced-handgun-carry-permit']],
        ['name' => 'Active Shooter 8 Hours', 'url' => route('service.by.slug', 'active-shooter'), 'match' => ['type' => 'slug', 'slug' => 'active-shooter']],
        ['name' => 'Force Science (De-Escalation)', 'url' => route('service.by.slug', 'forced-science-de-escalation'), 'match' => ['type' => 'slug', 'slug' => 'forced-science-de-escalation']],
    ];
    $path = request()->path();
    $isDallasLawPage = request()->routeIs('dallas-law')
        || (request()->routeIs('service.by.slug') && (string) request()->route('slug') === 'dallas-law');
    $isAsp4HrPage = request()->routeIs('service.by.slug') && (string) request()->route('slug') === 'asp-4-hr';
    $isAffiliatedServicesPage = request()->routeIs('affiliated-services')
        || $path === 'affiliated-services'
        || str_ends_with($path, '/affiliated-services');
    $isNraServicesPage = request()->routeIs('nra-services')
        || $path === 'nra-services'
        || str_ends_with($path, '/nra-services');
    $isAffiliatedPage = $isAffiliatedServicesPage || $isNraServicesPage;
    $activeNavSection = match (true) {
        $isAffiliatedPage => 'affiliated',
        (request()->routeIs(['services', 'service.details', 'service.by.slug', 'handgun.subcategories'])
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
        'training' => (request()->routeIs(['services', 'service.details', 'service.by.slug', 'handgun.subcategories'])
            || str_starts_with($path, 'training-services')) && ! $isDallasLawPage && ! $isAsp4HrPage,
        'affiliated' => $isAffiliatedPage,
        'security' => request()->routeIs(['security-training', 'intial-security', 'renewals']) || $isDallasLawPage || $isAsp4HrPage,
        'testimonials' => request()->routeIs('testimonials'),
        'contact' => request()->routeIs('contact'),
        'login' => request()->routeIs('customer.login'),
        'register' => request()->routeIs('customer.register'),
        'dashboard' => (request()->routeIs('customer.*')
            && ! request()->routeIs(['customer.login', 'customer.register']))
            || (request()->routeIs('admin.*') && ! request()->routeIs('admin.login')),
        'services_all' => request()->routeIs('services') && ! request()->filled('category') && ! request()->filled('subcategory'),
    ];
    $servicesAffiliates = [
        ['name' => 'APEX Security Group', 'url' => 'https://apexsgi.com/home', 'external' => true], 
        ['name' => 'Code Blue CPR Services', 'url' => 'https://codebluecprservices.com/', 'external' => true],
        ['name' => 'Elite Security Service', 'url' => 'https://www.elitesecuritytn.org/', 'external' => true],
        ['name' => 'Guns & Leather', 'url' => 'https://www.gunsandleather.com/', 'external' => true],
        ['name' => 'JS Security Consulting', 'url' => 'https://www.jssecurityconsulting.com/', 'external' => true],
        ['name' => 'SafetyTN Security Solutions', 'url' => 'https://www.safetytennessee.com/', 'external' => true],
        ['name' => 'Shooter\'s Nashville', 'url' => 'https://www.shootersnashville.com/', 'external' => true],
        ['name' => 'US Law Shield', 'url' => 'https://members.uslawshield.com/login', 'external' => true],
        ['name' => 'Vanguard Security Training LLC', 'url' => 'https://vanguardsecuritytrainingllc.com/index.php', 'external' => true],
    ];
    $nraAffiliates = [
        ['name' => 'Join NRA', 'url' => 'https://membership.nra.org/recruiters/Join/XI048340', 'external' => true],
        ['name' => 'TNPTI', 'url' => 'https://www.tnpti.com/', 'external' => true],
        ['name' => 'SouthwindS Cattle Company', 'url' => 'https://www.southwindscattleco.com/', 'external' => true],
        ['name' => 'Raven 1 Tactical', 'url' => 'https://raven1tactical.com/', 'external' => true],
        ['name' => 'Blue Line Security', 'url' => 'https://www.nashvillebluelinesecurity.com/services', 'external' => true],
        ['name' => 'Tactical Rifles and Ammo', 'url' => 'https://tacticalriflesandammollc.com/', 'external' => true], 
    ];
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
    <div class="container mx-auto px-4 lg:px-10">
        <div class="flex items-center justify-between h-20 lg:h-24">
            
            <!-- Logo Area (Exact Match to Screenshot) -->
            <div class="relative flex-shrink-0 z-[60]">
                <a href="{{ url('/') }}" class="absolute -top-4 left-0 lg:-top-4">
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
                <div class="w-24 md:w-32 lg:w-48"></div>
            </div>

            <!-- Desktop Navigation Links (Middle/Right) -->
            <nav class="desktop-nav hidden lg:flex items-center space-x-6 text-[15px] font-medium text-[var(--text-color)]">
                <a href="{{ url('/') }}" data-nav-section="home" class="destop-nav-link {{ $navActive['home'] ? 'nav-link-active' : '' }}">Home</a>
                <a href="{{ route('about') }}" data-nav-section="about" class="destop-nav-link {{ $navActive['about'] ? 'nav-link-active' : '' }}">About Us</a>
                
                <!-- Training Services with Mega Menu -->
                <div class="relative nav-group h-full flex items-center">
                    <a href="{{ route('services') }}" data-nav-section="training" class="destop-nav-link flex items-center gap-1 py-8 {{ $navActive['training'] ? 'nav-link-active' : '' }}">
                        Training & Classes
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    
                    <div class="dropdown-simple">
                        <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden py-2">
                            @foreach($trainingCategories as $cat)
                                @php
                                    $catItemActive = false;
                                    if (($cat['match']['type'] ?? '') === 'services') {
                                        $catItemActive = request()->routeIs('services') && request('category') === ($cat['match']['category'] ?? null);
                                    } elseif (($cat['match']['type'] ?? '') === 'slug') {
                                        $catItemActive = request()->routeIs('service.by.slug') && (string) request()->route('slug') === (string) ($cat['match']['slug'] ?? '');
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
                    <a href="{{ route('affiliated-services') }}" data-nav-section="affiliated" class="js-affiliated-nav destop-nav-link flex items-center gap-1 py-8 {{ $navActive['affiliated'] ? 'nav-link-active' : '' }}">
                        Affiliated
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <div class="dropdown-simple">
                        <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-visible py-2">
                            @foreach($servicesAffiliates as $aff)
                                <a href="{{ $aff['url'] }}" data-nav-section="affiliated" class="category-item js-affiliated-trigger" @if(!empty($aff['external'])) target="_blank" rel="noopener noreferrer" @endif>
                                    {{ $aff['name'] }}
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
                                            <a href="{{ $aff['url'] }}" data-nav-section="affiliated" class="category-item js-affiliated-trigger" @if(!empty($aff['external'])) target="_blank" rel="noopener noreferrer" @endif>
                                                {{ $aff['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <a href="https://www.regimentsecuritygroup.com" data-nav-section="affiliated" class="category-item js-affiliated-trigger" target="_blank" rel="noopener noreferrer">
                                Regiment Security Group
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Security Training with dropdown (Initial Security, Renewals) -->
                <div class="relative nav-group h-full flex items-center">
                    <a href="{{ route('security-training') }}" data-nav-section="security" class="destop-nav-link flex items-center gap-1 py-8 cursor-default {{ $navActive['security'] ? 'nav-link-active' : '' }}">
                        Security Training

                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <div class="dropdown-simple">
                        <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-hidden py-2">
                            <a href="{{ route('intial-security') }}" class="category-item {{ request()->routeIs('intial-security') ? 'category-item-active' : '' }}">Initial Security</a>
                            <a href="{{ route('renewals') }}" class="category-item {{ request()->routeIs('renewals') ? 'category-item-active' : '' }}">Renewals</a>
                            <a href="{{ route('service.by.slug', 'asp-4-hr') }}" class="category-item asp-4-modal-trigger {{ $isAsp4HrPage ? 'category-item-active' : '' }}">ASP 4 Hours (Less than Lethal)</a>
                            <a href="{{ route('dallas-law') }}" class="category-item dallas-law-trigger {{ $isDallasLawPage ? 'category-item-active' : '' }}">Dallas Law</a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('testimonials') }}" data-nav-section="testimonials" class="destop-nav-link {{ $navActive['testimonials'] ? 'nav-link-active' : '' }}">Testimonials</a>
                <a href="{{ route('contact') }}" data-nav-section="contact" class="destop-nav-link {{ $navActive['contact'] ? 'nav-link-active' : '' }}">Contact Us</a>
            </nav>

            <!-- Desktop Button (Far Right) -->
            <div class="hidden lg:flex items-center gap-4">
                @if(Auth::guard('web')->check())
                    <a href="{{ route('admin.dashboard') }}" class="destop-nav-link {{ $navActive['dashboard'] ? 'nav-link-active' : '' }}">Dashboard</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="destop-nav-link">Logout</button>
                    </form>
                @elseif(Auth::guard('customer')->check())
                    <a href="{{ route('customer.dashboard') }}" class="destop-nav-link {{ $navActive['dashboard'] ? 'nav-link-active' : '' }}">Dashboard</a>
                    <form method="POST" action="{{ route('customer.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="destop-nav-link">Logout</button>
                    </form>
                @else
                    <a href="{{ route('customer.login') }}" class="destop-nav-link {{ $navActive['login'] ? 'nav-link-active' : '' }}">Login</a>
                    <a href="{{ route('customer.register') }}" class="btn primary-button {{ $navActive['register'] ? 'ring-2 ring-offset-2 ring-[var(--primary-color)]' : '' }}">
                        Sign Up
                    </a>
                @endif
            </div>

            <!-- Hamburger Button (<1024px) -->
            <button id="menuBtn" class="lg:hidden text-3xl text-gray-800 focus:outline-none p-2">
                <span id="menuIcon">☰</span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Vertical List -->
    <div id="mobileMenu" class="hidden lg:hidden   overflow-hidden transition-all duration-300">
        <nav class="flex flex-col p-6 space-y-1 mt-[60px]">
            <a href="{{ url('/') }}" data-nav-section="home" class="mobile-nav-links {{ $navActive['home'] ? 'nav-link-active' : '' }}">Home</a>
            <a href="{{ route('about') }}" data-nav-section="about" class="mobile-nav-links {{ $navActive['about'] ? 'nav-link-active' : '' }}">About Us</a>
            
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
                                    $mCatItemActive = request()->routeIs('services') && request('category') === ($cat['match']['category'] ?? null);
                                } elseif (($cat['match']['type'] ?? '') === 'slug') {
                                    $mCatItemActive = request()->routeIs('service.by.slug') && (string) request()->route('slug') === (string) ($cat['match']['slug'] ?? '');
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
                            <a href="{{ $aff['url'] }}" data-nav-section="affiliated" class="mobile-nav-links js-affiliated-trigger text-[16px]! py-3 px-4 hover:bg-white rounded-lg block border-l-4 border-transparent hover:border-(--primary-color)" @if(!empty($aff['external'])) target="_blank" rel="noopener noreferrer" @endif>
                                {{ $aff['name'] }}
                            </a>
                        @endforeach
                        <div class="border-t border-gray-200 pt-3 mt-1 col-span-1">
                            <a href="{{ route('nra-services') }}" data-nav-section="affiliated" class="mobile-nav-links js-affiliated-trigger text-[16px]! py-2 px-4 font-semibold text-gray-800 block border-l-4 {{ $isNraServicesPage ? 'border-(--primary-color) bg-emerald-50 text-(--primary-color)' : 'border-transparent' }}">NRA</a>
                            @foreach($nraAffiliates as $aff)
                                <a href="{{ $aff['url'] }}" data-nav-section="affiliated" class="mobile-nav-links js-affiliated-trigger text-[16px]! py-2.5 pl-6 pr-4 hover:bg-white rounded-lg block border-l-4 border-transparent hover:border-(--primary-color) text-gray-700" @if(!empty($aff['external'])) target="_blank" rel="noopener noreferrer" @endif>
                                    {{ $aff['name'] }}
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
                        <a href="{{ route('intial-security') }}" class="mobile-nav-links text-[16px]! py-3 px-4 hover:bg-white rounded-lg block border-l-4 {{ request()->routeIs('intial-security') ? 'border-(--primary-color) bg-emerald-50 font-semibold text-(--primary-color)' : 'border-transparent hover:border-(--primary-color)' }}">Initial Security</a>
                        <a href="{{ route('renewals') }}" class="mobile-nav-links text-[16px]! py-3 px-4 hover:bg-white rounded-lg block border-l-4 {{ request()->routeIs('renewals') ? 'border-(--primary-color) bg-emerald-50 font-semibold text-(--primary-color)' : 'border-transparent hover:border-(--primary-color)' }}">Renewals</a>
                        <a href="{{ route('service.by.slug', 'asp-4-hr') }}" class="mobile-nav-links asp-4-modal-trigger text-[16px]! py-3 px-4 hover:bg-white rounded-lg block border-l-4 {{ $isAsp4HrPage ? 'border-(--primary-color) bg-emerald-50 font-semibold text-(--primary-color)' : 'border-transparent hover:border-(--primary-color)' }}">ASP 4 Hours (Less than Lethal)</a>
                        <a href="{{ route('dallas-law') }}" class="mobile-nav-links dallas-law-trigger text-[16px]! py-3 px-4 hover:bg-white rounded-lg block border-l-4 {{ $isDallasLawPage ? 'border-(--primary-color) bg-emerald-50 font-semibold text-(--primary-color)' : 'border-transparent hover:border-(--primary-color)' }}">Dallas Law</a>
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
            @elseif(Auth::guard('customer')->check())
                <a href="{{ route('customer.dashboard') }}" class="mobile-nav-links {{ $navActive['dashboard'] ? 'nav-link-active' : '' }}">Dashboard</a>
                <form method="POST" action="{{ route('customer.logout') }}" class="inline w-full">
                    @csrf
                    <button type="submit" class="mobile-nav-links w-full text-left">Logout</button>
                </form>
            @else
                <a href="{{ route('customer.login') }}" class="mobile-nav-links {{ $navActive['login'] ? 'nav-link-active' : '' }}">Login</a>
                <a href="{{ route('customer.register') }}" class="mobile-nav-links {{ $navActive['register'] ? 'nav-link-active' : '' }}">Sign Up</a>
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



