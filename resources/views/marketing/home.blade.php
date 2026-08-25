<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ScanLink — QR Code Generator &amp; Mobile Engagement Platform</title>
    <meta name="description" content="ScanLink is a powerful dynamic QR code generator and mobile engagement platform. Upload content, download your dynamic QR code, and measure every scan.">
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/marketing.css') }}?v=9">
</head>
<body class="mkt">
<a class="mkt__skip-link" href="#main">Skip to content</a>

@php
    $howTo = $howToLinks ?? [];
    $register = url('/portal/register');
@endphp

{{-- ===================== Header ===================== --}}
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
    {{-- ===================== Hero ===================== --}}
    <section class="mkt__hero">
        <div class="mkt__container mkt__hero-inner">
            <div>
                <h1>Create<span class="dot">.</span> Connect<span class="dot">.</span> Measure<span class="dot">.</span></h1>
                <p class="mkt__hero-lead">
                    The world&rsquo;s best QR code generator and content management platform for
                    creating mobile interactive experiences that <b>educate</b>, <b>inform</b> and <b>sell</b>.
                </p>
                <div class="mkt__hero-cta">
                    <a class="btn btn-primary btn-lg" href="{{ $register }}">Start your first code free</a>
                    <a class="btn btn-secondary btn-lg" href="#solutions">See what you can build</a>
                </div>
                <div class="mkt__hero-trust">
                    <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg> First code free for a year</span>
                    <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg> Dynamic &amp; editable codes</span>
                    <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg> Scan analytics built in</span>
                </div>
            </div>
            <div class="mkt__hero-card">
                <h3>Your dynamic QR, ready in minutes</h3>
                <div class="mkt__hero-qr">
                    <img src="{{ asset('images/qr_code_sample.png') }}" alt="Sample ScanLink QR code">
                    <p>Upload your content, download your dynamic QR code, then track every scan &mdash; time, date, device and GPS location.</p>
                </div>
                <a class="btn btn-primary" style="width:100%;margin-top:16px;" href="#express">Try the Express Generator</a>
            </div>
        </div>
    </section>

    {{-- ===================== Features ===================== --}}
    <section class="mkt__section mkt__section--alt">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Why ScanLink</span>
                <h2>Simple to create. Powerful to measure.</h2>
                <p>Everything you need to launch, print anywhere and understand your audience.</p>
            </div>
            <div class="mkt__grid-3">
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><img src="{{ asset('images/easy-info-icon.png') }}" alt=""></div>
                    <h3>It&rsquo;s easy</h3>
                    <p>Simply upload your content and download your &lsquo;dynamic&rsquo; QR code &mdash; no design skills required.</p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><img src="{{ asset('images/versatile-info-icon.png') }}" alt=""></div>
                    <h3>Versatile</h3>
                    <p>Print your QR code on product labels, brochures, signage, business cards &mdash; anywhere.</p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><img src="{{ asset('images/smart-info-icon.png') }}" alt=""></div>
                    <h3>Smart</h3>
                    <p>Every scan records the time, date, device and GPS location. Collect names, mobile numbers and emails to build your database.</p>
                </article>
            </div>
        </div>
    </section>

    {{-- ===================== Express Code Generator ===================== --}}
    <section class="mkt__section" id="express">
        <div class="mkt__container">
            <div class="mkt__ecg">
                <div class="mkt__ecg-left">
                    <h2>Express Code Generator</h2>
                    <p>Just need to create and download a basic mobile code fast?</p>
                    <div class="mkt__ecg-form">
                        <div class="mkt__field">
                            <label for="txt_url">Enter the web page address (URL)</label>
                            <input type="text" value="http://" name="txt_url" id="txt_url" class="url-textbox">
                        </div>
                        <div>
                            <span class="mkt__field" style="display:block;"><label>Code type</label></span>
                            <div class="mkt__radios">
                                <label class="mkt__radio"><input type="radio" name="code_type" class="type_of_code" value="0" checked> QR</label>
                                <label class="mkt__radio"><input type="radio" name="code_type" class="type_of_code" value="1"> Data Matrix</label>
                            </div>
                        </div>
                    </div>
                    <div class="mkt__ecg-note">
                        <img src="{{ asset('images/monitor3.png') }}" alt="">
                        <div>
                            <h3>Create a mobile code with the works&hellip;</h3>
                            <p>Powerful analytics, data collection, content templates, mobile interactive forms and more.</p>
                            <p class="green_text_normal" style="margin-top:8px;">
                                <a href="{{ $register }}"><b>Get started</b> with your <b>first code free</b> for a year!</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mkt__ecg-right">
                    <h2>Code Preview</h2>
                    <div class="mkt__ecg-qr">
                        <img src="{{ asset('images/qr_code_sample.png') }}" id="code_preview_img" alt="QR code preview">
                        <input type="hidden" name="code_preview_flag" id="code_preview_flag" value="0">
                    </div>
                    <div class="mkt__ecg-download">
                        <label class="visually-hidden" for="download_option">Download format</label>
                        <select name="download_option" id="download_option">
                            <option value="" selected>Download as&hellip;</option>
                            <option value="pdf">PDF</option>
                            <option value="png">PNG</option>
                            <option value="jpg">JPG</option>
                        </select>
                        <button type="button" class="btn btn-primary" id="ecg_download">Download</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== Applications ===================== --}}
    <section class="mkt__section mkt__section--alt">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Use cases</span>
                <h2>Hundreds of applications for ScanLink</h2>
                <p>Click any card to download a PDF information sheet.</p>
            </div>
            @php
                $pdfBase = 'https://scanlink.com.au/download_file.php';
                $apps = [
                    ['ScanLink-Hotel-Surveys.pdf', 'ScanLink-Hotel-Surveys.jpg', 'Hotel Surveys'],
                    ['Museum-Flyer.pdf', 'Museum-Flyer.jpg', 'Arts & Culture'],
                    ['ScanLink-Intercative-Memorials.pdf', 'ScanLink-Memorial-Flyer.jpg', 'Monuments & Memorials'],
                    ['Business-mobile-brochure-HR.pdf', 'Business-mobile-brochure.jpg', 'Business Profile'],
                    ['ScanLink-Mobile-Surveys-2.pdf', 'ScanLink-Customer-feedback-Surveys.jpg', 'Customer Feedback'],
                    ['ScanLink-for-product-packaging.pdf', 'ScanLink-Product-packaging.jpg', 'Product Packaging'],
                    ['Mobile-Interactive-Workplace.pdf', 'Mobile-Interactive-Workplace.jpg', 'The Workplace'],
                ];
            @endphp
            <div class="mkt__apps">
                @foreach ($apps as $app)
                    <a class="mkt__app-card" href="{{ $pdfBase }}?filename={{ urlencode($app[0]) }}&filepath=images/brochure" target="_blank" rel="noopener">
                        <span class="mkt__app-thumb"><img src="{{ asset('images/'.$app[1]) }}?v=2" alt="{{ $app[2] }} information sheet"></span>
                        <p>{{ $app[2] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== Museum video ===================== --}}
    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__media">
                <div class="mkt__media-text">
                    <span class="mkt__eyebrow">Case study</span>
                    <h2>The Hellenic Museum brings exhibitions to life</h2>
                    <p>See how the Hellenic Museum uses ScanLink to create engaging mobile interactive exhibitions.</p>
                    <div class="mkt__hero-cta">
                        <button type="button" class="btn btn-primary" data-video="https://www.youtube.com/embed/kzARc58KXTA?rel=0&autoplay=1">Watch the video</button>
                    </div>
                </div>
                <div class="mkt__video">
                    <iframe src="https://www.youtube.com/embed/kzARc58KXTA?rel=0" title="Hellenic Museum uses ScanLink" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== Solutions ===================== --}}
    <section class="mkt__section mkt__section--alt" id="solutions">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Solutions</span>
                <h2>Create a mobile interactive experience for anything</h2>
            </div>
            @php
                $solutions = [
                    ['website-680x249.jpg', 'Create a ScanLink QR code linked to any URL', 'howto', 'https://www.youtube.com/embed/uEDTnBPUk28?rel=0&autoplay=1', null],
                    ['Low-res2.png', 'Create mobile interactive packaging &amp; signage', 'more', null, route('marketing.packaging')],
                    ['banner_workplace3.png', 'Create a mobile interactive workplace to enhance health, safety, sustainability &amp; productivity', 'more', null, route('marketing.workplace')],
                    ['20140320_113407 LR.jpg', 'Create mobile interactive forms, surveys &amp; checklists', 'more', null, route('marketing.forms')],
                    ['20140604_135849_new.jpg', 'Create a mobile profile for you or your business', 'more', null, route('marketing.forYou')],
                ];
            @endphp
            <div class="mkt__solutions">
                @foreach ($solutions as $s)
                    <article class="mkt__solution">
                        <div class="mkt__solution-media"><img src="{{ asset('images/'.$s[0]) }}" alt="" loading="lazy"></div>
                        <div class="mkt__solution-body">
                            <h3>{!! $s[1] !!}</h3>
                            <div class="mkt__solution-cta">
                                @if ($s[2] === 'howto' && $s[3])
                                    <button type="button" class="btn btn-secondary" data-video="{{ $s[3] }}">Show me how</button>
                                @else
                                    <a class="btn btn-secondary" href="{{ $s[4] }}">Show me more</a>
                                @endif
                                <a class="btn btn-primary" href="{{ $register }}">Do it now</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== Testimonials ===================== --}}
    @if (!empty($testimonials) && count($testimonials))
    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Testimonials</span>
                <h2>What our clients say</h2>
            </div>
            <div class="mkt__testimonials">
                @foreach ($testimonials as $t)
                    @php
                        $vid = trim((string) ($t->video ?? ''));
                        $embed = $vid === '' ? null : (\Illuminate\Support\Str::startsWith($vid, ['http://', 'https://'])
                            ? $vid
                            : 'https://www.youtube.com/embed/'.$vid);
                    @endphp
                    <article class="mkt__testimonial">
                        <div class="mkt__testimonial-icon"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                        <h3>{{ $t->title }}</h3>
                        @if ($embed)
                            <button type="button" class="btn btn-secondary" data-video="{{ $embed }}">Watch the video</button>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ===================== Client logos ===================== --}}
    <section class="mkt__section mkt__section--alt">
        <div class="mkt__container">
            <div class="mkt__section-head">
                <span class="mkt__eyebrow">Trusted by</span>
                <h2>Organisations using ScanLink</h2>
            </div>
            @php
                $clientLogos = [
                    'iga.png', 'cellarbrations.png', 'dare-to-be-great.png', 'the-bottle.png', 'ergon-energy.png',
                    'raine-and-horne.png', 'victoria-university.png', 'fm-fleetmark.png', 'first-nationl-real-estate.png',
                    'scan-media.png', 'lj-hooker.png', 'super-coal-limited.png', 'bawbaw-shire-council.png',
                    'melbourne-water.png', 'ray-white.png', 'st-john.png', 'adelaide-hills-council.png',
                    'centra-wealth.png', 'ancare.png', 'mr-fothergills.png', 'angus-member.png', 'new-government.png',
                    'queensland.png', 'kyneton-ridge-estate.png', 'red-roo.png', 'bloriz.png', 'kanga_logo.png',
                    'Hellenic_Museum.jpg', 'BD_Newcastle.png', 'hilton.jpg',
                ];
            @endphp
            <div class="mkt__logos">
                @foreach ($clientLogos as $logo)
                    <div class="mkt__logo"><img src="{{ asset('images/client_logo/'.$logo) }}" alt="" loading="lazy"></div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== CTA band ===================== --}}
    <section class="mkt__section mkt__section--tight">
        <div class="mkt__container">
            <div class="mkt__cta-band">
                <h2>A powerful QR &amp; mobile engagement platform for businesses of all sizes</h2>
                <p>Try it now &mdash; your first code is free for a year.</p>
                <div class="mkt__hero-cta">
                    <a class="btn btn-on-dark btn-lg" href="{{ $register }}">Try it free for a year</a>
                    <a class="btn btn-outline-dark btn-lg" href="{{ route('marketing.pricing') }}">View pricing</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== Tips video ===================== --}}
    <section class="mkt__section mkt__section--alt">
        <div class="mkt__container">
            <div class="mkt__media mkt__media--reverse">
                <div class="mkt__media-text">
                    <span class="mkt__eyebrow">Get the most out of it</span>
                    <h2>5 tips to help make your QR code initiative a success</h2>
                    <p>Practical advice for placement, calls to action and measuring results.</p>
                    <div class="mkt__hero-cta">
                        <button type="button" class="btn btn-primary" data-video="https://www.youtube.com/embed/5c8EnTAMEl4?rel=0&autoplay=1">Watch the video</button>
                    </div>
                </div>
                <div class="mkt__video">
                    <iframe src="https://www.youtube.com/embed/5c8EnTAMEl4?rel=0" title="5 tips for QR code success" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>
</main>

{{-- ===================== Footer ===================== --}}
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
                    <li><a href="#main">What is ScanLink?</a></li>
                    <li><a href="{{ route('marketing.pricing') }}">Pricing</a></li>
                    <li><a href="{{ route('marketing.faq') }}">FAQ</a></li>
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

{{-- ===================== Login modal ===================== --}}
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

@include('marketing.partials.password-expired-modal')

{{-- ===================== Video modal ===================== --}}
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

        // Mobile nav
        var burger = document.querySelector('[data-toggle-nav]');
        var mobileNav = document.getElementById('mkt-mobile-nav');
        if (burger && mobileNav) {
            burger.addEventListener('click', function () {
                var open = mobileNav.getAttribute('data-open') === 'true';
                mobileNav.setAttribute('data-open', open ? 'false' : 'true');
                burger.setAttribute('aria-expanded', open ? 'false' : 'true');
            });
        }

        // Login modal
        document.querySelectorAll('[data-open-login]').forEach(function (b) {
            b.addEventListener('click', function () {
                if (mobileNav) { mobileNav.setAttribute('data-open', 'false'); }
                openOverlay(login);
                var email = document.getElementById('email');
                if (email) { setTimeout(function () { email.focus(); }, 50); }
            });
        });

        // Video modal (buttons/links with data-video)
        document.querySelectorAll('[data-video]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var url = el.getAttribute('data-video');
                if (!url) { return; }
                videoFrame.innerHTML = '<iframe src="' + url + '" title="Video" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                openOverlay(video);
            });
        });

        // Close handlers
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

        // Deep-link + validation error open login
        if (window.location.hash === '#login' || (login && login.getAttribute('data-open'))) {
            openOverlay(login);
        }
    })();
</script>

{{-- ===================== Express Code Generator ===================== --}}
<script>
    (function () {
        var urlInput = document.getElementById('txt_url');
        var previewImg = document.getElementById('code_preview_img');
        var previewFlag = document.getElementById('code_preview_flag');
        var downloadSel = document.getElementById('download_option');
        var downloadBtn = document.getElementById('ecg_download');
        if (!urlInput || !previewImg || !downloadBtn) { return; }

        var sampleSrc = previewImg.getAttribute('src');
        var previewUrl = @json(route('marketing.express.preview'));
        var downloadUrl = @json(route('marketing.express.download'));
        var csrf = @json(csrf_token());
        var request; // in-flight fetch (aborted on new input)

        function codeType() {
            var checked = document.querySelector('.type_of_code:checked');
            return checked ? checked.value : '0';
        }

        function resetPreview() {
            previewImg.setAttribute('src', sampleSrc);
            if (previewFlag) { previewFlag.value = '0'; }
        }

        function refreshPreview() {
            var url = (urlInput.value || '').trim();
            var stripped = url.replace(/^https?:\/\//i, '').replace(/^www\./i, '');
            if (stripped.length <= 3) { resetPreview(); return; }

            if (request && request.abort) { request.abort(); }
            var controller = new AbortController();
            request = controller;

            var body = 'url=' + encodeURIComponent(url) + '&code_type=' + encodeURIComponent(codeType());
            fetch(previewUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: body,
                signal: controller.signal
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (data && data.ok && data.image) {
                    previewImg.setAttribute('src', data.image);
                    if (previewFlag) { previewFlag.value = '1'; }
                } else {
                    resetPreview();
                }
            }).catch(function () { /* aborted or failed — keep current */ });
        }

        var debounce;
        urlInput.addEventListener('keyup', function () {
            clearTimeout(debounce);
            debounce = setTimeout(refreshPreview, 350);
        });
        urlInput.addEventListener('change', refreshPreview);
        document.querySelectorAll('.type_of_code').forEach(function (radio) {
            radio.addEventListener('change', refreshPreview);
        });

        downloadBtn.addEventListener('click', function () {
            if (!previewFlag || previewFlag.value === '0') {
                window.slAlert('Please enter valid URL');
                urlInput.focus();
                return;
            }
            var fmt = downloadSel ? downloadSel.value : '';
            if (fmt === '') {
                window.slAlert('Please select download as');
                if (downloadSel) { downloadSel.focus(); }
                return;
            }
            var qs = 'url=' + encodeURIComponent((urlInput.value || '').trim())
                + '&code_type=' + encodeURIComponent(codeType())
                + '&download_as=' + encodeURIComponent(fmt);
            window.location.href = downloadUrl + '?' + qs;
        });
    })();
</script>
@include('filament.hooks.themed-dialog')
</body>
</html>
