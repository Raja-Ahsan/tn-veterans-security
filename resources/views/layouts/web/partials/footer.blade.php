<!-- Premium Footer Section -->
<footer class="bg-[#161616] text-white pt-16 pb-0">
    <div class="container mx-auto px-4 lg:px-10">
        <div class="footer-content">

            <!-- Quick Links -->
            <div>
                <h4 class="footer-title">
                    Quick Links
                </h4>
                <ul class="footer-ul">
                    <li><a href="{{ url('/') }}" class="footer-link">Home</a></li>
                    <li><a href="{{ route('about') }}" class="footer-link">About Us</a></li>
                    <li><a href="{{ route('services') }}" class="footer-link">Training & Classes</a></li>
                    <li><a href="{{ route('security-training') }}" class="footer-link">Security Training</a></li>
                    <li><a href="{{ route('testimonials') }}" class="footer-link">Testimonials</a></li>
                    <li><a href="{{ route('contact') }}" class="footer-link">Contact Us</a></li>
                    <li><a href="{{ route('legal.privacy-policy') }}" class="footer-link">Privacy Policy</a></li>
                    <li><a href="{{ route('legal.terms-and-conditions') }}" class="footer-link">Terms & Conditions</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div>
                <h4 class="footer-title">
                    Contact Information
                </h4>
                <div class="space-y-6">
                    <!-- Phone -->
                    @if ($siteSettings && $siteSettings->phone)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 flex items-center justify-center"
                                style="background: #f6cb42;">
                                <svg class="w-6 h-6" fill="#333333" viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', $siteSettings->phone) }}"
                                class="contact-link">
                                {{ $siteSettings->phone }}
                            </a>
                        </div>
                    @endif
                    <!-- Email -->
                    @if ($siteSettings && $siteSettings->email)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 flex items-center justify-center"
                                style="background: #f6cb42;">
                                <svg class="w-6 h-6" fill="#333333" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <a href="mailto:{{ $siteSettings->email }}" class="contact-link">
                                {{ $siteSettings->email }}
                            </a>
                        </div>
                    @endif
                    <!-- Address -->
                    @if ($siteSettings && $siteSettings->address)
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0"
                                style="background: #f6cb42;">
                                <svg class="w-full h-full p-2" fill="#333333" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="contact-link">{{ $siteSettings->address }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Center Logo -->
            <div class="flex flex-col items-center justify-center lg:-mt-10">
                @if ($siteSettings && $siteSettings->footer_logo)
                    <img src="{{ asset('storage/' . $siteSettings->footer_logo) }}" alt="TN Veterans Logo"
                        class="w-auto h-48 md:h-64 lg:h-72 drop-shadow-2xl mb-6">
                @else
                    <img src="{{ asset('images/securty-logo.png') }}" alt="TN Veterans Logo"
                        class="w-auto h-48 md:h-64 lg:h-72 drop-shadow-2xl mb-6">
                @endif
            </div>
            <!-- Training Programs -->
            <div>
                <h4 class="footer-title">
                    Training Programs
                </h4>
                @if ($footerServices->count() > 0)
                    <ul class="footer-ul">
                        @foreach ($footerServices as $service)
                            <li>
                                <a href="{{ route('service.details', $service->id) }}" class="footer-link">
                                    {{ $service->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <ul class="footer-ul">
                        <li><a href="{{ route('services') }}" class="footer-link">View All Services</a></li>
                    </ul>
                @endif
            </div>


        </div>
    </div>

    <!-- Social Media Links -->
    @if (
        $siteSettings &&
            ($siteSettings->facebook_url ||
                $siteSettings->twitter_url ||
                $siteSettings->instagram_url ||
                $siteSettings->linkedin_url ||
                $siteSettings->youtube_url))
        <div class="bg-[#1a1a1a] py-8">
            <div class="container mx-auto px-4 lg:px-10">
                <div class="flex justify-center gap-6">
                    @if ($siteSettings->facebook_url)
                        <a href="{{ $siteSettings->facebook_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-12 h-12 bg-gray-700 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                    @endif
                    @if ($siteSettings->twitter_url)
                        <a href="{{ $siteSettings->twitter_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-12 h-12 bg-gray-700 hover:bg-blue-400 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                    @endif
                    @if ($siteSettings->instagram_url)
                        <a href="{{ $siteSettings->instagram_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-12 h-12 bg-gray-700 hover:bg-pink-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                    @endif
                    @if ($siteSettings->linkedin_url)
                        <a href="{{ $siteSettings->linkedin_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-12 h-12 bg-gray-700 hover:bg-blue-700 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                    @endif
                    @if ($siteSettings->youtube_url)
                        <a href="{{ $siteSettings->youtube_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-12 h-12 bg-gray-700 hover:bg-red-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-youtube text-white"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Bottom Copyright Bar -->
    <div class="bg-[var(--primary-color)] py-6 mt-10">
        <div class="container mx-auto px-4 text-center">
            <p class="text-[#FFFFFF]  text-[18px] font-regular tracking-wide">
                &copy; {{ date('Y') }} Tn Veterans Security Services and Training. All Rights Reserved.
            </p>
        </div>
    </div>
</footer>

{{-- Dallas Law alcohol modal (same style as existing modals) --}}
<div id="dallas-law-modal"
    class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6 md:p-8 bg-black/50"
    onclick="if(event.target===this) document.getElementById('dallas-law-modal').classList.add('hidden')">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3rem)] overflow-y-auto m-4 sm:m-6 relative"
        onclick="event.stopPropagation()">
        <button type="button" onclick="document.getElementById('dallas-law-modal').classList.add('hidden')"
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
        <div class="p-6 lg:p-8 pt-12">
            <div id="dallas-law-step-question">
                <h3 class="text-xl font-bold text-[var(--text-color)] mb-4 uppercase"
                    style="font-family: var(--font-display);">Dallas Law</h3>
                <p class="text-[var(--text-color)] text-[16px] leading-relaxed mb-6">Will you be working around
                    alcohol?</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" id="dallas-law-modal-yes"
                        class="flex-1 py-3 px-4 rounded-lg font-semibold text-white transition-colors"
                        style="background: var(--primary-color);">Yes</button>
                    <button type="button" id="dallas-law-modal-no"
                        class="flex-1 py-3 px-4 rounded-lg font-semibold border-2 border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">No</button>
                </div>
            </div>
            <div id="dallas-law-step-no-message" class="hidden">
                <h3 class="text-xl font-bold text-[var(--text-color)] mb-4 uppercase"
                    style="font-family: var(--font-display);">Dallas Law</h3>
                <p class="text-[var(--text-color)] text-[16px] leading-relaxed mb-6">You do not have to take this
                    course.</p>
                <button type="button" id="dallas-law-modal-close"
                    class="py-3 px-6 rounded-lg font-semibold text-white transition-colors"
                    style="background: var(--primary-color);">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ASP 4 Hours (Less than Lethal) eligibility modal --}}
<div id="asp-4-modal"
    class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6 md:p-8 bg-black/50"
    onclick="if(event.target===this) document.getElementById('asp-4-modal').classList.add('hidden')">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-3rem)] overflow-y-auto m-4 sm:m-6 relative"
        onclick="event.stopPropagation()">
        <button type="button" onclick="document.getElementById('asp-4-modal').classList.add('hidden')"
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
        <div class="p-6 lg:p-8 pt-12">
            <div id="asp-4-step-question">
                <h3 class="text-xl font-bold text-[var(--text-color)] mb-4 uppercase"
                    style="font-family: var(--font-display);">ASP 4 Hours (Less than Lethal)</h3>
                <p class="text-[var(--text-color)] text-[16px] leading-relaxed mb-6">Are you working in the field of
                    Law Enforcement or Security?</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" id="asp-4-modal-yes"
                        class="flex-1 py-3 px-4 rounded-lg font-semibold text-white transition-colors"
                        style="background: var(--primary-color);">Yes</button>
                    <button type="button" id="asp-4-modal-no"
                        class="flex-1 py-3 px-4 rounded-lg font-semibold border-2 border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">No</button>
                </div>
            </div>
            <div id="asp-4-step-no-message" class="hidden">
                <h3 class="text-xl font-bold text-[var(--text-color)] mb-4 uppercase"
                    style="font-family: var(--font-display);">ASP 4 Hours (Less than Lethal)</h3>
                <p class="text-[var(--text-color)] text-[16px] leading-relaxed mb-6">Then you are not able to take this
                    course. Due to not working in these fields you are ineligible to take these courses.</p>
                <button type="button" id="asp-4-modal-close"
                    class="py-3 px-6 rounded-lg font-semibold text-white transition-colors"
                    style="background: var(--primary-color);">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dallasLawTargetUrl = '';
        var stepQuestion = document.getElementById('dallas-law-step-question');
        var stepNoMessage = document.getElementById('dallas-law-step-no-message');
        document.querySelectorAll('.dallas-law-trigger').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                dallasLawTargetUrl = this.getAttribute('href');
                stepQuestion.classList.remove('hidden');
                stepNoMessage.classList.add('hidden');
                document.getElementById('dallas-law-modal').classList.remove('hidden');
            });
        });
        document.getElementById('dallas-law-modal-yes').addEventListener('click', function() {
            document.getElementById('dallas-law-modal').classList.add('hidden');
            if (dallasLawTargetUrl) window.location.href = dallasLawTargetUrl;
        });
        document.getElementById('dallas-law-modal-no').addEventListener('click', function() {
            stepQuestion.classList.add('hidden');
            stepNoMessage.classList.remove('hidden');
        });
        document.getElementById('dallas-law-modal-close').addEventListener('click', function() {
            document.getElementById('dallas-law-modal').classList.add('hidden');
            stepQuestion.classList.remove('hidden');
            stepNoMessage.classList.add('hidden');
        });

        var asp4TargetUrl = '';
        var asp4StepQuestion = document.getElementById('asp-4-step-question');
        var asp4StepNoMessage = document.getElementById('asp-4-step-no-message');
        document.querySelectorAll('.asp-4-modal-trigger').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                asp4TargetUrl = this.getAttribute('href');
                asp4StepQuestion.classList.remove('hidden');
                asp4StepNoMessage.classList.add('hidden');
                document.getElementById('asp-4-modal').classList.remove('hidden');
            });
        });
        document.getElementById('asp-4-modal-yes').addEventListener('click', function() {
            document.getElementById('asp-4-modal').classList.add('hidden');
            if (asp4TargetUrl) window.location.href = asp4TargetUrl;
        });
        document.getElementById('asp-4-modal-no').addEventListener('click', function() {
            asp4StepQuestion.classList.add('hidden');
            asp4StepNoMessage.classList.remove('hidden');
        });
        document.getElementById('asp-4-modal-close').addEventListener('click', function() {
            document.getElementById('asp-4-modal').classList.add('hidden');
            asp4StepQuestion.classList.remove('hidden');
            asp4StepNoMessage.classList.add('hidden');
        });
    });
</script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: false,
    });
</script>
</body>

</html>
