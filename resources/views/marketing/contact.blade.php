@extends('marketing.layout')

@section('title', 'Contact US — ScanLink')
@section('nav_contact_active', 'active')

@section('content')
<div class="scanlink-container">
    <h2 class="page-title">Contact us</h2>

    @if ($errors->any())
        <div>
            <label class="error">{{ $errors->first() }}</label>
            <br/><br/>
        </div>
    @elseif (session('contact_submitted'))
        <div>
            <label class="ok">Thanks — your enquiry has been sent.</label>
            <br/><br/>
        </div>
    @endif

    <form
        class="contact-form"
        id="frmcontact"
        name="frmcontact"
        method="post"
        action="{{ route('marketing.contact.submit') }}"
        enctype="multipart/form-data"
    >
        @csrf
        <ul class="form-view clearfix">
            <li>
                <label for="yourName">Name:</label>
                <input
                    type="text"
                    name="name"
                    id="yourName"
                    value="{{ old('name') }}"
                    class="text-fi"
                />
            </li>
            <li>
                <label for="email">Email:</label>
                <input
                    type="text"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="text-fi"
                />
            </li>
            <li>
                <label for="commentsText">Message:</label>
                <textarea name="comments" id="commentsText">{{ old('comments') }}</textarea>
            </li>
            <li>
                <label>Verification Code:</label>
                <br/>
                <img
                    src="{{ route('marketing.captcha') }}?t={{ time() }}"
                    alt="Verification code"
                    class="captcha"
                    width="150"
                    height="50"
                />
                <img src="{{ asset('images/capcha-img.png') }}" alt="" />
                <input type="text" class="text-fi" id="captcha" name="captcha" value="" autocomplete="off" />
            </li>
            <li>
                <input id="save" type="submit" name="submit" value="Send">
                <input type="hidden" name="submitted" id="submitted" value="true"/>
            </li>
        </ul>
    </form>

    <section class="contact-box">
        <h2 class="title-box">
            <img src="{{ asset('images/img2.png') }}" alt="" />&nbsp;ScanLink
        </h2>
        <section class="brd-btm">
            Submit your enquiry with the 'contact us' form or
            alternatively call us during business hours from Monday to Friday on...
        </section>
        <section class="brd-btm">
            <span class="tel-icon">+61 0364314025</span>
        </section>
        <section class="last">
            <span class="email-icon">
                <h3>ScanLink</h3>
                5 Wattle Ave,<br/>
                Emu Heights, Tasmania, 7320
            </span>
        </section>
    </section>
</div>
@endsection
