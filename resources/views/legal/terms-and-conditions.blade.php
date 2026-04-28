@extends('layouts.web.master')

@section('content')
    <main class="overflow-hidden">
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px]">
                    <h1 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        TERMS & <span class="text-(--primary-color)">CONDITIONS</span>
                    </h1>
                     
                </div>
            </div>
        </section>

        <section class="py-12 md:py-16">
            <div class="container mx-auto px-4 lg:px-10 max-w-4xl">
                <div class="prose prose-gray max-w-none">
                    <p>
                        This Terms & Conditions ("Agreement") is a legal agreement between you ("you", "your") and
                        {{ config('app.name') }} ("we", "us", "our") governing your access to and use of our website, booking
                        system, and related online services (the "Services").
                    </p>

                    <h2>Acceptance of Terms</h2>
                    <p>
                        By accessing or using the Services, you agree to be bound by these Terms & Conditions. If you do not agree, do not
                        use the Services.
                    </p>

                    <h2>Use of Services</h2>
                    <p>
                        Subject to these Terms & Conditions, we grant you a limited, non-exclusive, non-transferable, revocable license to
                        access and use the Services for your personal or internal business purposes related to booking and
                        receiving training services from us.
                    </p>

                    <h2>Prohibited Uses</h2>
                    <ul>
                        <li>No unlawful, fraudulent, harassing, or abusive use of the Services.</li>
                        <li>No attempt to interfere with, disrupt, or gain unauthorized access to our systems or data.</li>
                        <li>No automated scraping, reverse engineering, or circumvention of security controls, except as permitted by law.</li>
                    </ul>

                    <h2>Third-Party Services</h2>
                    <p>
                        The Services may integrate with third-party providers (for example, payment processors or accounting tools).
                        Your use of those services may be subject to additional third-party terms and conditions.
                    </p>

                    <h2>Disclaimer</h2>
                    <p>
                        The Services are provided "as is" and "as available" without warranties of any kind, whether express or
                        implied, to the maximum extent permitted by applicable law.
                    </p>

                    <h2>Limitation of Liability</h2>
                    <p>
                        To the maximum extent permitted by law, we will not be liable for any indirect, incidental, special,
                        consequential, or punitive damages, or any loss of profits or data, arising out of or related to your use
                        of the Services.
                    </p>

                    <h2>Changes to Terms & Conditions</h2>
                    <p>
                        We may update these Terms & Conditions from time to time. Continued use of the Services after changes means you
                        accept the updated Terms & Conditions.
                    </p>

                    <h2>Contact Information</h2>
                    <p>
                        @if($siteSettings && $siteSettings->email)
                            Email: <a class="text-(--primary-color)" href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a><br>
                        @endif
                        @if($siteSettings && $siteSettings->phone)
                            Phone: {{ $siteSettings->phone }}<br>
                        @endif
                        @if($siteSettings && $siteSettings->address)
                            Address: {{ $siteSettings->address }}
                        @endif
                    </p>

                     
                </div>
            </div>
        </section>
    </main>
@endsection
