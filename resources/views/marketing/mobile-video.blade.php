@extends('marketing.mkt-page')

@section('title', 'Mobile Video Production — ScanLink')
@section('meta_description', 'Affordable mobile-optimised video and animation production — scriptwriting, filming, editing, animation, voice-over and music services.')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Mobile video production</span>
            <h1>Show people how, right when and where they need to know</h1>
            <p>It makes a lot of sense: if you can show someone how to do something right when and where they need to know, they have the best possible opportunity to perform the task correctly and minimise the risk of harming themselves or others.</p>
            <div class="mkt__hero-cta">
                <a class="btn btn-primary btn-lg" href="{{ route('marketing.contact') }}">Enquire now</a>
                <a class="btn btn-secondary btn-lg" href="{{ route('marketing.enquiry') }}">Solutions enquiry</a>
            </div>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__prose-page">
                <p>The ability to deliver video to a mobile device right when people need it presents enormous opportunities to enhance the way we share knowledge.</p>
                <p>Producing effective and engaging mobile-optimised video requires a different approach to producing video for larger screens. Mobile is a very personal and direct medium, and there are specific production techniques required to ensure the message is communicated in an engaging and effective way.</p>
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--alt">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Our services</span>
                <h2>Affordable mobile-optimised video &amp; animation</h2>
                <p>Our experienced creative and production team offers a wide range of affordable mobile-optimised video and animation production services, including:</p>
            </div>
            <div class="mkt__grid-3">
                @php
                    $services = [
                        ['Scriptwriting &amp; pre-production', 'Concept, storyboard and planning tailored for the small screen.'],
                        ['Casting', 'Find the right talent to carry your message.'],
                        ['Filming', 'Professional capture optimised for mobile viewing.'],
                        ['Editing', 'Tight, engaging edits built for short attention spans.'],
                        ['Animation', 'Explainer animation that makes complex ideas simple.'],
                        ['Voice-over', 'Clear, professional narration in the right tone.'],
                        ['Music services', 'Licensed music and audio to finish your production.'],
                    ];
                @endphp
                @foreach ($services as $s)
                    <article class="mkt__feature">
                        <div class="mkt__feature-icon"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m23 7-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
                        <h3>{!! $s[0] !!}</h3>
                        <p>{{ $s[1] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--tight">
        <div class="mkt__container">
            <div class="mkt__cta-band">
                <h2>Contact us to learn more about how mobile video can work for you</h2>
                <p>Tell us about your project and we'll help you get started.</p>
                <div class="mkt__hero-cta">
                    <a class="btn btn-on-dark btn-lg" href="{{ route('marketing.contact') }}">Contact us</a>
                    <a class="btn btn-outline-dark btn-lg" href="{{ route('marketing.enquiry') }}">Solutions enquiry</a>
                </div>
            </div>
        </div>
    </section>
@endsection
