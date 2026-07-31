<x-filament-panels::page>
    <style>
        /* Purchase Form Builder — clean, professional billing card. */
        .fi-page-header { display: none !important; }

        .sl-fbp { max-width: 880px; margin: 0 auto; width: 100%; }

        .sl-fbp__card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            padding: 28px 32px 30px;
            box-sizing: border-box;
        }

        .sl-fbp__head { margin-bottom: 6px; }
        .sl-fbp__title { font-size: 22px; font-weight: 700; color: #111827; margin: 0; line-height: 1.2; }
        .sl-fbp__subtitle { margin: 6px 0 0; font-size: 14px; color: #6b7280; }

        .sl-fbp__section {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #008C00;
            margin: 26px 0 14px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eef0f2;
        }

        .sl-fbp__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 22px;
        }
        .sl-fbp__field { display: flex; flex-direction: column; min-width: 0; }
        .sl-fbp__field--full { grid-column: 1 / -1; }

        .sl-fbp__label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        .sl-fbp__req { color: #dc2626; margin-left: 2px; }
        .sl-fbp__hint { font-weight: 400; color: #9ca3af; font-size: 12px; }

        .sl-fbp__control {
            width: 100%;
            height: 44px;
            padding: 0 13px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            background: #ffffff;
            color: #111827;
            font-size: 14px;
            line-height: 1.2;
            box-sizing: border-box;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .sl-fbp__control::placeholder { color: #9ca3af; }
        .sl-fbp__control:hover { border-color: #b6bcc4; }
        .sl-fbp__control:focus {
            outline: none;
            border-color: #008C00;
            box-shadow: 0 0 0 3px rgba(0, 140, 0, 0.15);
        }

        select.sl-fbp__control {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 38px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 18px;
            cursor: pointer;
        }

        .sl-fbp__actions { margin-top: 30px; display: flex; justify-content: flex-end; gap: 12px; }
        .sl-fbp__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 46px;
            padding: 0 30px;
            background: #008C00;
            color: #ffffff;
            border: 0;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.05s ease;
            box-shadow: 0 4px 12px rgba(0, 140, 0, 0.25);
        }
        .sl-fbp__btn:hover { background: #00a300; }
        .sl-fbp__btn:active { transform: translateY(1px); }
        .sl-fbp__btn:disabled { opacity: 0.6; cursor: not-allowed; box-shadow: none; }

        /* Dark mode */
        .dark .sl-fbp__card { background: #1f2937; border-color: #374151; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35); }
        .dark .sl-fbp__title { color: #f9fafb; }
        .dark .sl-fbp__subtitle { color: #9ca3af; }
        .dark .sl-fbp__section { border-color: #374151; }
        .dark .sl-fbp__label { color: #d1d5db; }
        .dark .sl-fbp__control { background: #111827; border-color: #4b5563; color: #f3f4f6; }
        .dark .sl-fbp__control:hover { border-color: #6b7280; }

        @media (max-width: 640px) {
            .sl-fbp__card { padding: 22px 18px 24px; border-radius: 12px; }
            .sl-fbp__grid { grid-template-columns: 1fr; gap: 14px; }
            .sl-fbp__actions { justify-content: stretch; }
            .sl-fbp__btn { width: 100%; }
        }
    </style>

    <div class="sl-fbp">
        <div class="sl-fbp__card">
            <header class="sl-fbp__head">
                <h1 class="sl-fbp__title">Purchase Form Builder</h1>
                <p class="sl-fbp__subtitle">Activate Form Builder on one of your code profiles.</p>
            </header>

            <div class="sl-fbp__section">Profile</div>
            <div class="sl-fbp__field">
                <label class="sl-fbp__label" for="fbp-profile">Select a profile<span class="sl-fbp__req">*</span></label>
                <select id="fbp-profile" class="sl-fbp__control" wire:model="profileId">
                    <option value="">Select a profile…</option>
                    @foreach ($this->profileOptions() as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sl-fbp__section">Billing details</div>
            <div class="sl-fbp__grid">
                <div class="sl-fbp__field">
                    <label class="sl-fbp__label" for="fbp-first">First name<span class="sl-fbp__req">*</span></label>
                    <input id="fbp-first" type="text" class="sl-fbp__control" wire:model.defer="firstName" autocomplete="given-name">
                </div>
                <div class="sl-fbp__field">
                    <label class="sl-fbp__label" for="fbp-last">Last name<span class="sl-fbp__req">*</span></label>
                    <input id="fbp-last" type="text" class="sl-fbp__control" wire:model.defer="lastName" autocomplete="family-name">
                </div>

                <div class="sl-fbp__field">
                    <label class="sl-fbp__label" for="fbp-company">Company name<span class="sl-fbp__req">*</span></label>
                    <input id="fbp-company" type="text" class="sl-fbp__control" wire:model.defer="companyName" autocomplete="organization">
                </div>
                <div class="sl-fbp__field">
                    <label class="sl-fbp__label" for="fbp-address">Billing address<span class="sl-fbp__req">*</span></label>
                    <input id="fbp-address" type="text" class="sl-fbp__control" wire:model.defer="billingAddress" autocomplete="street-address">
                </div>

                <div class="sl-fbp__field">
                    <label class="sl-fbp__label" for="fbp-email">Email <span class="sl-fbp__hint">(this will also be your username)</span><span class="sl-fbp__req">*</span></label>
                    <input id="fbp-email" type="email" class="sl-fbp__control" wire:model.defer="email" autocomplete="email">
                </div>
                <div class="sl-fbp__field">
                    <label class="sl-fbp__label" for="fbp-town">Town<span class="sl-fbp__req">*</span></label>
                    <input id="fbp-town" type="text" class="sl-fbp__control" wire:model.defer="town" autocomplete="address-level2">
                </div>

                <div class="sl-fbp__field">
                    <label class="sl-fbp__label" for="fbp-phone">Telephone number<span class="sl-fbp__req">*</span></label>
                    <input id="fbp-phone" type="text" inputmode="numeric" onkeypress="var c=event.which||event.keyCode; return !(c>31 && (c<48||c>57));" class="sl-fbp__control" wire:model.defer="phone" autocomplete="tel">
                </div>
                <div class="sl-fbp__field">
                    <label class="sl-fbp__label" for="fbp-postal">Postal code<span class="sl-fbp__req">*</span></label>
                    <input id="fbp-postal" type="text" inputmode="numeric" onkeypress="var c=event.which||event.keyCode; return !(c>31 && (c<48||c>57));" class="sl-fbp__control" wire:model.defer="postalCode" autocomplete="postal-code">
                </div>
            </div>

            <div class="sl-fbp__actions">
                <button type="button" class="sl-fbp__btn" wire:click="next" wire:loading.attr="disabled">
                    Next
                </button>
            </div>
        </div>
    </div>
</x-filament-panels::page>
