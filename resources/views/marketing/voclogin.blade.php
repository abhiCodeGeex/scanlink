@extends('marketing.mkt-page')

@section('title', 'VOCC Card Holder Login — ScanLink')
@section('meta_description', 'Sign in to view and update your ScanLink VOCC card, your details and your competency documents.')

@section('content')
    {{-- Legacy voclogin.php was a dedicated sign-in screen, printed on cards and handed out at
         induction. The rebuilt portal shares one login, but the branded entry point still has to
         exist so an old bookmark or a printed card lands somewhere that says VOCC. --}}
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
                <form method="post" action="{{ route('marketing.portal-login') }}" class="mkt__voclogin-form">
                    @csrf

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
                        <a class="mkt__forgot" href="{{ url('/portal/password-reset/request') }}">Forgot your password?</a>
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

@push('head')
    <style>
        .mkt__voclogin {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 2.5rem;
            align-items: start;
            max-width: 62rem;
        }
        .mkt__voclogin-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.75rem;
            border: 1px solid #e3e8e3;
            border-radius: 12px;
            background: #fff;
        }
        .mkt__voclogin-aside h2 { margin-top: 0; font-size: 1.15rem; }
        .mkt__voclogin-aside ul { padding-left: 1.1rem; display: flex; flex-direction: column; gap: .45rem; }
        .mkt__voclogin-note { color: #5d675d; font-size: .92rem; }
        @media (max-width: 860px) {
            .mkt__voclogin { grid-template-columns: minmax(0, 1fr); gap: 1.75rem; }
        }
    </style>
@endpush
