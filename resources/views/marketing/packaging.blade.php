@extends('marketing.mkt-page')

@section('title', 'Packaging & Signage — ScanLink')
@section('meta_description', 'Add value to products and services and make your advertising, packaging and signage mobile interactive with ScanLink.')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Packaging &amp; Signage</span>
            <h1>Make your advertising, packaging &amp; signage mobile interactive</h1>
            <p>ScanLink lets you quickly generate intelligent QR and Data Matrix codes that create an instant connection between mobile consumers and your products and services &mdash; plus a user-friendly platform to create and launch your own branded mobile content.</p>
            <div class="mkt__hero-cta">
                <a class="btn btn-primary btn-lg" href="{{ $register }}">Start free &mdash; first code free for a year</a>
                <a class="btn btn-secondary btn-lg" href="{{ route('marketing.how-to') }}">See how it works</a>
            </div>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__grid-3">
                <article class="mkt__feature">
                    <div class="mkt__feature-icon">
                        <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg>
                    </div>
                    <h3>Powerful analytics</h3>
                    <p>Marketing becomes 100% measurable &mdash; every scan records the time, date, device type, browser and even the location.</p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon">
                        <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3>Customised data collection</h3>
                    <p>Instantly create and launch your own customised pop-up message to collect names, mobile numbers and email addresses.</p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon">
                        <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </div>
                    <h3>Create, edit and launch instantly</h3>
                    <p>User-friendly templates and edit tools let you build your own branded, mobile-optimised content &mdash; videos, text, pictures, documents, web links and more.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--tight">
        <div class="mkt__container">
            <div class="mkt__cta-band">
                <h2>Start your own mobile interactive initiative today</h2>
                <p>Your first code is free for a year.</p>
                <div class="mkt__hero-cta">
                    <a class="btn btn-on-dark btn-lg" href="{{ $register }}">Register free</a>
                    <a class="btn btn-outline-dark btn-lg" href="{{ route('marketing.contact') }}">Talk to us</a>
                </div>
            </div>
        </div>
    </section>
@endsection
