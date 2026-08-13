@extends('marketing.mkt-page')

@section('title', 'Pricing — ScanLink')
@section('meta_description', 'Scalable, affordable QR code and content management pricing. Your first ScanLink code is free for a year.')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Pricing</span>
            <h1>Your first ScanLink code is free</h1>
            <p>Scalable, affordable access to QR code and content management for any size business. Activate as few or as many codes as you need &mdash; each is a 12-month subscription you can renew indefinitely.</p>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__calc">
                <div class="mkt__calc-amount"><sup>$</sup><span id="code_amount">0.00</span></div>
                <div class="mkt__calc-permonth">Per code / month</div>
                <div class="mkt__calc-total">Total annual subscription <b>$<span id="subscription_amount">0.00</span></b></div>
                <div class="mkt__calc-form">
                    <label for="no_codes">Enter the number of codes required</label>
                    <div class="mkt__calc-input">
                        <input type="text" id="no_codes" name="no_codes" maxlength="4" inputmode="numeric" placeholder="e.g. 10" autocomplete="off">
                        <button type="button" id="calculate" class="btn btn-on-dark">Calculate</button>
                    </div>
                    <span class="mkt__calc-error" id="calc_error" role="alert"></span>
                </div>
            </div>

            <div class="mkt__section-head" style="margin-top:48px;">
                <p style="color:var(--body);">Ready to get started? Register your ScanLink account in three easy steps &mdash; your first code is free for a year.</p>
                @unless ($isPortalUser ?? false)
                    <div class="mkt__hero-cta" style="justify-content:center;margin-top:16px;">
                        <a class="btn btn-primary btn-lg" href="{{ $register }}">Register free</a>
                    </div>
                @endunless
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--alt">
        <div class="mkt__container">
            <div class="mkt__grid-3">
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></div>
                    <h3>Only pay for what you use</h3>
                    <p>Access is an annual subscription per code, from just $4 per month. Add new codes to your account anytime.</p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg></div>
                    <h3>Industrial labels &amp; tags from $3</h3>
                    <p>High-quality industrial labels and tags to deploy your mobile initiative &mdash; order any quantity from your account, including customised options.</p>
                </article>
                <article class="mkt__feature">
                    <div class="mkt__feature-icon"><svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="#008c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m23 7-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
                    <h3>Mobile video production</h3>
                    <p>Affordable mobile-optimised video and animation production to suit your budget. <a href="{{ route('marketing.mobileVideo') }}">Learn more</a> or <a href="{{ route('marketing.contact') }}">contact us</a> to discuss your requirements.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="mkt__section mkt__section--tight">
        <div class="mkt__container">
            <div class="mkt__cta-band">
                <h2>Start your mobile initiative today</h2>
                <p>Your first code is free for a year.</p>
                <div class="mkt__hero-cta">
                    <a class="btn btn-on-dark btn-lg" href="{{ $register }}">Register free</a>
                    <a class="btn btn-outline-dark btn-lg" href="{{ route('marketing.contact') }}">Enquire</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('no_codes');
        var btn = document.getElementById('calculate');
        var err = document.getElementById('calc_error');
        var amount = document.getElementById('code_amount');
        var sub = document.getElementById('subscription_amount');
        if (!btn) { return; }
        input.addEventListener('keypress', function (e) {
            if (e.key && e.key.length === 1 && !/[0-9]/.test(e.key)) { e.preventDefault(); }
        });
        function calc() {
            err.textContent = '';
            var qty = (input.value || '').trim();
            if (qty === '') { err.textContent = 'Enter a number of codes required.'; input.focus(); return; }
            if (parseInt(qty, 10) > 1000) { err.textContent = 'Enter a number of codes less than 1000.'; input.focus(); return; }
            fetch(@json(route('marketing.pricing.calculate')), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': @json(csrf_token()), 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                body: 'no_codes=' + encodeURIComponent(qty)
            }).then(function (r) { return r.json(); }).then(function (data) {
                var errors = data.errors || {};
                var keys = Object.keys(errors);
                if (keys.length) { err.textContent = errors[keys[0]]; return; }
                amount.textContent = data.amount;
                sub.textContent = data.totalsubscrption;
            }).catch(function () { err.textContent = 'Request failed. Please try again.'; });
        }
        btn.addEventListener('click', calc);
        input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); calc(); } });
    })();
</script>
@endpush
