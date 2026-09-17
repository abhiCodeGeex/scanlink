@extends('marketing.mkt-page')

@section('title', 'Set VOCC Card Password — ScanLink')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Card holder access</span>
            <h1>Set a new card password</h1>
            <p>For the VOCC card login of {{ $email }}.</p>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <form method="post" action="{{ request()->fullUrl() }}" class="mkt__voclogin-form" style="max-width: 30rem;">
                @csrf

                @error('password')<span class="mkt__login-error">{{ $message }}</span>@enderror

                <div class="mkt__login-field">
                    <label for="voc-new-password">New password</label>
                    <input type="password" name="password" id="voc-new-password" required minlength="6" autocomplete="new-password">
                </div>

                <div class="mkt__login-field">
                    <label for="voc-confirm-password">Confirm new password</label>
                    <input type="password" name="password_confirmation" id="voc-confirm-password" required minlength="6" autocomplete="new-password">
                </div>

                <div class="mkt__login-row">
                    <a class="mkt__forgot" href="{{ route('marketing.voclogin') }}">Back to login</a>
                    <button type="submit" class="btn btn-primary">Save password</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@include('marketing.partials.voclogin-form-styles')
