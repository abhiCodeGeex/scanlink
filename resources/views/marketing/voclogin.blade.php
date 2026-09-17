@extends('marketing.mkt-page')

@section('title', 'VOCC Card Holder Login — ScanLink')
@section('meta_description', 'Sign in to view and update your ScanLink VOCC card, your details and your competency documents.')

@section('content')
    {{-- Legacy voclogin.php was a dedicated sign-in screen, printed on cards and handed out at
         induction. This is that entry point: its own address and its own POST endpoint, so a
         card holder no longer signs in through the client login. --}}
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Card holder access</span>
            <h1>VOCC card holder login</h1>
            <p>Sign in to check your card, update your details and keep your competency documents current.</p>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__voclogin">
                <form method="post" action="{{ route('marketing.voclogin.submit') }}" class="mkt__voclogin-form">
                    @csrf

                    @if (session('status'))<p class="mkt__login-status">{{ session('status') }}</p>@endif
                    @error('email')<span class="mkt__login-error">{{ $message }}</span>@enderror
                    @error('password')<span class="mkt__login-error">{{ $message }}</span>@enderror

                    <div class="mkt__login-field">
                        <label for="voc-email">Email</label>
                        <input type="email" name="email" id="voc-email" value="{{ old('email') }}" required autocomplete="username">
                    </div>

                    <div class="mkt__login-field">
                        <label for="voc-password">Password</label>
                        <input type="password" name="password" id="voc-password" required autocomplete="current-password">
                    </div>

                    <div class="mkt__login-row">
                        <a class="mkt__forgot" href="{{ route('marketing.voclogin.forgot') }}">Forgot your password?</a>
                        <button type="submit" class="btn btn-primary">Log in</button>
                    </div>
                </form>

                <aside class="mkt__voclogin-aside">
                    <h2>What you can do here</h2>
                    <ul>
                        <li>See your card exactly as it appears when someone scans it.</li>
                        <li>Update your contact, emergency and employer details.</li>
                        <li>Upload renewed tickets and licences before they expire.</li>
                    </ul>
                    <p class="mkt__voclogin-note">Your login was issued by whoever set up your card. If you don&rsquo;t have one, ask them &mdash; not every VOCC card comes with card holder access.</p>
                </aside>
            </div>
        </div>
    </section>
@endsection

@include('marketing.partials.voclogin-form-styles')
