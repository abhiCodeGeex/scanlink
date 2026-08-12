@extends('marketing.mkt-page')

@section('title', 'Contact us — ScanLink')
@section('meta_description', 'Get in touch with the ScanLink team. Submit an enquiry or call us during business hours.')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Contact</span>
            <h1>Contact us</h1>
            <p>Submit your enquiry below, or call us during business hours Monday to Friday.</p>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__contact">
                <div class="mkt__form">
                    @if ($errors->any())
                        <div class="mkt__alert mkt__alert--err">{{ $errors->first() }}</div>
                    @elseif (session('contact_submitted'))
                        <div class="mkt__alert mkt__alert--ok">Thanks &mdash; your enquiry has been sent.</div>
                    @endif

                    <form id="frmcontact" method="post" action="{{ route('marketing.contact.submit') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mkt__form-field">
                            <label for="yourName">Name <span class="req" aria-hidden="true">*</span></label>
                            <input type="text" name="name" id="yourName" value="{{ old('name') }}" required>
                        </div>
                        <div class="mkt__form-field">
                            <label for="email">Email <span class="req" aria-hidden="true">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email">
                        </div>
                        <div class="mkt__form-field">
                            <label for="commentsText">Message <span class="req" aria-hidden="true">*</span></label>
                            <textarea name="comments" id="commentsText" required>{{ old('comments') }}</textarea>
                        </div>
                        <div class="mkt__form-field">
                            <label for="captcha">Verification code <span class="req" aria-hidden="true">*</span></label>
                            <div class="mkt__captcha">
                                <img src="{{ route('marketing.captcha') }}?t={{ time() }}" alt="Verification code" width="150" height="50">
                                <input type="text" id="captcha" name="captcha" value="" autocomplete="off" required style="max-width:200px;">
                            </div>
                        </div>
                        <input type="hidden" name="submitted" id="submitted" value="true">
                        <button type="submit" class="btn btn-primary btn-lg">Send enquiry</button>
                    </form>
                </div>

                <aside class="mkt__contact-card">
                    <h3>ScanLink</h3>
                    <p>Submit your enquiry with the form, or call us during business hours Monday to Friday.</p>
                    <p class="tel">+61 03 6431 4025</p>
                    <p>
                        5 Wattle Ave,<br>
                        Emu Heights, Tasmania, 7320<br>
                        Australia
                    </p>
                </aside>
            </div>
        </div>
    </section>
@endsection
