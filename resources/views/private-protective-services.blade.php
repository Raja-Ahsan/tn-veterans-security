@extends('layouts.web.master')

@section('content')
<main class="overflow-hidden">
    
    <!-- Inner Hero Section -->
    <section class="inner-hero">
        <div class="inner-hero-overlay"></div>
        <div class="container mx-auto px-4 lg:px-10 relative z-10">
            <div class="max-w-[1000px] py-8">
                <h2 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                    PRIVATE PROTECTIVE <span class="text-[var(--primary-color)]">SERVICES</span>
                </h2>
                <p class="inner-hero-subtext" data-aos="fade-up" data-aos-delay="200">
                    Professional Security Services for Companies and Individuals
                </p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 lg:py-24 bg-gradient-to-b from-white to-[#F8F8F8]">
        <div class="container mx-auto px-4 lg:px-10">
            
            <div class="max-w-4xl mx-auto mb-12" data-aos="fade-up">
                <h2 class="text-[32px] md:text-[42px] font-bold text-[var(--text-color)] mb-6 text-center uppercase" style="font-family: var(--font-display);">
                    Our <span class="text-[var(--primary-color)]">Services</span>
                </h2>
                <p class="text-[18px] text-gray-700 text-center leading-relaxed">
                    We provide comprehensive private protective services for businesses, organizations, and individuals. Our veteran-owned and operated team delivers professional security solutions tailored to your needs.
                </p>
            </div>

            @if($services->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 mb-16">
                    @foreach($services as $index => $service)
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden h-full flex flex-col transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                            @if($service->image)
                                <div class="relative h-[200px] overflow-hidden">
                                    <img src="{{ $service->image_url }}" 
                                         alt="{{ $service->title }}" 
                                         class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-[22px] font-bold text-[var(--text-color)] mb-4 uppercase" style="font-family: var(--font-display);">
                                    {{ $service->title }}
                                </h3>
                                @if($service->short_description)
                                    <p class="text-gray-600 mb-4 flex-1">
                                        {{ $service->short_description }}
                                    </p>
                                @endif
                                @if($service->description)
                                    <div class="text-gray-600 mb-4 flex-1">
                                        {!! Str::limit(strip_tags($service->description), 200) !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Contact CTA -->
            <div class="bg-[var(--primary-color)] rounded-lg p-8 text-center text-white" data-aos="fade-up">
                <h3 class="text-[28px] font-bold mb-4">Need Security Services?</h3>
                <p class="text-[18px] mb-6 opacity-90">
                    Contact us to discuss your security needs and get a customized solution.
                </p>
                <a href="{{ route('contact') }}" class="btn bg-white text-[var(--primary-color)] hover:bg-gray-100 inline-block">
                    Contact Us Today
                </a>
            </div>
        </div>
    </section>

    <!-- Security Company Links Section -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-4 lg:px-10">
            <div class="max-w-4xl mx-auto" data-aos="fade-up">
                <h2 class="text-[32px] md:text-[42px] font-bold text-[var(--text-color)] mb-6 text-center uppercase" style="font-family: var(--font-display);">
                    Partner <span class="text-[var(--primary-color)]">Companies</span>
                </h2>
                <p class="text-[18px] text-gray-700 text-center mb-12 leading-relaxed">
                    We work with trusted security companies in the region. Visit their websites to learn more about their services.
                </p>

                @if($companyLinks && $companyLinks->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($companyLinks as $company)
                            <a href="{{ $company->url }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="bg-gray-50 hover:bg-gray-100 rounded-lg p-6 text-center transition-colors border-2 border-transparent hover:border-[var(--primary-color)]">
                                @if($company->logo)
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" class="h-16 mx-auto mb-4 object-contain">
                                @endif
                                <h4 class="text-[20px] font-bold text-[var(--text-color)] mb-2">
                                    {{ $company->name }}
                                </h4>
                                @if($company->description)
                                    <p class="text-gray-600 text-sm">
                                        {{ $company->description }}
                                    </p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <p class="text-gray-600 mb-4">Company links will be added here.</p>
                        <p class="text-sm text-gray-500">Contact us for more information about our partner companies.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 lg:py-20 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-10">
            <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
                <h2 class="text-[32px] md:text-[42px] font-bold text-[var(--text-color)] mb-6 uppercase" style="font-family: var(--font-display);">
                    Ready to <span class="text-[var(--primary-color)]">Get Started?</span>
                </h2>
                <p class="text-[18px] md:text-[20px] text-gray-700 mb-10 leading-relaxed">
                    Whether you need security services for your business or are looking for employment opportunities, we're here to help.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('contact') }}" class="btn primary-button inline-block">
                        Get In Touch
                    </a>
                    <a href="{{ route('training-classes') }}" class="btn secondary-button inline-block">
                        View Training Programs
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
