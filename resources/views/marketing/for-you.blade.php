@extends('marketing.mkt-page')

@section('title', 'A Mobile Profile For You or Your Business — ScanLink')
@section('meta_description', 'Create a mobile profile for you or your business in minutes. Anything you print becomes a mobile interactive opportunity to educate, inform and sell.')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">For you &amp; your business</span>
            <h1>Anything you print is now a mobile interactive opportunity to&nbsp;educate, inform and&nbsp;sell</h1>
            <p>Create a mobile profile for you or your business in just a few minutes. It&rsquo;s easy &mdash; and your first code is free for a year.</p>
            <div class="mkt__hero-cta">
                <a class="btn btn-primary btn-lg" href="{{ $register }}">Create your profile free</a>
            </div>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">How it works</span>
                <h2>Four simple steps</h2>
            </div>
            <div class="mkt__steps">
                <div class="mkt__step">
                    <div class="mkt__step-num">1</div>
                    <div>
                        <h3>Register</h3>
                        <p>Open your ScanLink account with your first dynamic QR code free for a year.</p>
                    </div>
                </div>
                <div class="mkt__step">
                    <div class="mkt__step-num">2</div>
                    <div>
                        <h3>Create</h3>
                        <p>Use the &lsquo;Person/Business&rsquo; profile template to build your mobile profile. Upload videos, text, documents, pictures, web links and more.</p>
                    </div>
                </div>
                <div class="mkt__step">
                    <div class="mkt__step-num">3</div>
                    <div>
                        <h3>Print</h3>
                        <p>Download your QR code and print it on business cards, brochures &amp; flyers, signs, invoices &amp; receipts &mdash; anywhere.</p>
                    </div>
                </div>
                <div class="mkt__step">
                    <div class="mkt__step-num">4</div>
                    <div>
                        <h3>Measure</h3>
                        <p>Track the time, date, device type, browser type and even the GPS location each time your QR code is scanned.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--alt mkt__section--tight">
        <div class="mkt__container">
            <div class="mkt__belt">
                <p>Need a hand? Watch the <a href="{{ route('marketing.how-to') }}">How-to video tutorials</a> or <a href="{{ route('marketing.contact') }}">contact us</a> for assistance.</p>
                <div class="mkt__hero-cta" style="justify-content:center;margin-top:20px;">
                    <a class="btn btn-primary btn-lg" href="{{ $register }}">Ready to get started? Click here</a>
                </div>
            </div>
        </div>
    </section>
@endsection
