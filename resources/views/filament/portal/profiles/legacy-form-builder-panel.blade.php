{{-- Shared Form Builder chrome (location right column / survey left column). --}}
@php
    $formBuilderHelpVideo = $formBuilderHelpVideo
        ?? 'https://www.youtube.com/embed/CfLEhcgvgrA?rel=0';
    $formBuilderPanelClass = $formBuilderPanelClass ?? '';
    $analyticsLocked = (bool) ($record?->enable_form_analytics ?? false);
    $formBuilderPurchased = (bool) ($formBuilderPurchased ?? $record?->form_active);
    $canPurchaseFormBuilder = (bool) ($canPurchaseFormBuilder ?? false);
    $canAccessFormBuilder = (bool) ($canAccessFormBuilder ?? true);
    $settingsLocked = ! $formBuilderPurchased;
@endphp

<style>
    .sl-form-builder-panel {
        background: #fff;
        border: 1px solid #cccccc;
        border-radius: 6px 6px 0 0;
        padding: 10px 12px;
        font-family: Arial, Helvetica, sans-serif;
        box-sizing: border-box;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, .1);
    }
    .sl-form-builder-panel * { box-sizing: border-box; }

    .sl-form-builder-panel .form-builder-title {
        display: flex;
        align-items: center;
        gap: 6px 10px;
        flex-wrap: wrap;
        font-size: 18px;
        font-weight: normal;
        color: #009401;
        padding: 0;
        margin: 0 0 6px;
        border-bottom: 0;
        line-height: 34px;
    }
    .sl-form-builder-panel .sl-help-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .sl-form-builder-panel .activate-for-profile {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }
    .sl-form-builder-panel .activate-for-profile span {
        display: inline-flex;
        align-items: center;
    }
    .sl-form-builder-panel .activate-for-profile input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 42px;
        height: 23px;
        border-radius: 23px;
        background: #cbd5e1;
        position: relative;
        cursor: pointer;
        transition: background 0.15s ease;
        margin: 0;
        flex: 0 0 auto;
    }
    .sl-form-builder-panel .activate-for-profile input[type="checkbox"]::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 19px;
        height: 19px;
        border-radius: 50%;
        background: #fff;
        transition: left 0.15s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
    .sl-form-builder-panel .activate-for-profile input[type="checkbox"]:checked { background: #008C00; }
    .sl-form-builder-panel .activate-for-profile input[type="checkbox"]:checked::after { left: 21px; }

    .sl-form-builder-panel .existing-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin: 0 0 6px;
        padding: 0;
        font-size: 13px;
        color: #374151;
        line-height: 1.35;
    }
    .sl-form-builder-panel .existing-item input[type="checkbox"],
    .sl-form-builder-panel .existing-item input[type="radio"] {
        width: 16px;
        height: 16px;
        accent-color: #008C00;
        cursor: pointer;
        margin: 1px 0 0;
        flex: 0 0 auto;
    }
    .sl-form-builder-panel .existing-item input:disabled {
        cursor: not-allowed;
        opacity: 0.55;
    }
    .sl-form-builder-panel .existing-item label {
        cursor: pointer;
        font-weight: 500;
        margin: 0;
    }
    .sl-form-builder-panel .existing-item small {
        display: block;
        color: #92400e;
        font-size: 11px;
        font-weight: 400;
        margin-top: 2px;
        line-height: 1.3;
    }

    .sl-form-builder-panel .existing-item--format {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 6px !important;
        margin: 2px 0 8px !important;
        padding: 9px 12px !important;
        background: #f3f4f6 !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        height: auto !important;
        min-height: 0 !important;
    }
    .sl-form-builder-panel .existing-item__label {
        font-weight: 700 !important;
        color: #111827 !important;
        font-size: 13px !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .sl-form-builder-panel .existing-item__options {
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .sl-form-builder-panel .existing-item__option {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
        min-height: 0 !important;
    }

    /* Purchase CTA — match legacy green-btn (flat gradient, no soft shadow) */
    .sl-form-builder-panel .sl-form-builder-purchase {
        display: block;
        margin: 0 0 4px;
        padding: 0;
    }
    .sl-form-builder-panel .sl-fb-activate-btn {
        display: block;
        width: 100%;
        height: 42px;
        margin: 0;
        padding: 0 16px;
        border: 1px solid #006201;
        border-radius: 6px;
        background: #008901;
        background: linear-gradient(to bottom, #008901 0%, #007a01 100%);
        color: #fff;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px;
        font-weight: 700;
        line-height: 40px;
        text-align: center;
        text-transform: none;
        letter-spacing: 0;
        cursor: pointer;
        box-shadow: none;
        appearance: none;
        -webkit-appearance: none;
    }
    .sl-form-builder-panel .sl-fb-activate-btn:hover {
        background: #009a01;
        background: linear-gradient(to bottom, #009a01 0%, #008901 100%);
    }
    .sl-form-builder-panel .sl-fb-activate-btn:disabled {
        opacity: 0.7;
        cursor: wait;
    }
    .sl-form-builder-panel .sl-fb-activate-btn .sl-fb-activate-price {
        font-weight: 700;
        opacity: 0.95;
    }
    .sl-form-builder-panel .sl-form-builder-lock-copy {
        margin: 10px 0 0;
        color: #4b5563;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 400;
    }
    .sl-form-builder-panel .sl-form-builder-purchase-note {
        display: block;
        margin: 0;
        padding: 10px 12px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 6px;
        color: #92400e;
        font-size: 13px;
        line-height: 1.4;
    }
    .sl-form-builder-panel.sl-form-builder-panel--locked {
        padding-bottom: 14px;
    }

    .sl-form-builder-panel #iframe_container { margin-top: 4px; }
    .sl-form-builder-panel .sl-fb-iframe-placeholder,
    .sl-form-builder-panel .sl-fb-locked-placeholder {
        padding: 14px 12px;
        text-align: center;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.4;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        border-radius: 6px;
        min-height: 0;
    }

    /* Legacy .footer-rouded — gray bar under Form Builder iframe */
    .sl-fb-expand-footer.footer-rouded,
    .sl-fb-expand-footer {
        float: none;
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 10px;
        box-sizing: border-box;
        background-color: #cccccc;
        border: 0;
        border-radius: 0 0 8px 8px;
        text-align: right;
        display: block;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        font-weight: normal;
        color: #5f5f5f;
        line-height: 25px;
        cursor: pointer;
        user-select: none;
    }
    .sl-fb-expand-footer .expand-reduce {
        cursor: pointer;
        vertical-align: middle;
    }
    .sl-fb-expand-footer span.expand-reduce {
        display: inline-block;
        margin-right: 4px;
    }
    .sl-fb-expand-footer img.expand-reduce {
        display: inline-block;
        width: 25px;
        height: auto;
    }

    @media (max-width: 640px) {
        .sl-form-builder-panel { padding: 12px 10px 8px; }
        .sl-form-builder-panel .form-builder-title { font-size: 16px; }
        .sl-form-builder-panel .activate-for-profile {
            margin-left: 0;
            width: 100%;
            justify-content: space-between;
        }
    }
    html.dark .sl-form-builder-panel {
        background: rgb(17 24 39) !important;
        border-color: rgb(55 65 81) !important;
        box-shadow: none !important;
        color: rgb(229 231 235) !important;
    }
    html.dark .sl-form-builder-panel .form-builder-title { color: rgb(134 239 172) !important; border-color: rgb(55 65 81) !important; }
    html.dark .sl-form-builder-panel .existing-item,
    html.dark .sl-form-builder-panel .existing-item label,
    html.dark .sl-form-builder-panel .activate-for-profile { color: rgb(229 231 235) !important; }
    html.dark .sl-form-builder-panel .existing-item--format { background: rgb(31 41 55) !important; border-color: rgb(55 65 81) !important; }
    html.dark .sl-fb-expand-footer { background-color: rgb(55 65 81) !important; color: rgb(229 231 235) !important; }
</style>

<div class="form-builder-box sl-form-builder-panel {{ $formBuilderPanelClass }} {{ $settingsLocked ? 'sl-form-builder-panel--locked' : '' }}">
    <div class="form-builder-title">
        Form Builder
        <span class="sl-help-wrap">
            <img src="{{ asset('images/help_icon.jpg') }}" class="help-icon" alt="Help" width="18" height="18">
            <span class="help-div" role="tooltip">
                <a href="{{ $formBuilderHelpVideo }}" class="sl-help-video" data-video="{{ $formBuilderHelpVideo }}">Video Tutorial</a>
                <span class="arrow-bg" aria-hidden="true"></span>
            </span>
        </span>

        @if (! $settingsLocked)
            <div class="activate-for-profile">
                <span><label for="enable_form">Enable</label></span>
                <span>
                    <input
                        type="checkbox"
                        id="enable_form"
                        wire:model.live="data.form_is_enable"
                        value="1"
                    >
                </span>
            </div>
        @endif
    </div>

    @if ($settingsLocked)
        @if ($record?->exists)
            <div class="sl-form-builder-purchase">
                @if ($canPurchaseFormBuilder && ! empty($formBuilderPurchaseUrl))
                    <button
                        type="button"
                        class="sl-fb-activate-btn"
                        wire:click="startFormBuilderPurchase"
                        wire:loading.attr="disabled"
                    >
                        Activate Form Builder — <span class="sl-fb-activate-price">${{ number_format(\App\Support\PricingSettings::formBuilder(), 2) }} AUD</span>
                    </button>
                    <p class="sl-form-builder-lock-copy">
                        One-time activation for this code profile. After purchase you can enable the form and edit questions.
                    </p>
                @else
                    <span class="sl-form-builder-purchase-note">
                        Form Builder activation must be purchased by the primary account user ($5 AUD, one-time per profile).
                    </span>
                @endif
            </div>
        @else
            <div class="sl-fb-locked-placeholder">
                Save this code profile first, then activate Form Builder.
            </div>
        @endif
    @else
        <div class="existing-item">
            <input
                type="checkbox"
                id="add_existing"
                @disabled(! ($record?->exists && $canAccessFormBuilder))
                @checked(! empty($this->appliedExistingForms))
                wire:click.prevent="openExistingFormModal"
            >
            <label for="add_existing">Use an existing form</label>
        </div>

        @if (! empty($this->appliedExistingForms))
            <div class="sl-applied-forms" wire:key="applied-forms-{{ count($this->appliedExistingForms) }}">
                @foreach ($this->appliedExistingForms as $applied)
                    <span class="sl-applied-chip" wire:key="applied-{{ $applied['key'] }}">
                        <span class="sl-applied-chip__label">{{ $applied['label'] }}</span>
                        <button
                            type="button"
                            class="sl-applied-chip__remove"
                            title="Remove this form's questions"
                            aria-label="Remove {{ $applied['label'] }}"
                            x-on:click="window.slConfirm(@js('Remove all questions copied from '.$applied['label'].'?')).then(ok => ok && $wire.removeAppliedForm(@js($applied['key'])))"
                            wire:loading.attr="disabled"
                        >&times;</button>
                    </span>
                @endforeach
            </div>
        @endif

        <div class="existing-item">
            @if ($analyticsLocked)
                <input type="checkbox" id="enable_form_analytics" value="1" checked disabled>
            @else
                <input
                    type="checkbox"
                    id="enable_form_analytics"
                    wire:model.live="data.enable_form_analytics"
                    value="1"
                >
            @endif
            <label for="enable_form_analytics">
                Enable Form Analytics
                <small>(Note: editing this form will not be possible once saved)</small>
            </label>
        </div>

        <div class="existing-item existing-item--format">
            <div class="existing-item__label">Form submission format</div>
            <div class="existing-item__options">
                <div class="existing-item__option">
                    <input
                        type="radio"
                        name="form_submission_format_sidebar"
                        wire:model.live="data.form_submission_format"
                        value="0"
                        id="email_only"
                    >
                    <label for="email_only">Email only</label>
                </div>
                <div class="existing-item__option">
                    <input
                        type="radio"
                        name="form_submission_format_sidebar"
                        wire:model.live="data.form_submission_format"
                        value="1"
                        id="email_with_pdf"
                    >
                    <label for="email_with_pdf">Email notification with PDF</label>
                </div>
            </div>
        </div>

        <div id="iframe_container" wire:key="fb-iframe-host-{{ $record?->getKey() ?? 'new' }}-{{ $this->formBuilderEmbedNonce ?? 0 }}">
            @if ($formBuilderEmbedUrl ?? null)
                <iframe
                    id="iframe_frm_builder"
                    width="100%"
                    height="1114"
                    frameborder="0"
                    scrolling="no"
                    src="{{ $formBuilderEmbedUrl }}"
                    title="Form Builder"
                ></iframe>
            @else
                <div class="sl-fb-iframe-placeholder">
                    @if ($record?->exists)
                        Form Builder failed to load. Refresh the page.
                    @else
                        Opening Form Builder…
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>

@unless ($settingsLocked)
    <div class="footer-rouded sl-fb-expand-footer">
        <span class="expand-reduce" id="sl-expand-reduce-label">Expand Window</span>
        <img
            src="{{ asset('images/expand_window.png') }}"
            class="expand-reduce"
            id="expand_reduce_img"
            width="25"
            alt=""
        >
    </div>
@endunless

@include('filament.portal.profiles.existing-form-modal')
