@extends('layouts.web.master')

@section('title', 'Affiliated Services - Partners & Organizations')

@section('content')
    <main class="overflow-hidden">
        
        <!-- Inner Hero Section (same as home/inner pages) -->
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px]">
                    <h2 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        <span class="text-[var(--primary-color)]">Affiliated</span> Services
                    </h2>
                    <p class="inner-hero-subtext" data-aos="fade-up" data-aos-delay="200">
                        Our trusted partners and affiliated organizations.
                    </p>
                </div>
            </div>
        </section>

        <!-- Affiliates / Partners Section -->
         
        <section class="py-16 lg:py-24 bg-white">
            <div class="container mx-auto px-4 lg:px-10">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-[30px] md:text-[45px] font-bold text-[var(--text-color)] uppercase mb-4">
                        Partners & <span class="text-[var(--primary-color)]">Organizations</span>
                    </h2>
                    <p class="text-[16px] md:text-[20px] text-[#666] max-w-2xl mx-auto">
                        We work with industry-leading organizations. Use the links below to learn more (opens in a new tab).
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @php
                        $affiliates = [
                            ['initials' => 'ASG', 'name' => 'APEX Security Group', 'url' => 'https://apexsgi.com/home', 'blurb' => 'Security services and professional protection.'], 
                            ['initials' => 'CBP', 'name' => 'Code Blue CPR Services', 'url' => 'https://codebluecprservices.com/', 'blurb' => 'CPR and first aid training and certification.'],
                            ['initials' => 'ESS', 'name' => 'Elite Security Service', 'url' => 'https://www.elitesecuritytn.org/', 'blurb' => 'Tennessee-based security and protective services.'],
                            ['initials' => 'G+L', 'name' => 'Guns & Leather', 'url' => 'https://www.gunsandleather.com/', 'blurb' => 'Firearms, gear, and related retail services.'],
                            ['initials' => 'JSC', 'name' => 'JS Security Consulting', 'url' => 'https://www.jssecurityconsulting.com/', 'blurb' => 'Security consulting and professional guidance.'],
                            ['initials' => 'STN', 'name' => 'SafetyTN Security Solutions', 'url' => 'https://www.safetytennessee.com/', 'blurb' => 'Security solutions across Tennessee.'],
                            ['initials' => 'SN', 'name' => "Shooter's Nashville", 'url' => 'https://www.shootersnashville.com/', 'blurb' => 'Shooting sports and range-related services in Nashville.'],
                            ['initials' => 'USLS', 'name' => 'US Law Shield', 'url' => 'https://members.uslawshield.com/login', 'blurb' => 'Legal defense and education for responsible gun owners.'],
                            ['initials' => 'VST', 'name' => 'Vanguard Security Training LLC', 'url' => 'https://vanguardsecuritytrainingllc.com/index.php', 'blurb' => 'Security training and certification.'],
                        ];
                    @endphp
                    @foreach ($affiliates as $index => $affiliate)
                        @php
                            $delay = 100 + ($index * 50);
                            $initialLen = strlen($affiliate['initials']);
                            $initialClass = $initialLen <= 2 ? 'text-lg' : ($initialLen <= 3 ? 'text-sm' : 'text-xs');
                        @endphp
                        <a href="{{ $affiliate['url'] }}" target="_blank" rel="noopener noreferrer" class="group block bg-gray-50 hover:bg-[var(--primary-color)]/10 border border-gray-200 hover:border-[var(--primary-color)] rounded-xl p-6 transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                            <div class="flex items-center gap-4 mb-3">
                                <div class="w-12 h-12 rounded-lg bg-[var(--primary-color)] flex items-center justify-center text-white font-bold {{ $initialClass }} shrink-0 group-hover:scale-110 transition-transform">{{ $affiliate['initials'] }}</div>
                                <h3 class="text-xl font-bold text-[var(--text-color)] group-hover:text-[var(--primary-color)] transition-colors">{{ $affiliate['name'] }}</h3>
                            </div>
                            <p class="text-[#666] text-sm group-hover:text-gray-700">{{ $affiliate['blurb'] }}</p>
                            <span class="inline-flex items-center gap-1 text-[var(--primary-color)] font-semibold mt-3 text-sm">Visit site <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CTA Section (same style as home inner pages) -->
        <section class="ready-section">
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="text-left md:text-center lg:text-left md:mx-auto lg:mx-0">
                    <h2 class="mb-5" data-aos="fade-up">
                        <span class="block text-[18px] md:text-[24px] text-white font-normal">Questions about our</span>
                        <span class="block text-[30px] md:text-[45px] font-black leading-tight uppercase">
                            <span class="text-[#F6CB42]">PARTNERS</span> <span class="text-[#FFFFFF]">?</span>
                        </span>
                    </h2>
                    <p class="text-[16px] md:text-[20px] text-white font-normal mb-8 md:mx-auto lg:mx-0" data-aos="fade-up" data-aos-delay="200">
                        Contact us for more information about our affiliated services and training programs.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-start md:justify-center lg:justify-start" data-aos="fade-up" data-aos-delay="400">
                        <a href="{{ route('contact') }}" class="btn primary-button !text-center">Contact Us</a>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection
