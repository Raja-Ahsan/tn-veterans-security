@extends('layouts.web.master')

@section('content')
    <main class="overflow-hidden">
        
        <!-- Inner Hero Section -->
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px]">
                    <h2 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        ABOUT <span class="text-(--primary-color)">US</span>
                    </h2>
                    <p class="inner-hero-subtext" data-aos="fade-up" data-aos-delay="200">
                        Dedication, Professionalism, and Veteran-Friendly Security Training.
                    </p>
                </div>
            </div>
        </section>

        <!-- Company Overview Section -->
        <section class="about-overview-section">
            <div class="container mx-auto px-4 lg:px-10">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                    
                    <!-- Left Side: Image -->
                    <div class="lg:w-1/2" data-aos="fade-right">
                        <div class="relative">
                            <img src="{{ asset('images/about-left-img.png') }}" alt="About Our Service" class="w-full h-auto shadow-2xl rounded-sm">
                            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-(--primary-color) -z-10 hidden md:block"></div>
                        </div>
                    </div>

                    <!-- Right Side: Content -->
                    <div class="lg:w-1/2" data-aos="fade-left">
                        <h2 class="about-overview-title">
                            Providing <span class="text-(--primary-color)">High-Quality</span> Security Training
                        </h2>
                        <p class="about-overview-text">
                            At our core, we are committed to offering high-quality security training to individuals interested in pursuing a rewarding career in security. We understand the demands of the industry and the importance of having well-trained professionals protecting our communities.
                        </p>
                        <p class="about-overview-text">
                            Whether you're looking to start a new career as an armed or unarmed security officer, or you're a seasoned professional looking renewal your certifications, our expert instructors are here to guide you every step of the way.
                        </p>

                        <div class="mt-8 mb-8 pb-8 border-b border-gray-100">
                             <h4 class="text-[20px] md:text-[24px] font-bold text-(--primary-color) uppercase mb-3">At Tn Veterans Security and Training</h4>
                             <p class="about-overview-text mb-0!">
                                Veteran and privately owned and operated, is dedicated to making a difference in the Tennessee security industry, one student at a time.
                             </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mission-vision-card border-(--primary-color)" data-aos="fade-up" data-aos-delay="100">
                                <h3>OUR VISION</h3>
                                <p>We aim to be Tennessee’s most preferred and trusted security guard training and compliance resource provider.</p>
                            </div>
                            <div class="mission-vision-card border-[#F6CB42]" data-aos="fade-up" data-aos-delay="200">
                                <h3>OUR FOCUS</h3>
                                <p>We focus on providing dependable, uniform, and proficient security guard training services.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Training You Can Trust (Reused Style) -->
        <section class="about-trust-section overflow-hidden">
            <div class="container mx-auto px-4 lg:px-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    
                    <!-- Left Content -->
                    <div class="max-w-[650px]" data-aos="fade-right">
                        <h2 class="training-heading text-start!">
                            <span class="text-(--primary-color)">TRAINING</span> <span class="text-(--text-color) uppercase">YOU CAN TRUST</span>
                        </h2>

                        <div class="space-y-6 mb-10">
                            <div class="trust-list" data-aos="fade-up" data-aos-delay="100">
                                <div class="mt-1.5 w-6 h-6 rounded-full bg-(--primary-color) flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="trust-text !text-[18px] md:!text-[22px]">Veteran-driven leadership and instruction</p>
                            </div>
                            <div class="trust-list" data-aos="fade-up" data-aos-delay="200">
                                <div class="mt-1.5 w-6 h-6 rounded-full bg-(--primary-color) flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="trust-text !text-[18px] md:!text-[22px]">Professional, structured training programs</p>
                            </div>
                            <div class="trust-list" data-aos="fade-up" data-aos-delay="300">
                                <div class="mt-1.5 w-6 h-6 rounded-full bg-(--primary-color) flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="trust-text !text-[18px] md:!text-[22px]">Support for new career paths and renewals</p>
                            </div>
                        </div>

                        <p class="text-[18px] md:text-[22px] text-[#333] font-medium leading-relaxed mb-10">
                            Our team is dedicated to your success. We don't just provide training; we provide a clear path to a stable and rewarding career in security.
                        </p>
                    </div>

                    <!-- Right Image -->
                    <div class="relative" data-aos="fade-left">
                        <img src="{{ asset('images/training-trust-right-img.png') }}" alt="Security Professionals" class="w-full h-auto">
                    </div>
                </div>
            </div>
        </section>

        <!-- Classes & Travel Section -->
        <section class="py-16 lg:py-24 bg-white overflow-hidden border-t border-gray-100">
            <div class="container mx-auto px-4 lg:px-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20">
                    
                    <!-- Left: Classes -->
                    <div data-aos="fade-right">
                        <h2 class="about-overview-title">
                            ARMED & UNARMED <span class="text-(--primary-color) uppercase">In-Person Classes</span>
                        </h2>
                        
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 w-10 h-10 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-(--primary-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-[20px] font-bold text-(--text-color)">Shooter’s</h4>
                                    <p class="text-[16px] md:text-[18px] text-[#555]">575 Murfresburo Pike Nashville, Tn</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="mt-1 w-10 h-10 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-(--primary-color)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-[20px] font-bold text-(--text-color)">Gun’s and Leather</h4>
                                    <p class="text-[16px] md:text-[18px] text-[#555]">HWY 41, Greenbrier Tn 37073</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Travel -->
                    <div data-aos="fade-left">
                        <h2 class="about-overview-title">
                            TRAVEL TO <span class="text-(--primary-color) uppercase">YOUR SITE</span> IN TENNESSEE
                        </h2>
                        
                        <div class="bg-[#F8F8F8] p-8 border-l-4 border-[#F6CB42] relative">
                            <p class="text-[16px] md:text-[20px] text-[#444] leading-relaxed mb-6">
                                Travel to client site anywhere in Tennessee: <span class="font-bold text-(--text-color)">$450</span> for up to 10 onsite training participants on scheduled availability dates for NRA, Red Cross, Unarmed security guard group classes.
                            </p>
                            <div class="flex items-center gap-3 py-4 px-6 bg-white border border-gray-100 shadow-sm rounded-sm">
                                <div class="w-2 h-2 rounded-full bg-(--primary-color)"></div>
                                <p class="text-[15px] md:text-[17px] text-[#666] font-medium uppercase">
                                    This includes approved 4-hour general training for an unarmed guard card.
                                </p>
                            </div>
                            <p class="mt-6 text-[18px] font-bold text-(--text-color)">
                                Additional students are <span class="text-(--primary-color) text-[24px]">$40 each</span>.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Ready to take selection -->
        <section class="ready-section">
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="text-left md:text-center lg:text-left md:mx-auto lg:mx-0">
                    <h2 class="mb-5" data-aos="fade-up">
                        <span class="block text-[18px] md:text-[24px] text-white font-normal">Ready to join our</span>
                        <span class="block text-[30px] md:text-[45px] font-black leading-tight uppercase">
                            <span class="text-[#F6CB42]">NEXT TRAINING</span> <span class="text-[#FFFFFF]">SESSION?</span>
                        </span>
                    </h2>
                    <p class="text-[16px] md:text-[20px] text-white font-normal mb-8 md:mx-auto lg:mx-0" data-aos="fade-up" data-aos-delay="200">
                        Enroll today and start your journey towards becoming a certified security professional.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-start md:justify-center lg:justify-start" data-aos="fade-up" data-aos-delay="400">
                        <a href="#" class="btn primary-button !text-center">Start Your Training</a>
                        <a href="#" class="btn secondary-button !text-center">Contact Us</a>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection
