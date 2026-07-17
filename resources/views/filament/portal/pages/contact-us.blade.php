<x-filament-panels::page>
    <div class="sl-portal-contact">
        <h2 class="sl-portal-contact__title">Contact us</h2>

        @if ($formError)
            <div class="sl-portal-contact__flash">
                <label class="sl-portal-contact__error">{{ $formError }}</label>
            </div>
        @elseif ($submitted)
            <div class="sl-portal-contact__flash">
                <label class="sl-portal-contact__ok">Thanks — your enquiry has been sent.</label>
            </div>
        @endif

        <div class="sl-portal-contact__layout">
            <form wire:submit.prevent="submit" class="sl-portal-contact__form" id="frmcontact">
                <ul class="sl-portal-contact__fields">
                    <li>
                        <label for="yourName">Name:</label>
                        <input
                            type="text"
                            wire:model="name"
                            id="yourName"
                            class="sl-portal-contact__input"
                        />
                    </li>
                    <li>
                        <label for="contactEmail">Email:</label>
                        <input
                            type="text"
                            wire:model="email"
                            id="contactEmail"
                            class="sl-portal-contact__input"
                        />
                    </li>
                    <li>
                        <label for="commentsText">Message:</label>
                        <textarea
                            wire:model="comments"
                            id="commentsText"
                            class="sl-portal-contact__input sl-portal-contact__textarea"
                            rows="3"
                        ></textarea>
                    </li>
                    <li>
                        <label>Verification Code:</label>
                        <br/>
                        <img
                            src="{{ route('marketing.captcha') }}?t={{ $captchaNonce }}"
                            alt="Verification code"
                            class="sl-portal-contact__captcha"
                            width="150"
                            height="50"
                            wire:key="captcha-{{ $captchaNonce }}"
                        />
                        <input
                            type="text"
                            wire:model="captcha"
                            id="captcha"
                            class="sl-portal-contact__input"
                            autocomplete="off"
                        />
                    </li>
                    <li>
                        <button type="submit" class="sl-portal-contact__send" wire:loading.attr="disabled">
                            SEND
                        </button>
                    </li>
                </ul>
            </form>

            <section class="sl-portal-contact__box">
                <h2 class="sl-portal-contact__box-title">
                    <img src="{{ asset('images/img2.png') }}" alt="" />&nbsp;ScanLink
                </h2>
                <section class="sl-portal-contact__box-section">
                    Submit your enquiry with the 'contact us' form or
                    alternatively call us during business hours from Monday to Friday on...
                </section>
                <section class="sl-portal-contact__box-section">
                    <span class="sl-portal-contact__tel">+61 0364314025</span>
                </section>
                <section class="sl-portal-contact__box-section sl-portal-contact__box-section--last">
                    <span class="sl-portal-contact__address">
                        <strong>ScanLink</strong><br/>
                        5 Wattle Ave,<br/>
                        Emu Heights, Tasmania, 7320
                    </span>
                </section>
            </section>
        </div>
    </div>
</x-filament-panels::page>
