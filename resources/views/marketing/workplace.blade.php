@extends('marketing.mkt-page')

@section('title', 'The Mobile Interactive Workplace — ScanLink')
@section('meta_description', 'ScanLink is a mobile visual communication solution to enhance health, safety, sustainability and productivity in the workplace.')

@php
    $check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
    $benefits = ['Health &amp; Safety', 'Quality / Standards', 'Productivity', 'Sustainability', 'Efficiency', 'Financial'];
    $wpFeatures = [
        ['Right here, right now', 'Bridge communication barriers of language, literacy and comprehension with mobile visual communication. Deliver instructional videos, pictures and diagrams instantly on demand.'],
        ['Interactive forms &amp; checklists', 'Improve efficiency and reduce paperwork with forms and checklists completed on the spot. Responses are delivered instantly to any nominated email and recorded in your database.'],
        ['Instant access to documents', 'Minimise productivity risk from missing documentation &mdash; view and download important compliance documents instantly, on demand.'],
        ['Manage user access', 'Password-protect access to information so it&rsquo;s available exclusively to individuals or a specific user group.'],
        ['Measure user engagement', 'Comprehensive analytics record engagement: local time, date, device type, browser type and GPS location &mdash; and the identity of the mobile user.'],
        ['Create, edit &amp; launch instantly', 'User-friendly content management templates make creating and editing your own branded, mobile-optimised content quick and easy.'],
        ['Technology that doesn&rsquo;t discriminate', 'Accessed by multiple device types and operating systems &mdash; not a device-restricted app. Produces dynamic Data Matrix and QR codes.'],
        ['Industrial label &amp; tag solutions', 'A range of industrial label and tag solutions, from single print to customised print-your-own packages.'],
    ];
@endphp

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">The Workplace</span>
            <h1>Minimise risk in your workplace</h1>
            <p>ScanLink is a mobile visual communication solution to enhance health, safety, sustainability and productivity. Give people instant access to information for any equipment, product, location or procedure &mdash; videos, documents, pictures, checklists and audit forms &mdash; by simply scanning a label or sign with a smartphone or tablet.</p>
            <div class="mkt__hero-cta">
                <a class="btn btn-primary btn-lg" href="{{ $register }}">Get started free</a>
                <button type="button" class="btn btn-secondary btn-lg" data-video="https://www.youtube.com/embed/dtjwTrk-ons?rel=0&autoplay=1">Watch the video</button>
            </div>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__media">
                <div class="mkt__media-text">
                    <span class="mkt__eyebrow">Right here, right now</span>
                    <h2>Visual communication &mdash; mobile access to information on demand</h2>
                    <p>An intelligent mobile engagement platform that lets you create, control and measure your own branded mobile interactive workplace communication program.</p>
                    <div class="mkt__hero-cta">
                        <button type="button" class="btn btn-primary" data-video="https://www.youtube.com/embed/dtjwTrk-ons?rel=0&autoplay=1">Watch the video</button>
                    </div>
                </div>
                <div class="mkt__video">
                    <iframe src="https://www.youtube.com/embed/dtjwTrk-ons?rel=0" title="ScanLink in the workplace" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--alt">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Benefits</span>
                <h2>ScanLink benefits employers and employees by minimising workplace risk</h2>
            </div>
            <div class="mkt__benefits">
                @foreach ($benefits as $b)
                    <div class="mkt__benefit">{!! $check !!} <span>{!! $b !!}</span></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Capabilities</span>
                <h2>Everything you need for a mobile interactive workplace</h2>
            </div>
            <div class="mkt__grid-3">
                @foreach ($wpFeatures as $f)
                    <article class="mkt__feature">
                        <div class="mkt__feature-icon">{!! str_replace('currentColor', '#008c00', $check) !!}</div>
                        <h3>{!! $f[0] !!}</h3>
                        <p>{!! $f[1] !!}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--tight">
        <div class="mkt__container">
            <div class="mkt__cta-band">
                <h2>Ready to get started?</h2>
                <p>Register your free ScanLink account, or contact us for more information.</p>
                <div class="mkt__hero-cta">
                    <a class="btn btn-on-dark btn-lg" href="{{ $register }}">Register free</a>
                    <a class="btn btn-outline-dark btn-lg" href="{{ route('marketing.contact') }}">Contact us</a>
                </div>
            </div>
        </div>
    </section>
@endsection
