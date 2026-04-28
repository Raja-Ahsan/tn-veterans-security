@extends('layouts.web.master')

@section('content')
    <main class="overflow-hidden">
        <section class="inner-hero">
            <div class="inner-hero-overlay"></div>
            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="max-w-[1000px]">
                    <h1 class="inner-hero-title" data-aos="fade-down" data-aos-duration="1000">
                        PRIVACY <span class="text-(--primary-color)">POLICY</span>
                    </h1>
                     
                </div>
            </div>
        </section>

        <section class="py-12 md:py-16">
            <div class="container mx-auto px-4 lg:px-10 max-w-4xl">
                <div class="prose prose-gray max-w-none">
                    <p>
                        This Privacy Policy describes how {{ config('app.name') }} ("we", "us", "our") collects, uses, and
                        discloses information when you use our website, training services, and related offerings.
                    </p>

                    <h2>Information we collect</h2>
                    <ul>
                        <li><strong>Information you provide:</strong> name, email, phone, booking details, and payment-related information you submit through forms or checkout.</li>
                        <li><strong>Automatically collected data:</strong> basic technical information such as IP address, browser type, and device information, where applicable.</li>
                    </ul>

                    <h2>How we use information</h2>
                    <ul>
                        <li>To provide, schedule, and manage training bookings and services.</li>
                        <li>To process payments and communicate with you about your booking.</li>
                        <li>To improve our website, security, and customer support.</li>
                        <li>To comply with legal obligations and prevent fraud or abuse.</li>
                    </ul>

                    <h2>How we share information</h2>
                    <p>
                        We may share information with service providers that help us operate the business (for example, payment
                        processors, email delivery, and accounting tools), and when required by law. We do not sell your personal
                        information.
                    </p>

                    <h2>Data retention</h2>
                    <p>
                        We retain information for as long as needed to provide services, meet legal requirements, and resolve
                        disputes.
                    </p>

                    <h2>Your choices</h2>
                    <p>
                        You may request access, correction, or deletion of certain personal information where applicable. Contact us
                        using the information below.
                    </p>

                    <h2>Contact</h2>
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
