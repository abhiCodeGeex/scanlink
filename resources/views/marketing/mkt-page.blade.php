<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ScanLink')</title>
    <meta name="description" content="@yield('meta_description', 'ScanLink — QR code generator and mobile engagement platform.')">
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/marketing.css') }}?v=9">
    @stack('head')
</head>
<body class="mkt">
<a class="mkt__skip-link" href="#main">Skip to content</a>

@php
    $howTo = $howToLinks ?? [];
    $register = url('/portal/register');
@endphp

<header class="mkt__header">
    <div class="mkt__container mkt__header-inner">
        <a class="mkt__brand" href="{{ route('marketing.home') }}" aria-label="ScanLink home">
            <img src="{{ asset('images/logo.png') }}" alt="ScanLink">
        </a>
        <nav class="mkt__nav" aria-label="Primary">
            <a class="mkt__nav-link" href="{{ url('/voclogin') }}">VOCC Login</a>
            <a class="mkt__nav-link" href="{{ route('marketing.pricing') }}">Pricing</a>
            <a class="mkt__nav-link" href="{{ route('marketing.contact') }}">Contact us</a>
            <div class="mkt__dropdown">
                <a class="mkt__nav-link" href="{{ route('marketing.how-to') }}" aria-haspopup="true">How to ▾</a>
                @if (!empty($howTo))
                    <ul class="mkt__dropdown-menu">
                        @foreach ($howTo as $howto)
                            <li><a href="{{ $howto['url'] }}" data-video="{{ $howto['url'] }}">{{ $howto['title'] }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </nav>
        <div class="mkt__actions">
            <button type="button" class="btn btn-ghost" data-open-login>Login</button>
            <a class="btn btn-primary" href="{{ $register }}">Get started free</a>
        </div>
        <button type="button" class="mkt__hamburger" data-toggle-nav aria-label="Open menu" aria-expanded="false" aria-controls="mkt-mobile-nav">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div class="mkt__mobile-nav" id="mkt-mobile-nav">
        <div class="mkt__container">
            <a href="{{ url('/voclogin') }}">VOCC Login</a>
            <a href="{{ route('marketing.pricing') }}">Pricing</a>
            <a href="{{ route('marketing.contact') }}">Contact us</a>
            @if (!empty($howTo))
                <details>
                    <summary>How to</summary>
                    <ul>
                        @foreach ($howTo as $howto)
                            <li><a href="{{ $howto['url'] }}" data-video="{{ $howto['url'] }}">{{ $howto['title'] }}</a></li>
                        @endforeach
                    </ul>
                </details>
            @else
                <a href="{{ route('marketing.how-to') }}">How to</a>
            @endif
            <div class="mkt__mobile-cta">
                <button type="button" class="btn btn-secondary" data-open-login>Login</button>
                <a class="btn btn-primary" href="{{ $register }}">Get started free</a>
            </div>
        </div>
    </div>
</header>

<main id="main">
    @yield('content')
</main>

<footer class="mkt__footer">
    <div class="mkt__container">
        <div class="mkt__footer-top">
            <div class="mkt__footer-brand">
                <img src="{{ asset('images/logo.png') }}" alt="ScanLink">
                <p>A powerful QR code generator and mobile engagement platform for creating mobile interactive experiences that educate, inform and sell.</p>
            </div>
            <div>
                <h4>Company</h4>
                <ul>
                    <li><a href="{{ route('marketing.home') }}">Home</a></li>
                    <li><a href="{{ route('marketing.pricing') }}">Pricing</a></li>
                    <li><a href="{{ route('marketing.faq') }}">FAQ</a></li>
                    <li><a href="{{ route('marketing.workplace') }}">The Workplace</a></li>
                    <li><a href="{{ route('marketing.mobileVideo') }}">Mobile video</a></li>
                </ul>
            </div>
            <div>
                <h4>Support</h4>
                <ul>
                    <li><a href="{{ route('marketing.contact') }}">Contact us</a></li>
                    <li><a href="{{ route('marketing.enquiry') }}">Solutions enquiry</a></li>
                    <li><a href="{{ route('marketing.how-to') }}">How to</a></li>
                    <li><a href="{{ route('marketing.terms') }}">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('marketing.privacy') }}">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="mkt__footer-bottom">
            <span class="aus"><img src="{{ asset('images/aus-map-icon.png') }}" alt=""> An Australian Innovation</span>
            <span>&copy; {{ date('Y') }} ScanLink Technologies. Powered by ScanLink.</span>
        </div>
    </div>
</footer>

<div class="mkt__overlay" id="mkt-login" role="dialog" aria-modal="true" aria-labelledby="mkt-login-title" @if ($errors->any()) data-open="true" @endif>
    <div class="mkt__modal">
        <button type="button" class="mkt__modal-close" data-close aria-label="Close login">&times;</button>
        <h2 id="mkt-login-title">Log in to ScanLink</h2>
        @error('email')<span class="mkt__login-error">{{ $message }}</span>@enderror
        @error('password')<span class="mkt__login-error">{{ $message }}</span>@enderror
        <form id="signin" method="post" action="{{ route('marketing.portal-login') }}">
            @csrf
            <div class="mkt__login-field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username">
            </div>
            <div class="mkt__login-field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required autocomplete="current-password">
            </div>
            <div class="mkt__login-row">
                <a class="mkt__forgot" href="{{ url('/portal/password-reset/request') }}">Forgot your password?</a>
                <button type="submit" class="btn btn-primary">Log in</button>
            </div>
        </form>
    </div>
</div>

<div class="mkt__overlay" id="mkt-video" role="dialog" aria-modal="true" aria-label="Video player">
    <div class="mkt__modal mkt__modal--video">
        <button type="button" class="mkt__modal-close" data-close aria-label="Close video">&times;</button>
        <div class="mkt__video-frame" id="mkt-video-frame"></div>
    </div>
</div>

<script>
    (function () {
        var body = document.body;
        function openOverlay(el) { if (el) { el.setAttribute('data-open', 'true'); body.style.overflow = 'hidden'; } }
        function closeOverlay(el) { if (el) { el.removeAttribute('data-open'); body.style.overflow = ''; } }
        var login = document.getElementById('mkt-login');
        var video = document.getElementById('mkt-video');
        var videoFrame = document.getElementById('mkt-video-frame');
        var burger = document.querySelector('[data-toggle-nav]');
        var mobileNav = document.getElementById('mkt-mobile-nav');
        if (burger && mobileNav) {
            burger.addEventListener('click', function () {
                var open = mobileNav.getAttribute('data-open') === 'true';
                mobileNav.setAttribute('data-open', open ? 'false' : 'true');
                burger.setAttribute('aria-expanded', open ? 'false' : 'true');
            });
        }
        document.querySelectorAll('[data-open-login]').forEach(function (b) {
            b.addEventListener('click', function () {
                if (mobileNav) { mobileNav.setAttribute('data-open', 'false'); }
                openOverlay(login);
                var email = document.getElementById('email');
                if (email) { setTimeout(function () { email.focus(); }, 50); }
            });
        });
        document.querySelectorAll('[data-video]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var url = el.getAttribute('data-video');
                if (!url) { return; }
                videoFrame.innerHTML = '<iframe src="' + url + '" title="Video" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                openOverlay(video);
            });
        });
        document.querySelectorAll('.mkt__overlay').forEach(function (ov) {
            ov.addEventListener('click', function (e) {
                if (e.target === ov || e.target.closest('[data-close]')) {
                    closeOverlay(ov);
                    if (ov === video) { videoFrame.innerHTML = ''; }
                }
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeOverlay(login);
                if (video.getAttribute('data-open')) { closeOverlay(video); videoFrame.innerHTML = ''; }
            }
        });
        if (window.location.hash === '#login' || (login && login.getAttribute('data-open'))) { openOverlay(login); }
    })();
</script>
@stack('scripts')
@include('filament.hooks.themed-dialog')
</body>
</html>
