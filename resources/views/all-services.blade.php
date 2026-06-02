@extends('layouts.web.master')

@section('content')
   <main class="overflow-hidden">
        <!-- Inner Hero Section -->
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px] py-8">
                    <h2 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        ALL <span class="text-(--primary-color)">TRAINING </span> AND <span class="text-(--primary-color)">CLASSES</span>
                    </h2>
                    <p class="inner-hero-subtext" data-aos="fade-up" data-aos-delay="200">
                        Explore our complete range of professional security training and classes.
                    </p>
                </div>
            </div>
        </section>

        <!-- Professional Security Training Section -->
        <section class="py-16 lg:py-24">
            <div class="container mx-auto px-4 lg:px-10">

                <!-- Heading -->
                <h2 class="training-heading"
                    data-aos="fade-up"
                    data-aos-delay="100">
                    Professional Security 
                    <span class="text-(--primary-color)">Training</span> & Career Support
                </h2>

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
                    @foreach($allServices as $service)
                        <div class="service-item" 
                             data-category="{{ implode(',', $service->categories ?? []) }}" 
                             data-subcategory="{{ $service->subcategory }}"
                             data-aos="fade-up" 
                             data-aos-delay="{{ ($loop->index % 6) * 100 }}">
                            <a href="{{ route('service.details', $service->id) }}" 
                               class="training-card block cursor-pointer hover:opacity-90 transition-opacity group h-full">
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
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 lg:py-20 bg-gray-50">
            <div class="container mx-auto px-4 lg:px-10">
                <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
                    <h2 class="text-[32px] md:text-[42px] font-bold text-(--text-color) mb-6 uppercase">
                        Ready to <span class="text-(--primary-color)">Get Started?</span>
                    </h2>
                    <p class="text-[18px] md:text-[20px] text-[#666] mb-10 leading-relaxed max-w-2xl mx-auto">
                        Enroll in our training programs today and take the first step towards your career in security.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('contact') }}" class="btn primary-button inline-block">
                            Contact Us Now
                        </a>
                    </div>
                </div>
            </div>
        </section>
   </main>

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
            display: none !important;
        }
    </style>

    <script>
        function filterServices(filter, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const grid = document.getElementById('services-grid');
            const items = grid.querySelectorAll('.service-item');

            if (filter === 'all') {
                items.forEach(item => {
                    item.classList.remove('hidden');
                    item.setAttribute('data-aos', 'fade-up');
                });
            } else {
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
@endsection
