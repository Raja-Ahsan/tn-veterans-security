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

                <div>
                    @php
                        $sections = [
                            [
                                'title' => 'Veteran Owned Security Companies',
                                'companies' => [
                                    ['initials' => 'ES', 'name' => 'Elite Security', 'url' => 'https://www.elitesecuritytn.org'],
                                    ['initials' => 'VST', 'name' => 'Vanguard Security Training', 'url' => 'https://vanguardsecuritytrainingllc.com'],
                                    ['initials' => 'RSG', 'name' => 'Regiment Security Group', 'url' => 'https://www.regimentsecuritygroup.com'],
                                    ['initials' => 'ESS', 'name' => 'Essential Security Services', 'url' => 'https://www.essentialsecurityservices.com'],
                                ],
                            ],
                            [
                                'title' => 'Non Veteran Owned Security Companies',
                                'companies' => [
                                    ['initials' => 'STN', 'name' => 'SafetyTN Security Solutions', 'url' => 'https://safetytennessee.com'],
                                    ['initials' => 'JSC', 'name' => 'JS Security Consulting', 'url' => 'https://www.jssecurityconsulting.com'],
                                    ['initials' => 'APX', 'name' => 'Apex', 'url' => 'https://apexsgi.com'],
                                ],
                            ],
                            [
                                'title' => 'Veteran Owned Companies (Non Security)',
                                'companies' => [
                                    ['initials' => 'G+L', 'name' => 'Guns and Leather', 'url' => 'https://gunsandleather.com'],
                                    ['initials' => 'SGA', 'name' => "Shooter's Guns, Ammo & Range", 'url' => 'https://www.shootersnashville.com'],
                                    ['initials' => 'SWC', 'name' => 'South Winds Cattle Company', 'url' => 'https://www.southwindscattleco.com'],
                                ],
                            ],
                            [
                                'title' => 'Non Veteran Owned Companies (Non Security)',
                                'companies' => [
                                    ['initials' => 'CBP', 'name' => 'Code Blue CPR Services', 'url' => 'https://codebluecprservices.com'],
                                    ['initials' => 'USL', 'name' => 'USLAW Shield', 'url' => 'https://members.uslawshield.com/login'],
                                    ['initials' => 'TPT', 'name' => 'TN Professional Training Institute', 'url' => 'https://www.tnpti.com'],
                                ],
                            ],
                        ];
                    @endphp

                    @foreach ($sections as $sectionIndex => $section)
                        <div class="affiliate-section-box @unless($loop->last) mb-16 lg:mb-24 @endunless" data-aos="fade-up" data-aos-delay="{{ $sectionIndex * 100 }}">
                            <h3 class="text-[22px] md:text-[28px] font-bold text-[var(--text-color)] uppercase mb-8 lg:mb-10 border-l-4 border-[var(--primary-color)] pl-4">
                                {{ $section['title'] }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                                @foreach ($section['companies'] as $index => $company)
                                    @php
                                        $initialLen = strlen($company['initials']);
                                        $initialClass = $initialLen <= 2 ? 'text-lg' : ($initialLen <= 3 ? 'text-sm' : 'text-xs');
                                    @endphp
                                    <a href="{{ $company['url'] }}" target="_blank" rel="noopener noreferrer" class="group block bg-gray-50 hover:bg-[var(--primary-color)]/10 border border-gray-200 hover:border-[var(--primary-color)] rounded-xl p-6 transition-all duration-300">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-lg bg-[var(--primary-color)] flex items-center justify-center text-white font-bold {{ $initialClass }} shrink-0 group-hover:scale-110 transition-transform">{{ $company['initials'] }}</div>
                                            <div>
                                                <h4 class="text-xl font-bold text-[var(--text-color)] group-hover:text-[var(--primary-color)] transition-colors">{{ $company['name'] }}</h4>
                                                <span class="inline-flex items-center gap-1 text-[var(--primary-color)] font-semibold mt-2 text-sm">Visit site <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg></span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
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
