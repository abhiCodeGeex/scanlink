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
            {{-- With reCAPTCHA on, the submit is intercepted so a token can be fetched and handed
                 to submit() directly; without it, Livewire handles the submit as before. --}}
            <form @if (! $this->recaptcha()->enabled()) wire:submit.prevent="submit" @endif class="sl-portal-contact__form" id="frmcontact">
                <ul class="sl-portal-contact__fields">
                    <li>
                        <label for="yourName">Name:<span class="required" aria-hidden="true">*</span></label>
                        <input
                            type="text"
                            wire:model="name"
                            id="yourName"
                            class="sl-portal-contact__input"
                            required
                        />
                    </li>
                    <li>
                        <label for="contactEmail">Email:<span class="required" aria-hidden="true">*</span></label>
                        <input
                            type="text"
                            wire:model="email"
                            id="contactEmail"
                            class="sl-portal-contact__input"
                            required
                        />
                    </li>
                    <li>
                        <label for="commentsText">Message:<span class="required" aria-hidden="true">*</span></label>
                        <textarea
                            wire:model="comments"
                            id="commentsText"
                            class="sl-portal-contact__input sl-portal-contact__textarea"
                            rows="3"
                            required
                        ></textarea>
                    </li>
                    @if ($this->recaptcha()->enabled())
                        <li>
                            <span class="sl-portal-contact__recaptcha-note" style="font-size:12px;color:#6b7280;">
                                Protected by reCAPTCHA &mdash;
                                <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privacy</a> and
                                <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms</a> apply.
                            </span>
                        </li>
                    @else
                        <li>
                            <label>Verification Code:<span class="required" aria-hidden="true">*</span></label>
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
                                required
                            />
                        </li>
                    @endif
                    <li>
                        <button type="submit" class="sl-portal-contact__send" wire:loading.attr="disabled">
                            SEND
                        </button>
                    </li>
                </ul>
            </form>

            @if ($this->recaptcha()->enabled())
                @include('partials.recaptcha-v3', [
                    'recaptcha' => $this->recaptcha(),
                    'formId' => 'frmcontact',
                    'livewireId' => $this->getId(),
                    'method' => 'submit',
                ])
            @endif

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
