@extends('layouts.web.master')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <main class="overflow-hidden">
        
        <!-- Inner Hero Section -->
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px] py-8">
                    <h2 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        {{-- add servuice name dynamically --}}
                        @if($category)
                            {{ ucfirst(str_replace('_', ' ', $category)) }}
                        @else
                        <span class="text-(--primary-color)">TRAINING</span> AND <span class="text-(--primary-color)">CLASSES</span>
                        @endif
                        {{-- <span class="text-[var(--primary-color)]">SERVICES</span>
                        TRAINING <span class="text-[var(--primary-color)]">SERVICES</span> --}}
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

                <div class="mb-10" data-aos="fade-up">
                    <div class="mx-auto max-w-2xl">
                        <label for="training-services-search" class="mb-2 block text-center text-sm font-semibold uppercase tracking-wide text-gray-500">
                            Search classes
                        </label>
                        <div class="relative">
                            <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="search"
                                id="training-services-search"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Search by class name…"
                                autocomplete="off"
                                class="w-full rounded-xl border border-gray-200 bg-white py-3.5 pl-11 pr-12 text-[15px] text-gray-800 shadow-sm outline-none transition focus:border-[var(--primary-color)] focus:ring-2 focus:ring-[var(--primary-color)]/20"
                            >
                            <button type="button"
                                    id="training-services-search-clear"
                                    class="absolute right-3 top-1/2 hidden -translate-y-1/2 rounded-full px-2 py-1 text-sm text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                                    aria-label="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p id="training-services-search-status" class="mt-2 text-center text-sm text-gray-500" aria-live="polite"></p>
                    </div>
                </div>

                <div id="training-services-results">
                    @include('training-classes.partials.cards', ['services' => $services])
                </div>

                <!-- Unarmed  Security Modal -->
                <div id="unarmed-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-8 bg-black/50" onclick="if(event.target===this) document.getElementById('unarmed-modal').classList.add('hidden')">
                    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3rem)] overflow-y-auto m-4 sm:m-6 relative" onclick="event.stopPropagation()">
                        <button type="button" onclick="document.getElementById('unarmed-modal').classList.add('hidden')" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                        <div class="p-6 lg:p-8 pt-12">
                            <h3 class="text-xl font-bold text-[var(--text-color)] mb-4 uppercase" style="font-family: var(--font-display);">Choose Your Path</h3>
                            <div class="space-y-4">
                                <a href="{{ route('training-classes', ['category' => 'dallas_law']) }}" class="block p-4 rounded-lg border-2 border-gray-200 hover:border-[var(--primary-color)] hover:bg-gray-50 transition-all text-left group">
                                    <p class="text-[var(--text-color)] font-medium group-hover:text-[var(--primary-color)]">If you are working where Alcohol is distributed you must have Dallas Law.</p>
                                </a>
                                <a href="{{ route('training-classes', ['category' => 'asp_less_than_lethal']) }}" class="block p-4 rounded-lg border-2 border-gray-200 hover:border-[var(--primary-color)] hover:bg-gray-50 transition-all text-left group">
                                    <p class="text-[var(--text-color)] font-medium group-hover:text-[var(--primary-color)]">If you want to carry anything such as OC Spray, Baton, Restraints, or Taser you must have Less Than Lethal Training.</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Specialized Certification Section -->
        <section class="py-16 lg:py-24 bg-[#111] text-white relative overflow-hidden">
            <!-- Background Pattern -->
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
                                    <div class="w-16 h-16 bg-[var(--primary-color)] rounded-lg flex items-center justify-center text-white text-[32px] font-bold shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">
                                        01
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-[24px] md:text-[28px] font-bold mb-3 uppercase" style="font-family: var(--font-display);">
                                        Veteran Friendly
                                    </h4>
                                    <p class="text-gray-400 text-[16px] md:text-[18px] leading-relaxed">
                                        We prioritize veterans and provide an environment that respects and utilizes your previous experience.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex gap-6 items-start group">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-[var(--primary-color)] rounded-lg flex items-center justify-center text-white text-[32px] font-bold shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">
                                        02
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-[24px] md:text-[28px] font-bold mb-3 uppercase" style="font-family: var(--font-display);">
                                        Job Placement
                                    </h4>
                                    <p class="text-gray-400 text-[16px] md:text-[18px] leading-relaxed">
                                        We help our graduates connect with security companies for immediate employment opportunities.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex gap-6 items-start group">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-[var(--primary-color)] rounded-lg flex items-center justify-center text-white text-[32px] font-bold shadow-lg shadow-green-500/20 group-hover:scale-110 transition-transform">
                                        03
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-[24px] md:text-[28px] font-bold mb-3 uppercase" style="font-family: var(--font-display);">
                                        Ongoing Support
                                    </h4>
                                    <p class="text-gray-400 text-[16px] md:text-[18px] leading-relaxed">
                                        From certification renewals to advanced training, we support your career throughout your professional life.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative" data-aos="fade-left">
                        <div class="relative rounded-lg overflow-hidden shadow-2xl">
                            <img src="{{ asset('images/contact-form-left-img.png') }}" 
                                 alt="Certification Training" 
                                 class="w-full h-auto object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </div>
                        <!-- Decorative Elements -->
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
                        <a href="{{ route('contact') }}" class="btn primary-button inline-block">
                            Get In Touch
                        </a>
                        @if($siteSettings && $siteSettings->phone)
                            <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', $siteSettings->phone) }}" 
                               class="btn secondary-button inline-block">
                                <i class="fas fa-phone mr-2"></i> Call Us Now
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection

@push('scripts')
<script>
(function () {
    var input = document.getElementById('training-services-search');
    var clearBtn = document.getElementById('training-services-search-clear');
    var results = document.getElementById('training-services-results');
    var status = document.getElementById('training-services-search-status');
    if (! input || ! results) return;

    var searchUrl = @json(route('training-classes.search'));
    var category = @json($category);
    var subcategory = @json($subcategory);
    var timer = null;
    var controller = null;

    function toggleClear() {
        if (! clearBtn) return;
        clearBtn.classList.toggle('hidden', input.value.trim() === '');
    }

    function setStatus(text) {
        if (status) status.textContent = text || '';
    }

    function runSearch() {
        var q = input.value.trim();
        toggleClear();

        if (controller) {
            controller.abort();
        }
        controller = new AbortController();

        setStatus(q ? 'Searching…' : '');

        var params = new URLSearchParams();
        if (q) params.set('q', q);
        if (category) params.set('category', category);
        if (subcategory) params.set('subcategory', subcategory);

        fetch(searchUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            signal: controller.signal
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                results.innerHTML = data.html || '';
                if (! q) {
                    setStatus('');
                } else if (data.count === 0) {
                    setStatus('No classes match “' + q + '”.');
                } else {
                    setStatus(data.count + (data.count === 1 ? ' class' : ' classes') + ' found');
                }
            })
            .catch(function (err) {
                if (err.name === 'AbortError') return;
                setStatus('Search failed. Please try again.');
            });
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(runSearch, 300);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            input.value = '';
            toggleClear();
            runSearch();
            input.focus();
        });
    }

    toggleClear();
})();
</script>
@endpush
