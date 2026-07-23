@extends('layouts.web.master')

@section('content')
    <main class="overflow-hidden">
        
        <!-- Inner Hero Section -->
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px] py-8">
                    <h2 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        TRAINING <span class="text-[var(--primary-color)]">SERVICES</span>
                    </h2>
                    <p class="inner-hero-subtext" data-aos="fade-up" data-aos-delay="200">
                        Professional Security Training, Certified Instruction, and Career Development.
                    </p>
                </div>
            </div>
        </section>

        <!-- Services Detailed Section -->
        <section class="py-16 lg:py-24 bg-gradient-to-b from-white to-[#F8F8F8]">
            <div class="container mx-auto px-4 lg:px-10">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                    <a href="{{ $services->first() ? route('training-classes.show', $services->first()->id) : '#' }}" class="group block">
                        <div class="service-detail-card bg-white rounded-lg overflow-hidden h-full flex flex-col transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                            <div class="relative h-[280px] overflow-hidden">
                                <img src="{{ asset('images/training-img-1.png') }}" 
                                     alt="Rifle" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="absolute top-4 right-4 bg-[var(--primary-color)] text-white px-4 py-2 rounded-full text-sm font-bold opacity-0 group-hover:opacity-100 transform translate-y-[-10px] group-hover:translate-y-0 transition-all duration-300">
                                    Learn More
                                </div>
                            </div>
                            <div class="p-6 lg:p-8 flex-1 flex flex-col">
                                <h3 class="text-[22px] lg:text-[26px] font-bold text-[var(--text-color)] mb-4 uppercase group-hover:text-[var(--primary-color)] transition-colors" style="font-family: var(--font-display);">
                                    Rifle
                                </h3>
                                @if($services->first() && $services->first()->short_description)
                                    <p class="text-[#666] text-[15px] lg:text-[16px] leading-relaxed mb-4 flex-1 line-clamp-3">
                                        {{ $services->first()->short_description }}
                                    </p>
                                @else
                                    <p class="text-[#666] text-[15px] lg:text-[16px] leading-relaxed mb-4 flex-1 line-clamp-3">
                                        Rifle training and certification program.
                                    </p>
                                @endif
                                <div class="flex items-center gap-2 text-[var(--primary-color)] font-semibold mt-auto pt-4 border-t border-gray-100 group-hover:border-[var(--primary-color)]/30 transition-colors">
                                    <span class="text-[15px] uppercase tracking-wide">View Details</span>
                                    <i class="fas fa-arrow-right transform group-hover:translate-x-2 transition-transform"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="{{ $services->last() ? route('training-classes.show', $services->last()->id) : '#' }}" class="group block">
                        <div class="service-detail-card bg-white rounded-lg overflow-hidden h-full flex flex-col transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                            <div class="relative h-[280px] overflow-hidden">
                                <img src="{{ asset('images/training-img-2.png') }}" 
                                     alt="Shotgun" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <div class="absolute top-4 right-4 bg-[var(--primary-color)] text-white px-4 py-2 rounded-full text-sm font-bold opacity-0 group-hover:opacity-100 transform translate-y-[-10px] group-hover:translate-y-0 transition-all duration-300">
                                    Learn More
                                </div>
                            </div>
                            <div class="p-6 lg:p-8 flex-1 flex flex-col">
                                <h3 class="text-[22px] lg:text-[26px] font-bold text-[var(--text-color)] mb-4 uppercase group-hover:text-[var(--primary-color)] transition-colors" style="font-family: var(--font-display);">
                                    Shotgun
                                </h3>
                                @if($services->last() && $services->last()->short_description)
                                    <p class="text-[#666] text-[15px] lg:text-[16px] leading-relaxed mb-4 flex-1 line-clamp-3">
                                        {{ $services->last()->short_description }}
                                    </p>
                                @else
                                    <p class="text-[#666] text-[15px] lg:text-[16px] leading-relaxed mb-4 flex-1 line-clamp-3">
                                        Shotgun training and certification program.
                                    </p>
                                @endif
                                <div class="flex items-center gap-2 text-[var(--primary-color)] font-semibold mt-auto pt-4 border-t border-gray-100 group-hover:border-[var(--primary-color)]/30 transition-colors">
                                    <span class="text-[15px] uppercase tracking-wide">View Details</span>
                                    <i class="fas fa-arrow-right transform group-hover:translate-x-2 transition-transform"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </section>

        <!-- Specialized Certification Section -->
        <section class="py-16 lg:py-24 bg-[#111] text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="text-center mb-16">
                    <h2 class="text-[32px] md:text-[52px] font-bold uppercase mb-6 leading-tight" style="font-family: var(--font-display);" data-aos="fade-up">
                        Get <span class="text-[var(--primary-color)]">Certified</span> Professionally
                    </h2>
                    <p class="text-[18px] md:text-[22px] max-w-[800px] mx-auto text-gray-400 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                        Our certifications are recognized throughout the industry and are fully state-compliant.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    <div data-aos="fade-right">
                        <div class="space-y-10">
                            <div class="flex gap-6 items-start group">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-[var(--primary-color)] rounded-lg flex items-center justify-center text-white text-[32px] font-bold shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">01</div>
                                </div>
                                <div>
                                    <h4 class="text-[24px] md:text-[28px] font-bold mb-3 uppercase" style="font-family: var(--font-display);">Veteran Friendly</h4>
                                    <p class="text-gray-400 text-[16px] md:text-[18px] leading-relaxed">We prioritize veterans and provide an environment that respects and utilizes your previous experience.</p>
                                </div>
                            </div>
                            <div class="flex gap-6 items-start group">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-[var(--primary-color)] rounded-lg flex items-center justify-center text-white text-[32px] font-bold shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">02</div>
                                </div>
                                <div>
                                    <h4 class="text-[24px] md:text-[28px] font-bold mb-3 uppercase" style="font-family: var(--font-display);">Job Placement</h4>
                                    <p class="text-gray-400 text-[16px] md:text-[18px] leading-relaxed">We help our graduates connect with security companies for immediate employment opportunities.</p>
                                </div>
                            </div>
                            <div class="flex gap-6 items-start group">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-[var(--primary-color)] rounded-lg flex items-center justify-center text-white text-[32px] font-bold shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">03</div>
                                </div>
                                <div>
                                    <h4 class="text-[24px] md:text-[28px] font-bold mb-3 uppercase" style="font-family: var(--font-display);">Ongoing Support</h4>
                                    <p class="text-gray-400 text-[16px] md:text-[18px] leading-relaxed">From certification renewals to advanced training, we support your career throughout your professional life.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative" data-aos="fade-left">
                        <div class="relative rounded-lg overflow-hidden shadow-2xl">
                            <img src="{{ asset('images/contact-form-left-img.png') }}" alt="Certification Training" class="w-full h-auto object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </div>
                        <div class="absolute -top-6 -left-6 w-32 h-32 border-4 border-[var(--primary-color)] rounded-full opacity-30 animate-pulse"></div>
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 border-4 border-[var(--primary-color)] rounded-full opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="container mx-auto px-4 lg:px-10">
                <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
                    <h2 class="text-[32px] md:text-[42px] font-bold text-[var(--text-color)] mb-6 uppercase" style="font-family: var(--font-display);">
                        Ready to <span class="text-[var(--primary-color)]">Start Your Journey?</span>
                    </h2>
                    <p class="text-[18px] md:text-[20px] text-[#666] mb-10 leading-relaxed max-w-2xl mx-auto">
                        Contact us today to learn more about our training programs and how we can help you achieve your career goals.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('contact') }}" class="btn primary-button inline-block">Get In Touch</a>
                        @if(isset($siteSettings) && $siteSettings && $siteSettings->phone)
                            <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', $siteSettings->phone) }}" class="btn secondary-button inline-block">
                                <i class="fas fa-phone mr-2"></i> Call Us Now
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection
