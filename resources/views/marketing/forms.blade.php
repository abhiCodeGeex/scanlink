@extends('marketing.mkt-page')

@section('title', 'Mobile Interactive Forms, Surveys & Checklists — ScanLink')
@section('meta_description', 'ScanLink Form Builder lets you create mobile interactive forms, surveys and checklists completed on a phone or tablet and recorded instantly — no paperwork.')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Form Builder</span>
            <h1>Create mobile interactive forms, surveys &amp; checklists</h1>
            <p>Build forms that are completed on a smartphone or tablet, then submitted and recorded instantly.</p>
            <div class="mkt__hero-cta">
                <a class="btn btn-primary btn-lg" href="{{ $register }}">Get started free</a>
                <button type="button" class="btn btn-secondary btn-lg" data-video="https://www.youtube.com/embed/MK6pIu_gf0A?rel=0&autoplay=1">Give it a try</button>
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--tight">
        <div class="mkt__container">
            <div class="mkt__belt">
                <p>ScanLink Form Builder enables you to quickly and easily create mobile interactive forms that can be completed on a smartphone or tablet, then submitted and recorded instantly &mdash; <strong>no paperwork, no filing!</strong></p>
            </div>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__media">
                <div class="mkt__media-text">
                    <span class="mkt__eyebrow">The process is simple</span>
                    <h2>Scan, complete, recorded</h2>
                    <p>Create your mobile interactive form and print your code. Mobile and tablet users scan it to instantly access the form and complete it on their device. The date, time, device type, location and response are recorded instantly.</p>
                    <div class="mkt__hero-cta">
                        <button type="button" class="btn btn-primary" data-video="https://www.youtube.com/embed/MK6pIu_gf0A?rel=0&autoplay=1">Watch it in action</button>
                    </div>
                </div>
                <div class="mkt__video">
                    <iframe src="https://www.youtube.com/embed/MK6pIu_gf0A?rel=0" title="ScanLink Form Builder" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--alt">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">It&rsquo;s easy &mdash; and free to start</span>
                <h2>From account to insights in four steps</h2>
            </div>
            <div class="mkt__grid-3">
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg></div>
                    <h3>1. Register</h3>
                    <p>Open your ScanLink account with your first dynamic QR code free for a year.</p>
                    <p style="margin-top:10px;"><button type="button" class="btn btn-secondary" data-video="https://www.youtube.com/embed/9aTjweHyAWw?rel=0&autoplay=1">Show me how</button></p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg></div>
                    <h3>2. Build your forms</h3>
                    <p>Simple click &amp; drag tools make it easy to create audit forms, pre-start checklists, surveys and more.</p>
                    <p style="margin-top:10px;"><button type="button" class="btn btn-secondary" data-video="https://www.youtube.com/embed/cYQnzxkp528?rel=0&autoplay=1">Show me how</button></p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5M12 15V3"/></svg></div>
                    <h3>3. Publish your code</h3>
                    <p>Download and print your ScanLink QR or Data Matrix code.</p>
                    <p style="margin-top:10px;"><button type="button" class="btn btn-secondary" data-video="https://www.youtube.com/embed/MrsyIv-72YI?rel=0&autoplay=1">Show me how</button></p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg></div>
                    <h3>4. Record the results</h3>
                    <p>Responses are recorded in your account to view or download anytime &mdash; and can be sent automatically to email.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Use cases</span>
                <h2>So many ways to save time and money</h2>
            </div>
            <div class="mkt__benefits">
                @foreach (['Site inductions','Customer surveys','Quality checks','Petitions','Pre-start checklists','Enquiry forms','Education','Competition entries','Registration forms','Order forms','Audit forms','and more…'] as $u)
                    <div class="mkt__benefit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg> <span>{{ $u }}</span></div>
                @endforeach
            </div>
            <div class="mkt__hero-cta" style="justify-content:center;margin-top:36px;">
                <a class="btn btn-primary btn-lg" href="{{ $register }}">Get started with your first code free for a year</a>
            </div>
        </div>
    </section>
@endsection
