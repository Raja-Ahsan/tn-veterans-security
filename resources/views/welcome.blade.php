@extends('layouts.web.master')

@section('content')
   <main class="overflow-hidden">
    <!-- Hero Section -->
       <section class="hero-section">
            <div class="hero-overlay"></div>
            
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[750px] mt-30 ">
                    <!-- Heading -->
                    <h1 class="text-[30px] md:text-[60px]  font-bold leading-none tracking-tight mb-8" data-aos="fade-down" data-aos-duration="1000">
                        <span class="text-[#F6CB42]">OUR</span> <span class="text-[var(--text-white)]">CERTIFICATIONS</span>
                    </h1>

                    <!-- Certifications List -->
                    <div class="cert-list" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>Armed Security Guard #650102</p>
                        </div>
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>ASP Instructor #65058</p>
                        </div>
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>Red Cross Instructor</p>
                        </div>
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>Force Science Instructor</p>
                        </div>
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>NRA Instructor #266631928</p>
                        </div>
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>State of Tennessee Certified Handgun Instructor #7004689023509</p>
                        </div>
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>Private Protective Services State Certified Trainer #932608</p>
                        </div>
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>ALERRT Active Shooter Instructor</p>
                        </div>
                        <div class="cert-item">
                            <span class="cert-bullet"></span>
                            <p>Tn Veterans Security Services and Training LLC formerly known as the Security Firm LLC</p>
                        </div>
                    </div>

                    <!-- Subtext -->
                    <p class="text-white text-[16px] lg:text-[20px] font-normal mt-5 mb-8" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                        We offer expert training for armed and unarmed security officers.
                    </p>

                    <!-- CTA Button -->
                    <div class="mb-10 md:mb-16 flex flex-col sm:flex-row gap-4" data-aos="fade-up" data-aos-delay="600" data-aos-duration="1000">
                        <a href="{{ route('class-calendar') }}" class="btn secondary-button !text-center">
                            View Class Calendar
                        </a>
                        <a href="{{ route('security-training') }}" class="btn primary-button !text-center">
                            Get Security Training Today
                        </a>
                    </div>
                </div>
            </div>
       </section>

       <!-- Veteran Owned & Instructor Bios Section -->
       <section class="py-16 lg:py-24 bg-gradient-to-b from-white to-gray-50">
           <div class="container mx-auto px-4 lg:px-10">
               <!-- Veteran Owned Badge -->
               <div class="text-center mb-12" data-aos="fade-up">
                   <div class="inline-flex items-center gap-3 bg-[var(--primary-color)]/10 px-8 py-4 rounded-full border-2 border-[var(--primary-color)]">
                       <i class="fas fa-flag text-[var(--primary-color)] text-2xl"></i>
                       <div class="text-left">
                           <p class="text-[20px] md:text-[24px] font-bold text-[var(--text-color)]">
                               Veteran Owned and Operated
                           </p>
                           <p class="text-[14px] text-gray-600">Established March 2024</p>
                       </div>
                   </div>
               </div>

               <!-- Instructor Bios -->
               <style>
                   .instructor-bio-scroll {
                       max-height: 15rem;
                       overflow-y: auto;
                       padding-right: 0.5rem;
                       scrollbar-width: thin;
                       scrollbar-color: var(--primary-color) #e5e7eb;
                   }
                   @media (min-width: 768px) {
                       .instructor-bio-scroll {
                           max-height: 17rem;
                       }
                   }
                   .instructor-bio-scroll::-webkit-scrollbar {
                       width: 6px;
                   }
                   .instructor-bio-scroll::-webkit-scrollbar-track {
                       background: #f3f4f6;
                       border-radius: 9999px;
                   }
                   .instructor-bio-scroll::-webkit-scrollbar-thumb {
                       background: var(--primary-color);
                       border-radius: 9999px;
                   }
               </style>
               <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 max-w-5xl mx-auto mb-12 items-start">
                   <!-- Jayson Bio -->
                   <div class="bg-white rounded-lg shadow-lg p-8 flex flex-col" data-aos="fade-right">
                       <div class="flex items-center gap-4 mb-4 shrink-0">
                           <div class="w-20 h-20 bg-[var(--primary-color)] rounded-full flex items-center justify-center shrink-0" style="width: 80px; height: 80px;">
                               <span class="text-white text-2xl font-bold">J</span>
                           </div>
                           <div>
                               <h3 class="text-[24px] font-bold text-[var(--text-color)] uppercase">Jayson</h3>
                               <p class="text-gray-600">Lead Instructor</p>
                           </div>
                       </div>
                       <div class="instructor-bio-scroll text-gray-700 leading-relaxed text-[15px]" tabindex="0" aria-label="Jayson bio — scroll for more">
                           @if($siteSettings && $siteSettings->jayson_bio)
                               {!! nl2br(e($siteSettings->jayson_bio)) !!}
                           @else
                               <p class="text-gray-500 italic">Bio coming soon. Contact information for certificates, gear, and job questions available through our contact form.</p>
                           @endif
                       </div>
                   </div>

                   <!-- Kenny Bio -->
                   <div class="bg-white rounded-lg shadow-lg p-8 flex flex-col" data-aos="fade-left">
                       <div class="flex items-center gap-4 mb-4 shrink-0">
                           <div class="w-20 h-20 bg-[var(--primary-color)] rounded-full flex items-center justify-center shrink-0" style="width: 80px; height: 80px;">
                               <span class="text-white text-2xl font-bold">K</span>
                           </div>
                           <div>
                               <h3 class="text-[24px] font-bold text-[var(--text-color)] uppercase">Kenny</h3>
                               <p class="text-gray-600">Lead Instructor</p>
                           </div>
                       </div>
                       <div class="instructor-bio-scroll text-gray-700 leading-relaxed text-[15px]" tabindex="0" aria-label="Kenny bio — scroll for more">
                           @if($siteSettings && $siteSettings->kenny_bio)
                               {!! nl2br(e($siteSettings->kenny_bio)) !!}
                           @else
                               <p class="text-gray-500 italic">Bio coming soon. Contact information for certificates, gear, and job questions available through our contact form.</p>
                           @endif
                       </div>
                   </div>
               </div>

               <!-- Instructor Contact Note -->
               <div class="bg-blue-50 border-l-4 border-[var(--primary-color)] p-6 rounded max-w-4xl mx-auto" data-aos="fade-up">
                   <div class="flex items-start gap-4">
                       <i class="fas fa-info-circle text-[var(--primary-color)] text-2xl mt-1"></i>
                       <div>
                           <h4 class="text-[18px] font-bold text-[var(--text-color)] mb-2">Instructor Contact</h4>
                           <p class="text-gray-700 leading-relaxed">
                               Our instructors can be contacted for certificates, gear, and job questions. Feel free to reach out through our contact form or by phone.
                           </p>
                       </div>
                   </div>
               </div>

               <!-- Security Services Note -->
               <div class="mt-8 text-center" data-aos="fade-up" data-aos-delay="200">
                   <p class="text-[16px] text-gray-700 mb-4">
                       Companies or individuals needing security services can contact us for more information.
                   </p>
                   <a href="{{ route('affiliated-services') }}" class="btn primary-button inline-block">
                       View Security Services
                   </a>
               </div>
           </div>
       </section>

       <!-- About Section -->
       <section class="about-section">
           <div class="container mx-auto px-4 lg:px-10">
               
              
               <div class="md:hidden mb-8 text-center" data-aos="fade-up">
                   <h2 class="training-heading">
                       <span class="text-[var(--primary-color)]">ABOUT</span> <span class="text-[var(--text-color)]">US</span>
                   </h2>
               </div>

               <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-0">
                   
                   <!-- Left Side: Image with Mission Text -->
                   <div class="lg:w-[48%] relative group" data-aos="fade-right">
                       <div class="relative z-10">
                           <img src="{{ asset('images/about-left-img.png') }}" alt="Security Professional" class="w-full h-auto">
                       </div>
                       
                       <div class="lg:absolute lg:top-0 lg:-right-15 p-6 lg:max-w-[300px] z-20 mt-6 lg:mt-0" data-aos="fade-left" data-aos-delay="200">
                           <p class="text-[16px] md:text-[20px] font-bold text-[var(--text-color)] leading-snug">
                             Our mission is to provide professional, efficient, cost-effective security guard training solutions for our customers and partners through continuous improvement driven by integrity, teamwork, and innovation.
                           </p>
                       </div>
                   </div>

                   <!-- Green Decorative Line (Separate Div) -->
                   <div class="hidden lg:block w-[3px] bg-[var(--primary-color)] self-stretch lg:mx-12"></div>

                   <!-- Right Side: Content -->
                   <div class="lg:w-[48%] lg:pl-4" data-aos="fade-left">
                       <!-- Heading for MD & Desktop (Hidden on Mobile) -->
                       <h2 class="about-title hidden md:block" data-aos="fade-up">
                           <span class="text-[var(--primary-color)]">ABOUT</span> <span class="text-[var(--text-color)]">US</span>
                       </h2>
                       
                       <p class="about-text" data-aos="fade-up" data-aos-delay="200">
                          Tennessee’s leading veteran-led institution for five-star-rated security guard registration training. We specialize in preparing students for both the Armed and Unarmed Security Guard Licenses in Nashville, Tennessee. Our graduates receive expert training that exceeds state requirements. If you’re considering a career as an armed or unarmed security guard in Tennessee, our comprehensive security registration training is the ideal starting point. Our classes are designed to equip individuals with all the necessary skills for a successful career in security.
                       </p>
                       
                       <div class="" data-aos="fade-up" data-aos-delay="400">
                           <p class="about-text">
                              We offer Blended classes online with in-person testing for Dallas Law Active Shooter Classification Certification programs, providing unmatched convenience and flexibility. At Tn Veterans Security Services and Training, we understand the importance of training security guards in Tennessee and take pride in preparing our students for real-world situations. Our graduates are well-regarded for providing reliable security, maintaining strong customer relations, and enhancing overall productivity for businesses across the Volunteer State.
                           </p>
                       </div>

                       <div class="mt-10" data-aos="fade-up" data-aos-delay="600">
                           <a href="{{ route('about') }}" class="btn primary-button inline-block text-center whitespace-nowrap">
                               Learn More About Us
                           </a>
                       </div>
                   </div>

               </div>
           </div>
       </section>   

       <!-- Professional Security Training & Career Support -->
    <section class="">
        <div class="container mx-auto px-4 lg:px-10">

            <!-- Heading -->
            <h2 class="training-heading"
                data-aos="fade-up"
                data-aos-delay="100">
                Professional Security 
                <span class="text-[var(--primary-color)]">Training</span> & Career Support
            </h2>

            <!-- Veteran Owned Badge -->
            <div class="text-center mb-12" data-aos="fade-up" data-aos-delay="150">
                <div class="inline-flex items-center gap-3 bg-[var(--primary-color)]/10 px-6 py-3 rounded-full">
                    <i class="fas fa-flag text-[var(--primary-color)] text-xl"></i>
                    <span class="text-[18px] font-bold text-[var(--text-color)]">
                        Veteran Owned and Operated • Established March 2024
                    </span>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mb-12" data-aos="fade-up" data-aos-delay="150">
                <button class="tab-btn active" onclick="filterServices('all', this)">All</button>
                <button class="tab-btn" onclick="filterServices('category:nra', this)">NRA</button>
                <button class="tab-btn" onclick="filterServices('category:red_cross', this)">Red Cross</button>
              
                <button class="tab-btn" onclick="filterServices('category:homeland_security', this)">Handgun Carry Permit</button>
              
             
                <button class="tab-btn" onclick="filterServices('category:renewals', this)">Renewals</button>
            </div>

            <!-- Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 min-h-[400px]" id="services-grid">
                @php
                    $allServices = $allServices ?? $servicesByCategory->flatten()->unique('id');
                @endphp
                
                @foreach($allServices as $service)
                    <div class="service-item {{ $loop->index >= 9 ? 'hidden' : '' }}" 
                         data-category="{{ implode(',', $service->categories ?? []) }}" 
                         data-subcategory="{{ $service->subcategory }}"
                         data-aos="fade-up" 
                         data-aos-delay="{{ ($loop->index % 6) * 100 }}">
                        @if($service->title === 'Unarmed  Security')
                        <div onclick="document.getElementById('unarmed-modal').classList.remove('hidden')" 
                             class="training-card block cursor-pointer hover:opacity-90 transition-opacity group h-full">
                        @else
                        <a href="{{ $service->title === 'Enhanced Armed Guard Security' ? route('handgun.subcategories') : route('service.details', $service->id) }}" 
                           class="training-card block cursor-pointer hover:opacity-90 transition-opacity group h-full">
                        @endif
                            <div class="training-card-img-div">
                                @if($service->image)
                                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="training-card-img">
                                @else
                                    <img src="{{ asset('images/training-img-' . (($loop->index % 6) + 1) . '.png') }}" alt="{{ $service->title }}" class="training-card-img">
                                @endif
                            </div>
                            <div class="p-4">
                                <h4 class="training-card-title">{{ $service->title }}</h4>
                                @if($service->location)
                                    <p class="text-sm text-gray-600 mt-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $service->location }}
                                    </p>
                                @endif
                                @if($service->requires_dallas_law || $service->requires_active_shooter)
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @if($service->requires_dallas_law)
                                            <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">Dallas Law Required</span>
                                        @endif
                                        @if($service->requires_active_shooter)
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Active Shooter Required</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @if($service->title === 'Unarmed  Security')
                        </div>
                        @else
                        </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Unarmed Security Modal -->
            <div id="unarmed-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-8 bg-black/50" onclick="if(event.target===this) document.getElementById('unarmed-modal').classList.add('hidden')">
                <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3rem)] overflow-y-auto m-4 sm:m-6 relative" onclick="event.stopPropagation()">
                    <button type="button" onclick="document.getElementById('unarmed-modal').classList.add('hidden')" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                    <div class="p-6 lg:p-8 pt-12">
                        <h3 class="text-xl font-bold text-[var(--text-color)] mb-4 uppercase" style="font-family: var(--font-display);">Choose Your Path</h3>
                        <div class="space-y-4">
                            <a href="{{ route('services', ['category' => 'dallas_law']) }}" class="block p-4 rounded-lg border-2 border-gray-200 hover:border-[var(--primary-color)] hover:bg-gray-50 transition-all text-left group">
                                <p class="text-[var(--text-color)] font-medium group-hover:text-[var(--primary-color)]">If you are working where Alcohol is distributed you must have Dallas Law.</p>
                            </a>
                            <a href="{{ route('services', ['category' => 'asp_less_than_lethal']) }}" class="block p-4 rounded-lg border-2 border-gray-200 hover:border-[var(--primary-color)] hover:bg-gray-50 transition-all text-left group">
                                <p class="text-[var(--text-color)] font-medium group-hover:text-[var(--primary-color)]">If you want to carry anything such as OC Spray, Baton, Restraints, or Taser you must have Less Than Lethal Training.</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All Services Button -->
            <div class="mt-12 text-center">
                <a href="{{ route('all-services') }}" id="view-all-services-btn" class="btn primary-button" style="display: {{ count($allServices) > 9 ? 'inline-block' : 'none' }};">
                    View Training and Classes
                </a>
            </div>

          


            <!-- Featured Services Grid (for Explore Training Programs) -->
            @if($featuredServices && $featuredServices->count() > 0)
                <!-- <div class="mt-16" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="text-[24px] md:text-[32px] font-bold text-[var(--text-color)] mb-8 text-center uppercase" style="font-family: var(--font-display);">
                        Explore Training Programs
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
                        @foreach($featuredServices as $index => $service)
                            <a href="{{ route('service.details', $service->id) }}" class="training-card block cursor-pointer hover:opacity-90 transition-opacity" data-aos="zoom-in" data-aos-delay="{{ ($index + 1) * 100 }}">
                                <div class="training-card-img-div">
                                    @if($service->image)
                                        <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="training-card-img">
                                    @else
                                        <img src="{{ asset('images/training-img-' . (($index % 6) + 1) . '.png') }}" alt="{{ $service->title }}" class="training-card-img">
                                    @endif
                                </div>
                                <h3 class="training-card-title">{{ $service->title }}</h3>
                            </a>
                        @endforeach
                    </div>
                </div> -->
            @endif

            <!-- CTA Button -->
            <!-- <div class="mt-16 text-center"
                 data-aos="fade-up"
                 data-aos-delay="400">
                <a href="{{ route('services') }}" class="btn primary-button inline-block !text-[16px]">
                    View All Training Programs
                </a>
            </div> -->

        </div>
    </section>

       <!-- Training You Can Trust -->
<section class="py-16 lg:py-24 overflow-hidden">
    <div class="container mx-auto px-4 lg:px-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            
            <!-- Left Content -->
            <div class="max-w-[650px]"
                 data-aos="fade-right"
                 data-aos-delay="100">

                <h2 class="training-heading !text-start"
                    data-aos="fade-up"
                    data-aos-delay="150">
                    <span class="text-[var(--primary-color)]">TRAINING</span>
                    <span class="text-[var(--text-color)] uppercase">YOU CAN TRUST</span>
                </h2>

                <!-- List with Checkmarks -->
                <div class="space-y-4 mb-10 text-[16px] md:text-[18px]">

                    <div class="trust-list" data-aos="fade-up" data-aos-delay="200">
                        <div class="mt-1.5 w-5 h-5 rounded-full bg-[var(--primary-color)] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="trust-text">Veteran-driven leadership and instruction</p>
                    </div>

                    <div class="trust-list" data-aos="fade-up" data-aos-delay="250">
                        <div class="mt-1.5 w-5 h-5 rounded-full bg-[var(--primary-color)] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="trust-text">Professional, structured training programs</p>
                    </div>

                    <div class="trust-list" data-aos="fade-up" data-aos-delay="300">
                        <div class="mt-1.5 w-5 h-5 rounded-full bg-[var(--primary-color)] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="trust-text">Support for new career paths and certification renewals</p>
                    </div>

                    <div class="trust-list" data-aos="fade-up" data-aos-delay="350">
                        <div class="mt-1.5 w-5 h-5 rounded-full bg-[var(--primary-color)] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="trust-text">Strong connections with trusted security companies</p>
                    </div>

                    <div class="trust-list" data-aos="fade-up" data-aos-delay="400">
                        <div class="mt-1.5 w-5 h-5 rounded-full bg-[var(--primary-color)] flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="trust-text">Dedicated, approachable, and supportive learning environment</p>
                    </div>

                </div>

                <p class="trust-text text-[16px] md:text-[20px] leading-relaxed mb-10"
                   data-aos="fade-up"
                   data-aos-delay="450">
                    We are committed to helping individuals succeed in the security industry through quality training, clear guidance, and long-term career support.
                </p>

                <div data-aos="fade-up" data-aos-delay="500">
                    <a href="{{ route('security-training') }}" class="btn primary-button inline-block !text-[16px]">
                        Get Security Training Today
                    </a>
                </div>

            </div>

            <!-- Right Image -->
            <div class="relative"
                 data-aos="fade-left"
                 data-aos-delay="200">
                <img src="{{ asset('images/training-trust-right-img.png') }}"
                     alt="Security Professionals"
                     class="w-full h-auto">
            </div>

        </div>
    </div>
</section>


       <!-- jason-section -->
<section class="jason-section"
         data-aos="fade-up"
         data-aos-delay="100">
    <div class="container mx-auto px-4 lg:px-10 relative z-10">
     
        <div class="flex justify-end">
            <div class="max-w-full lg:max-w-[45%] text-right lg:text-left pt-12 lg:pt-0"
                 data-aos="fade-left"
                 data-aos-delay="200">

                <!-- Testimonial Icon -->
                <div class="mb-6 flex justify-end lg:justify-start"
                     data-aos="zoom-in"
                     data-aos-delay="300">
                    <img src="{{ asset('images/jason-icon.png') }}"
                         alt="Quote Icon"
                         class="w-16 md:w-24">
                </div>

                <!-- Testimonial Text -->
                <p class="text-[20px] md:text-[32px] font-bold text-[#000000] leading-tight mb-8 max-w-[500px] md:text-[var(--text-color)]"
                   data-aos="fade-up"
                   data-aos-delay="350">
                    The training was professional, clear, and well-organized. I felt prepared and confident after completing my certification.
                </p>

                <!-- Reviewer Name -->
                <h3 class="text-[30px] md:text-[64px] font-bold text-[var(--primary-color)] uppercase leading-none mb-1"
                    data-aos="fade-up"
                    data-aos-delay="400">
                    JASON
                </h3>

                <!-- Reviewer Title -->
                <p class="text-[16px] text-[var(--text-color)] font-normal"
                   data-aos="fade-up"
                   data-aos-delay="450">
                    Certified Security Officer
                </p>

            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-16 lg:py-24"
         data-aos="fade-up"
         data-aos-delay="100">
    <div class="container mx-auto px-4 lg:px-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-stretch">
            
            <!-- Left Side: Image & Info Box -->
            <div class="relative w-full h-[400px] md:h-[500px] lg:h-auto min-h-[450px]"
                 data-aos="fade-right"
                 data-aos-delay="200">

                <div class="absolute inset-0 overflow-hidden">
                    <img src="{{ asset('images/contact-form-left-img.png') }}"
                         alt="Security Guard"
                         class="w-full h-full object-cover">
                </div>
                
                <!-- Floating Contact Box -->
                <div class="absolute bottom-6 left-6 right-6 bg-white p-6 md:p-4 z-10"
                     data-aos="fade-up"
                     data-aos-delay="350">
                    <h4 class="contact-title"
                        data-aos="fade-up"
                        data-aos-delay="380">
                        Contact
                    </h4>

                    <div class="flex flex-col md:flex-row gap-6 md:items-center">

                        <!-- Phone -->
                        <div class="flex items-center gap-3 group"
                             data-aos="fade-up"
                             data-aos-delay="420">
                            <div>
                                <img src="{{ asset('images/conatct-phone-icon.png') }}"
                                     alt="Phone"
                                     class="w-10 h-10">
                            </div>
                            <a href="tel:{{ $siteSettings && $siteSettings->phone ? str_replace([' ', '-', '(', ')'], '', $siteSettings->phone) : '6155541131' }}"
                               class="text-[15px] md:text-[17px] font-bold">
                                {{ $siteSettings && $siteSettings->phone ? $siteSettings->phone : '615-554-1131' }}
                            </a>
                        </div>

                        <!-- Email -->
                        <div class="flex items-center gap-3 group"
                             data-aos="fade-up"
                             data-aos-delay="460">
                            <div>
                                <img src="{{ asset('images/conatct-mail-icon.png') }}"
                                     alt="Mail"
                                     class="w-10 h-10">
                            </div>
                            <a href="mailto:{{ $siteSettings && $siteSettings->email ? $siteSettings->email : 'tnvetsecsvctrng@gmail.com' }}"
                               class="text-[15px] md:text-[17px] font-bold">
                                {{ $siteSettings && $siteSettings->email ? $siteSettings->email : 'tnvetsecsvctrng@gmail.com' }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="w-full border-t lg:border-t-0 pt-12 lg:pt-0"
                 data-aos="fade-left"
                 data-aos-delay="200">

                <h2 class="text-[30px] md:text-[40px] font-bold leading-[1.2] mb-4 uppercase text-[var(--text-color)]"
                    data-aos="fade-up"
                    data-aos-delay="250">
                    GET IN <span class="text-[var(--primary-color)]">TOUCH</span> WITH OUR TEAM
                </h2>

                <p class="text-[16px] md:text-[20px] text-[var(--text-color)] mb-10 max-w-[550px]"
                   data-aos="fade-up"
                   data-aos-delay="300">
                    Have questions about our training programs, certification requirements, or enrollment process?
                </p>

                <form action="#" method="POST" class="space-y-6"
                      data-aos="fade-up"
                      data-aos-delay="350">
                    @csrf

                    <!-- Full Name -->
                    <div data-aos="fade-up" data-aos-delay="380">
                        <input type="text" name="name" placeholder="Full Name" required 
                               class="contact-input">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <input type="email" name="email" placeholder="Email Address" required 
                               class="contact-input"
                               data-aos="fade-up"
                               data-aos-delay="420">

                        <!-- Phone -->
                        <input type="tel" name="phone" placeholder="Phone Number" 
                               class="contact-input"
                               data-aos="fade-up"
                               data-aos-delay="450">
                    </div>

                    <!-- Training Interest -->
                    <div class="relative"
                         data-aos="fade-up"
                         data-aos-delay="480">
                        <select name="interest" 
                                class="contact-input cursor-pointer">
                            <option value="" disabled selected>Training Interest</option>
                            <option value="armed">Armed Security Guard</option>
                            <option value="unarmed">Unarmed Security Guard</option>
                            <option value="redcross">Red Cross Training</option>
                            <option value="firearms">Firearms Training</option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div data-aos="fade-up" data-aos-delay="510">
                        <textarea name="message" rows="5" placeholder="Message" 
                                  class="contact-input resize-none"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div data-aos="fade-up" data-aos-delay="540">
                        <button type="submit"
                                class="w-full bg-[var(--primary-color)] text-white font-bold py-4 rounded-sm hover:bg-[var(--btn-hover-color)] transition-colors tracking-widest text-[14px]">
                            Submit Request
                        </button>

                        <p class="text-[16px] text-[var(--text-color)] font-normal mt-4 text-center">
                            Your information is kept confidential and used only to respond to your inquiry.
                        </p>
                    </div>

                </form>
            </div>

        </div>
    </div>
</section>

      <!-- ready-to-take -->
<section class="ready-section"
         data-aos="fade-up"
         data-aos-delay="100">
    <div class="ready-overlay"
         data-aos="fade-in"
         data-aos-delay="150"></div>

    <div class="container mx-auto px-4 lg:px-10 relative z-10">
        <div class="text-left md:text-center lg:text-left md:mx-auto lg:mx-0"
             data-aos="fade-right"
             data-aos-delay="200">

            <!-- Heading -->
            <h2 class="mb-5 md:mb-5"
                data-aos="fade-up"
                data-aos-delay="250">
                <span class="block text-[18px] md:text-[24px] text-white font-normal"
                      data-aos="fade-up"
                      data-aos-delay="280">
                    Ready to take the
                </span>
                <span class="block text-[30px] md:text-[40px] font-black leading-tight uppercase"
                      data-aos="fade-up"
                      data-aos-delay="320">
                    <span class="text-[#F6CB42]">NEXT STEP</span>
                    <span class="text-[#FFFFFF]">IN</span><br>
                    <span class="text-[#FFFFFF]">SECURITY?</span>
                </span>
            </h2>

            <!-- Subtext -->
            <p class="text-[16px] md:text-[20px] text-white font-normal mb-5 md:mx-auto lg:mx-0"
               data-aos="fade-up"
               data-aos-delay="360">
                Get trained, get certified, and move forward with confidence.
            </p>

            <!-- Buttons Group -->
            <div class="flex flex-col sm:flex-row gap-4 justify-start md:justify-center lg:justify-start"
                 data-aos="fade-up"
                 data-aos-delay="420">
                <a href="{{ route('security-training') }}" class="btn primary-button !text-center">
                    Start Your Training
                </a>
                <a href="{{ route('contact') }}" class="btn secondary-button !text-center">
                    Contact Us
                </a>
            </div>

        </div>
    </div>
</section>


       <!-- faqs-section -->
<section class="faqs-section relative"
         data-aos="fade-up"
         data-aos-delay="100">
    <div class="container mx-auto px-4 lg:px-10 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 items-start">
            
            <!-- Left Content: Heading & FAQs -->
            <div class="w-full text-left md:text-center lg:text-left"
                 data-aos="fade-right"
                 data-aos-delay="150">

                <h2 class="training-heading !text-start"
                    data-aos="fade-up"
                    data-aos-delay="200">
                    <span class="text-[var(--primary-color)]">FREQUENTLY</span> ASKED <br> QUESTIONS
                </h2>

                <div class="faq-accordion space-y-4 md:max-w-[600px] md:mx-auto lg:mx-0">

                    <!-- FAQ Item 1 (Active by default) -->
                    <div class="faq-item active"
                         data-aos="fade-up"
                         data-aos-delay="250">
                        <button class="faq-question">
                            Do I need prior experience to enroll?
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                No. Our programs are designed for beginners as well as individuals renewing or upgrading their certifications.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 - Conditional: Dallas Law -->
                    <div class="faq-item"
                         data-aos="fade-up"
                         data-aos-delay="300">
                        <button class="faq-question">
                            Do you plan to work around alcohol?
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                <p class="mb-3">If you plan to work around alcohol, you <strong class="text-[var(--primary-color)]">must</strong> take Dallas Law training.</p>
                                <p>Dallas Law training is required for security officers who will be working in establishments that serve or sell alcohol. This specialized training covers the legal requirements and protocols for security work in alcohol-related environments.</p>
                                <p class="mt-3">
                                    <a href="{{ route('services', ['category' => 'security_training', 'subcategory' => 'dallas_law']) }}" class="text-[var(--primary-color)] font-semibold hover:underline">
                                        View Dallas Law Training Courses →
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 - Conditional: Active Shooter -->
                    <div class="faq-item"
                         data-aos="fade-up"
                         data-aos-delay="350">
                        <button class="faq-question">
                            Will you work at a school, church, or daycare?
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                <p class="mb-3">If you will work at a school, church, or daycare, you <strong class="text-[var(--primary-color)]">must</strong> take Active Shooter Training.</p>
                                <p>Active Shooter Training is mandatory for security officers working in educational institutions, places of worship, or childcare facilities. This critical training prepares you to respond effectively in emergency situations.</p>
                                <p class="mt-3">
                                    <a href="{{ route('services', ['category' => 'security_training', 'subcategory' => 'active_shooter']) }}" class="text-[var(--primary-color)] font-semibold hover:underline">
                                        View Active Shooter Training Courses →
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="faq-item"
                         data-aos="fade-up"
                         data-aos-delay="400">
                        <button class="faq-question">
                            Do you offer both armed and unarmed security training?
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                Yes, we provide comprehensive training for both armed and unarmed security roles, following all state requirements. This includes our 4-hour Unarmed Guard Training program.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="faq-item"
                         data-aos="fade-up"
                         data-aos-delay="450">
                        <button class="faq-question">
                            Is your training state-compliant?
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                Absolutely. All our training programs are fully certified and compliant with the State of Tennessee regulations.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 6 -->
                    <div class="faq-item"
                         data-aos="fade-up"
                         data-aos-delay="500">
                        <button class="faq-question">
                            Do you help with job placement after training?
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                Yes, we assist our graduates in securing employment by connecting them with our network of trusted security partners.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 7 -->
                    <div class="faq-item"
                         data-aos="fade-up"
                         data-aos-delay="550">
                        <button class="faq-question">
                            How long does the training take?
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                Training duration varies by program. Unarmed Guard Training is 4 hours, while other certifications can be completed in just a few days of intensive instruction.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 8 -->
                    <div class="faq-item"
                         data-aos="fade-up"
                         data-aos-delay="600">
                        <button class="faq-question">
                            How do I get started?
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                Simply fill out the contact form above or give us a call to enroll in our next available training session.
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Empty Right Div for BG visibility on Desktop -->
            <div class="hidden lg:block"
                 data-aos="fade-left"
                 data-aos-delay="200"></div>

        </div>
    </div>
</section>


       <script>
           document.addEventListener('DOMContentLoaded', () => {
               const faqItems = document.querySelectorAll('.faq-item');

               faqItems.forEach(item => {
                   const question = item.querySelector('.faq-question');
                   
                   question.addEventListener('click', () => {
                       const isActive = item.classList.contains('active');
                       
                       // Close all items
                       faqItems.forEach(i => i.classList.remove('active'));
                       
                       // If the clicked item wasn't active, open it
                       if (!isActive) {
                           item.classList.add('active');
                       }
                   });
               });
           });
       </script>
   </main>
@endsection


  <style>
                .tab-btn {
                    padding: 8px 20px;
                    border: 1px solid var(--primary-color);
                    border-radius: 4px;
                    color: var(--text-color);
                    font-weight: 600;
                    text-transform: uppercase;
                    font-size: 14px;
                    transition: all 0.3s ease;
                    background: transparent;
                }
                .tab-btn.active, .tab-btn:hover {
                    background: var(--primary-color);
                    color: white;
                }
                .service-item.hidden {
                    display: none;
                }
            </style>

            
            <script>
                function filterServices(filter, btn) {
                    if (btn) {
                        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                    }

                    const grid = document.getElementById('services-grid');
                    const items = grid.querySelectorAll('.service-item');
                    const viewAllBtn = document.getElementById('view-all-services-btn');

                    if (filter === 'all') {
                        items.forEach((item, index) => {
                            if (index < 9) {
                                item.classList.remove('hidden');
                                item.setAttribute('data-aos', 'fade-up');
                            } else {
                                item.classList.add('hidden');
                            }
                        });
                        
                        if (viewAllBtn) {
                            viewAllBtn.style.display = 'inline-block';
                        }
                    } else {
                        if (viewAllBtn) viewAllBtn.style.display = 'none';
                        const [type, value] = filter.split(':');
                        items.forEach(item => {
                            const itemValue = item.getAttribute(`data-${type}`);
                            const cats = (itemValue || '').split(',').map(s => s.trim()).filter(Boolean);
                            if (cats.length && cats.some(c => c.toLowerCase() === value.toLowerCase())) {
                                item.classList.remove('hidden');
                                item.setAttribute('data-aos', 'zoom-in');
                            } else {
                                item.classList.add('hidden');
                            }
                        });
                    }

                    if (typeof AOS !== 'undefined') {
                        AOS.refresh();
                    }
                }
            </script>