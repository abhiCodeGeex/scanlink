{{-- Pixel match: https://scanlink.com.au/register/index — CSS grid (floats break inside Filament). --}}
<style>
    /* Force Filament auth shell wide enough for 3×310 breadcrumbs + 2 form cols */
    .fi-simple-layout:has(.sl-reg) .fi-simple-main,
    .fi-simple-main:has(.sl-reg),
    .fi-simple-main-ctn:has(.sl-reg) {
        max-width: 980px !important;
        width: 100% !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
    }

    .sl-reg {
        box-sizing: border-box;
        width: 100%;
        max-width: 920px;
        margin: 0 auto;
        padding: 0 0 28px;
        color: #333;
        font-family: Arial, Helvetica, sans-serif;
        text-align: left;
        overflow-x: hidden;
    }

    .sl-reg *,
    .sl-reg *::before,
    .sl-reg *::after {
        box-sizing: border-box;
    }

    /* —— Step bar (legacy breadcrumbsMain look via flex, not float) —— */
    .sl-reg .breadcrumbsMain {
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        width: 100%;
        margin: 0 0 28px;
        padding: 0;
        list-style: none;
        font-family: Arial, Helvetica, sans-serif !important;
        overflow: visible;
    }

    .sl-reg .breadcrumbsMain li {
        position: relative;
        flex: 1 1 0;
        min-width: 0;
        max-width: 310px;
        height: 113px;
        margin: 0;
        padding: 0;
        background-color: #c9c9c9;
        color: #fff;
        list-style: none;
        float: none !important;
    }

    .sl-reg .breadcrumbsMain li span {
        float: left;
        width: 92px;
        height: 113px;
        margin-right: 12px;
        background: url("{{ asset('images/breadcrumb-no-bg.png') }}") no-repeat top left;
        text-align: center;
        line-height: 113px;
        color: #c3c3c3;
        font-size: 72px;
        font-weight: bold;
    }

    .sl-reg .breadcrumbsMain li h4 {
        color: #fff;
        font-size: 28px;
        margin: 14px 0 0;
        font-weight: bold;
        line-height: 1.1;
    }

    .sl-reg .breadcrumbsMain li p {
        color: #fff;
        font-size: 15px;
        margin: 0;
        line-height: 1.2;
        padding-right: 18px;
    }

    .sl-reg .breadcrumbsMain li > label {
        position: absolute;
        top: 0;
        right: -23px;
        z-index: 5;
        width: 26px;
        height: 113px;
        margin: 0;
        padding: 0;
        font-size: 0;
        background: url("{{ asset('images/breadcrumb-dis-arw.png') }}") no-repeat top left;
        float: none;
    }

    .sl-reg .breadcrumbsMain li.current {
        background-color: #7fc07f !important;
    }

    .sl-reg .breadcrumbsMain li.current span {
        color: #686868 !important;
    }

    .sl-reg .breadcrumbsMain li.current > label {
        background-image: url("{{ asset('images/breadcrumb-en-arw.png') }}");
    }

    .sl-reg .txtI-10 {
        text-indent: 10px;
    }

    /* —— Form: intro row + two independent stacks (legacy layout) —— */
    .sl-reg #frmregister {
        display: block;
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0;
        border: 0;
    }

    .sl-reg .sl-reg-top {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px 24px;
        align-items: start;
        margin: 0 0 18px;
        width: 100%;
    }

    .sl-reg .sl-reg-intro-title {
        color: #006201;
        font-size: 20px;
        line-height: 1.35;
        margin: 0;
        padding: 0;
    }

    .sl-reg .sl-reg-reseller-wrap {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 8px 10px;
        margin: 0;
        justify-self: end;
    }

    .sl-reg .sl-reg-reseller-label {
        font-size: 13px;
        color: #555;
        line-height: 1.25;
        white-space: nowrap;
    }

    .sl-reg .sl-reg-reseller-label small {
        display: block;
        color: #777;
        white-space: nowrap;
    }

    .sl-reg .reg-reseller-code {
        width: 110px;
        height: 25px;
        flex: 0 0 110px;
        border-radius: 6px;
        border: 1px solid #cccccc;
        padding: 0 6px;
        font-size: 13px;
        outline: none;
        background: #fff;
        margin: 0;
    }

    .sl-reg .sl-reg-cols {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        column-gap: 40px;
        align-items: start;
        width: 100%;
    }

    .sl-reg .sl-reg-col {
        display: flex;
        flex-direction: column;
        gap: 12px;
        min-width: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .sl-reg .sl-reg-field {
        display: block;
        width: 100%;
        min-width: 0;
        margin: 0;
        padding: 0;
    }

    .sl-reg .sl-reg-field > label:not(.error) {
        display: block;
        font-weight: normal;
        color: #727171;
        font-family: Verdana, Arial, sans-serif;
        font-size: 13px;
        line-height: 1.3;
        margin: 0 0 4px;
        padding: 0;
    }

    .sl-reg .sl-reg-field .sl-reg-hint {
        font-size: 12px;
        color: #727171;
    }

    .sl-reg .sl-reg-field .text-fi,
    .sl-reg .text-fi {
        display: block;
        width: 100% !important;
        max-width: 100% !important;
        height: 32px;
        line-height: 32px;
        padding: 5px 10px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #e5e5e5;
        box-shadow: inset 3px 3px 5px 0 rgba(0, 0, 0, .1);
        outline: 0;
        background: #fff;
        color: #222;
    }

    .sl-reg .sl-reg-field label.error,
    .sl-reg .error {
        display: block;
        color: #c00;
        font-size: 12px;
        font-weight: normal;
        margin: 2px 0 0;
        padding: 0;
    }

    .sl-reg .sl-reg-captcha-img {
        display: block;
        width: 150px;
        height: 50px;
        margin: 4px 0 6px;
        border: 1px solid #e5e5e5;
    }

    .sl-reg .rgister-div-submit {
        display: flex;
        justify-content: flex-end;
        width: 100%;
        margin: 24px 0 0;
        padding: 0;
    }

    .sl-reg .rgister-div-submit .save,
    .sl-reg button.save {
        display: inline-block;
        width: 150px;
        height: 42px;
        line-height: 40px;
        padding: 0 15px;
        border: 1px solid #006201;
        border-radius: 6px;
        background: linear-gradient(to bottom, #008901 0%, #007a01 100%);
        color: #fff;
        text-transform: uppercase;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
        text-align: center;
    }

    .sl-reg button.save:disabled {
        opacity: 0.65;
        cursor: wait;
    }

    .sl-reg-error-banner {
        margin: 0 0 12px;
        padding: 8px 10px;
        border: 1px solid #e0b4b4;
        background: #fff5f5;
        color: #a40000;
        font-size: 13px;
    }

    /* —— Step 2 —— */
    .sl-reg h3.page-title {
        color: #006201;
        font-size: 18px;
        font-weight: normal;
        margin: 10px 0 20px;
        line-height: 1.35;
    }

    .sl-reg .SecondPurchaseBox {
        margin-top: 12px;
        max-width: 640px;
    }

    .sl-reg .GreenTitlePurchaseCodes {
        background: #008401;
        font-size: 16px;
        color: #fff;
        text-align: center;
        padding: 0 10px;
        line-height: 30px;
        width: 140px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .sl-reg .content-register {
        border: 1px solid #d2d2d2;
        border-radius: 10px;
        border-top-left-radius: 0;
        background: #fff;
        overflow: hidden;
    }

    .sl-reg .content-main-register {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 16px 20px;
        align-items: center;
        margin: 25px;
    }

    .sl-reg .content-dispaly-amount-register {
        grid-column: 2;
        grid-row: 1 / span 2;
        width: 220px;
        text-align: left;
    }

    .sl-reg .code-amount {
        font-size: 35px;
        color: #444;
        line-height: 1.1;
    }

    .sl-reg .code-amount > sup {
        font-size: 20px;
    }

    .sl-reg .code-permonth {
        font-size: 16px;
        color: #555;
        margin-top: 4px;
    }

    .sl-reg .code-total {
        margin-top: 8px;
        font-size: 14px;
        color: #555;
    }

    .sl-reg .content-label-register {
        font-size: 14px;
        color: #444;
        grid-column: 1;
    }

    .sl-reg .content-textbox-register input {
        height: 28px;
        width: 200px;
        max-width: 100%;
        padding: 0 8px;
        border-radius: 6px;
        border: 1px solid #e5e5e5;
        box-shadow: inset 3px 3px 5px 0 rgba(0, 0, 0, .1);
    }

    .sl-reg .content-button-register button,
    .sl-reg .secondstep-submit button {
        height: 42px;
        padding: 0 18px;
        border: 1px solid #006201;
        border-radius: 6px;
        background: linear-gradient(to bottom, #008901 0%, #007a01 100%);
        color: #fff;
        text-transform: uppercase;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
    }

    .sl-reg .content-actions-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        grid-column: 1;
    }

    .sl-reg .secondstep-submit {
        display: flex;
        justify-content: flex-end;
        margin: 8px 25px 16px;
    }

    .sl-reg .sl-reg-free-note {
        margin: 0 25px 20px;
        font-size: 13px;
        color: #006201;
    }

    .sl-reg-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 80;
        background: repeating-linear-gradient(
            -45deg,
            rgba(0, 0, 0, 0.72),
            rgba(0, 0, 0, 0.72) 8px,
            rgba(20, 20, 20, 0.78) 8px,
            rgba(20, 20, 20, 0.78) 16px
        );
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .sl-reg-modal {
        position: relative;
        width: min(560px, 100%);
        background: #fff;
        border: 1px solid #444;
        border-radius: 8px;
        padding: 48px 28px 56px;
        text-align: center;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35);
    }

    .sl-reg-modal-title {
        display: block;
        color: #008901;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 18px;
    }

    .sl-reg-modal p {
        margin: 0;
        font-size: 15px;
        line-height: 1.5;
        color: #222;
    }

    .sl-reg-modal-close {
        position: absolute;
        right: 10px;
        bottom: 10px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid #666;
        background: #f3f3f3;
        color: #333;
        font-size: 16px;
        line-height: 26px;
        cursor: pointer;
        padding: 0;
    }

    @media (max-width: 860px) {
        .sl-reg .breadcrumbsMain {
            flex-direction: column;
        }

        .sl-reg .breadcrumbsMain li {
            max-width: none;
        }

        .sl-reg .breadcrumbsMain li > label {
            display: none;
        }

        .sl-reg .sl-reg-top {
            grid-template-columns: 1fr;
        }

        .sl-reg .sl-reg-reseller-wrap {
            justify-self: start;
        }

        .sl-reg .sl-reg-cols {
            grid-template-columns: 1fr;
        }

        .sl-reg .rgister-div-submit {
            justify-content: flex-start;
        }

        .sl-reg .content-main-register {
            grid-template-columns: 1fr;
        }

        .sl-reg .content-dispaly-amount-register {
            grid-column: 1;
            grid-row: auto;
        }
    }
</style>

<div class="sl-reg" wire:key="legacy-register-v3-{{ $this->wizardStep }}">
    <ul class="breadcrumbsMain" aria-label="Registration steps">
        <li @class(['current' => $this->wizardStep === 1])>
            <span>1</span>
            <h4>Enter</h4>
            <p>your registration<br>details</p>
            <label>&nbsp;</label>
        </li>
        <li @class(['current' => $this->wizardStep === 2])>
            <span class="txtI-10">2</span>
            <h4>Select</h4>
            <p>the quantity of codes<br>your require</p>
            <label>&nbsp;</label>
        </li>
        <li>
            <span class="txtI-10">3</span>
            <h4>Upload</h4>
            <p>web links, videos,<br>pictures, documents</p>
            <label>&nbsp;</label>
        </li>
    </ul>

    @if ($errors->any())
        <div class="sl-reg-error-banner" role="alert">
            Please correct the highlighted fields before continuing.
        </div>
    @endif

    @if ($this->wizardStep === 1)
        <form wire:submit.prevent="register" id="frmregister">
            <div class="sl-reg-top">
                <div class="sl-reg-intro-title">
                    Account registration includes your first Scanlink code is free for a year
                </div>
                <div class="sl-reg-reseller-wrap">
                    <div class="sl-reg-reseller-label">
                        Reseller Code
                        <small>(For Resellers Only)</small>
                    </div>
                    <input
                        type="text"
                        name="client_reseller_code"
                        id="client_reseller_code"
                        class="reg-reseller-code"
                        maxlength="255"
                        wire:model="data.client_reseller_code"
                    >
                    @error('data.client_reseller_code')
                        <label class="error" generated="true">{{ $message }}</label>
                    @enderror
                </div>
            </div>

            <div class="sl-reg-cols">
                <div class="sl-reg-col sl-reg-col-left">
                    <div class="sl-reg-field">
                        <label for="first_name">First name:</label>
                        <input type="text" id="first_name" class="text-fi" tabindex="1" wire:model="data.first_name" autofocus>
                        @error('data.first_name') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="company_name">Company name/Business name:</label>
                        <input type="text" id="company_name" class="text-fi" tabindex="3" wire:model="data.company_name">
                        @error('data.company_name') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="email">
                            Email:
                            <span class="sl-reg-hint">(This will also be your username)</span>
                        </label>
                        <input type="email" id="email" class="text-fi" tabindex="5" wire:model="data.email" autocomplete="username">
                        @error('data.email') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="phone">Telephone number:</label>
                        <input type="text" id="phone" class="text-fi" tabindex="7" wire:model="data.phone" inputmode="numeric">
                        @error('data.phone') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="password">Password:</label>
                        <input type="password" id="password" class="text-fi" tabindex="9" wire:model="data.password" autocomplete="new-password">
                        @error('data.password') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="captcha">Verification code:</label>
                        <img
                            src="{{ route('marketing.captcha') }}?t={{ $this->captchaNonce }}"
                            alt="Captcha"
                            class="sl-reg-captcha-img"
                            width="150"
                            height="50"
                            wire:key="register-captcha-{{ $this->captchaNonce }}"
                        >
                        <input type="text" id="captcha" class="text-fi" tabindex="11" wire:model="data.captcha" autocomplete="off">
                        @error('data.captcha') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                </div>

                <div class="sl-reg-col sl-reg-col-right">
                    <div class="sl-reg-field">
                        <label for="last_name">Last name:</label>
                        <input type="text" id="last_name" class="text-fi" tabindex="2" wire:model="data.last_name">
                        @error('data.last_name') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="billing_address">Address:</label>
                        <input type="text" id="billing_address" class="text-fi" tabindex="4" wire:model="data.billing_address">
                        @error('data.billing_address') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="town">Town:</label>
                        <input type="text" id="town" class="text-fi" tabindex="6" wire:model="data.town">
                        @error('data.town') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="postal_code">Postal code:</label>
                        <input type="text" id="postal_code" class="text-fi" tabindex="8" wire:model="data.postal_code" inputmode="numeric">
                        @error('data.postal_code') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <label for="cpassword">Confirm Password:</label>
                        <input type="password" id="cpassword" class="text-fi" tabindex="10" wire:model="data.cpassword" autocomplete="new-password">
                        @error('data.cpassword') <label class="error" generated="true">{{ $message }}</label> @enderror
                    </div>
                    <div class="sl-reg-field">
                        <div class="rgister-div-submit">
                            <button type="submit" id="save" class="save" wire:loading.attr="disabled">NEXT</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <h3 class="page-title">
            Enter the amount of codes you would like to activate and click the calculate button to display the total order value
        </h3>

        <div class="SecondPurchaseBox">
            <div class="GreenTitlePurchaseCodes">Purchase Codes</div>
            <div class="content-register">
                <div class="content-main-register">
                    <div class="content-label-register">
                        <label for="no_codes">Enter a number of codes required:</label>
                    </div>
                    <div class="content-dispaly-amount-register">
                        <div class="code-amount"><sup>$</sup><span>{{ $this->perCodeAmount }}</span></div>
                        <div class="code-permonth">Per Code / Month</div>
                        <div class="code-total">
                            Total Annual subscription <b>${{ $this->subscriptionAmount }}</b>
                        </div>
                    </div>
                    <div class="content-actions-row">
                        <div class="content-textbox-register">
                            <input
                                type="text"
                                id="no_codes"
                                class="text-fi"
                                maxlength="4"
                                inputmode="numeric"
                                wire:model="data.no_codes"
                            >
                            @error('data.no_codes') <label class="error" generated="true">{{ $message }}</label> @enderror
                        </div>
                        <div class="content-button-register">
                            <button type="button" id="calculate" wire:click="calculateCodes" wire:loading.attr="disabled">
                                Calculate
                            </button>
                        </div>
                    </div>
                </div>
                <div class="secondstep-submit">
                    <button type="button" id="order-summary" class="save" wire:click="register" wire:loading.attr="disabled">
                        NEXT
                    </button>
                </div>
                <p class="sl-reg-free-note">
                    Account registration includes your first ScanLink code free for a year.
                </p>
            </div>
        </div>
    @endif
</div>

@if ($this->showNearlyDoneModal)
    <div class="sl-reg-modal-backdrop" wire:key="nearly-done-modal">
        <div class="sl-reg-modal" role="dialog" aria-modal="true" aria-labelledby="sl-reg-nearly-done-title">
            <span id="sl-reg-nearly-done-title" class="sl-reg-modal-title">We're nearly done...</span>
            <p>
                We've just sent you an email with a confirmation link.<br>
                Please click on the link to complete your account registration.
            </p>
            <button
                type="button"
                class="sl-reg-modal-close"
                wire:click="dismissNearlyDone"
                aria-label="Close and continue to portal"
                title="Continue to portal"
            >
                ×
            </button>
        </div>
    </div>
@endif
