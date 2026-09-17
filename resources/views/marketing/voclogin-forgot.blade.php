@extends('marketing.mkt-page')

@section('title', 'Reset VOCC Card Password — ScanLink')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Card holder access</span>
            <h1>Reset your card password</h1>
            <p>This resets the password for your VOCC card login only. Any client account you have keeps its own password.</p>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <form method="post" action="{{ route('marketing.voclogin.forgot.send') }}" class="mkt__voclogin-form" style="max-width: 30rem;">
                @csrf

                @if (session('status'))<p class="mkt__login-status">{{ session('status') }}</p>@endif
                @error('email')<span class="mkt__login-error">{{ $message }}</span>@enderror

                <div class="mkt__login-field">
                    <label for="voc-forgot-email">Email</label>
                    <input type="email" name="email" id="voc-forgot-email" value="{{ old('email') }}" required autocomplete="username">
                </div>

                <div class="mkt__login-row">
                    <a class="mkt__forgot" href="{{ route('marketing.voclogin') }}">Back to login</a>
                    <button type="submit" class="btn btn-primary">Send reset link</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@include('marketing.partials.voclogin-form-styles')
