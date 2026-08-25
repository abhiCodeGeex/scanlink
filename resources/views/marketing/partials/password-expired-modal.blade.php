{{-- Shown after a login attempt by a migrated account whose legacy password expired
     (blank marker). PortalAuthController emails the reset link and flashes the email. --}}
@if (session('password_expired_email'))
    <div class="mkt__overlay" id="mkt-pw-expired" role="dialog" aria-modal="true" aria-labelledby="mkt-pw-expired-title" data-open="true">
        <div class="mkt__modal" style="max-width:460px;text-align:center;">
            <button type="button" class="mkt__modal-close" data-close aria-label="Close">&times;</button>

            <span aria-hidden="true" style="width:64px;height:64px;margin:4px auto 18px;border-radius:50%;background:#e7f6ec;display:flex;align-items:center;justify-content:center;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#008C00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </span>

            <h2 id="mkt-pw-expired-title" style="margin:0 0 10px;font-size:22px;line-height:1.25;">Check your email to reset your password</h2>

            <p style="margin:0 0 10px;color:#374151;font-size:15px;line-height:1.55;">
                For your security, your password was reset as part of ScanLink&rsquo;s recent platform upgrade.
                We&rsquo;ve emailed a secure reset link to
                <strong style="color:#111827;">{{ session('password_expired_email') }}</strong>.
            </p>
            <p style="margin:0 0 22px;color:#6b7280;font-size:13.5px;line-height:1.5;">
                Open the link in that email to choose a new password, then sign in as usual.
                Didn&rsquo;t receive it? Please check your spam folder.
            </p>

            <button type="button" class="btn btn-primary" data-close style="display:inline-block;min-width:160px;">Got it</button>
        </div>
    </div>
@endif
