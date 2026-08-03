{{-- Shared Form Builder chrome (location right column / survey left column). --}}
@php
    $formBuilderHelpVideo = $formBuilderHelpVideo
        ?? 'https://www.youtube.com/embed/CfLEhcgvgrA?rel=0';
    $formBuilderPanelClass = $formBuilderPanelClass ?? '';
    $analyticsLocked = (bool) ($record?->enable_form_analytics ?? false);
    $formBuilderPurchased = (bool) ($formBuilderPurchased ?? $record?->form_active);
    $canPurchaseFormBuilder = (bool) ($canPurchaseFormBuilder ?? false);
@endphp

<style>
    /* Professional, responsive Form Builder panel. */
    .sl-form-builder-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px 12px 0 0;
        padding: 16px 18px 6px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        font-family: Arial, Helvetica, sans-serif;
        box-sizing: border-box;
    }
    .sl-form-builder-panel * { box-sizing: border-box; }
    .sl-form-builder-panel .form-builder-title {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 17px;
        font-weight: 700;
        color: #008C00;
        padding-bottom: 12px;
        margin: 0 0 4px;
        border-bottom: 1px solid #eef0f2;
    }
    .sl-form-builder-panel .sl-help-wrap { position: relative; display: inline-flex; align-items: center; }
    .sl-form-builder-panel .activate-for-profile {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }
    .sl-form-builder-panel .activate-for-profile span { display: inline-flex; align-items: center; }
    /* Toggle switch for Enable */
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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    .sl-form-builder-panel .activate-for-profile input[type="checkbox"]:checked { background: #008C00; }
    .sl-form-builder-panel .activate-for-profile input[type="checkbox"]:checked::after { left: 21px; }
    .sl-form-builder-panel .activate-for-profile input[type="checkbox"]:disabled { opacity: 0.5; cursor: not-allowed; }
    /* Regular checkboxes / radios */
    .sl-form-builder-panel .existing-item input[type="checkbox"],
    .sl-form-builder-panel .existing-item input[type="radio"] {
        width: 17px;
        height: 17px;
        accent-color: #008C00;
        cursor: pointer;
        margin: 0;
        flex: 0 0 auto;
    }
    .sl-form-builder-panel .existing-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin: 12px 0;
        font-size: 13px;
        color: #374151;
        line-height: 1.4;
    }
    .sl-form-builder-panel .existing-item > span:first-child { flex: 0 0 auto; padding-top: 1px; }
    .sl-form-builder-panel .existing-item label { cursor: pointer; font-weight: 500; margin: 0; }
    .sl-form-builder-panel .existing-item small { display: block; color: #d97706; font-weight: 400; margin-top: 2px; }
    .sl-form-builder-panel .existing-item--format {
        flex-direction: column;
        gap: 9px;
        background: #f7faf7;
        border: 1px solid #e6efe6;
        border-radius: 10px;
        padding: 12px 14px;
    }
    .sl-form-builder-panel .existing-item__label { font-weight: 700; color: #111827; }
    .sl-form-builder-panel .existing-item__option { display: inline-flex; align-items: center; gap: 7px; }
    .sl-form-builder-panel .existing-item__option label { font-weight: 500; }
    .sl-form-builder-panel .sl-form-builder-purchase { margin: 12px 0; }
    .sl-form-builder-panel .green-btn {
        background: #008C00;
        color: #fff;
        border: 0;
        border-radius: 9px;
        padding: 11px 20px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 140, 0, 0.22);
        transition: background 0.15s ease;
    }
    .sl-form-builder-panel .green-btn:hover { background: #00a300; }
    .sl-form-builder-panel #iframe_container { margin-top: 6px; }
    .sl-form-builder-panel .sl-fb-iframe-placeholder {
        padding: 2rem 1rem;
        text-align: center;
        color: #6b7280;
        font-size: 13px;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        border-radius: 9px;
    }
    .sl-fb-expand-footer {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-top: 0;
        border-radius: 0 0 12px 12px;
        padding: 9px 14px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        cursor: pointer;
    }
    @media (max-width: 640px) {
        .sl-form-builder-panel { padding: 14px 12px 6px; border-radius: 10px 10px 0 0; }
        .sl-form-builder-panel .form-builder-title { font-size: 16px; }
        .sl-form-builder-panel .activate-for-profile { margin-left: 0; width: 100%; justify-content: space-between; }
    }
</style>

<div class="form-builder-box sl-form-builder-panel {{ $formBuilderPanelClass }}">
    <div class="form-builder-title">
        Form Builder
        <span class="sl-help-wrap">
            <img src="{{ asset('images/help_icon.jpg') }}" class="help-icon" alt="Help" width="18" height="18">
            <span class="help-div" role="tooltip">
                <a href="{{ $formBuilderHelpVideo }}" class="sl-help-video" data-video="{{ $formBuilderHelpVideo }}">Video Tutorial</a>
                <span class="arrow-bg" aria-hidden="true"></span>
            </span>
        </span>

        <div class="activate-for-profile">
            <span><label for="enable_form">Enable</label></span>
            <span>
                <input
                    type="checkbox"
                    id="enable_form"
                    wire:model.live="data.form_is_enable"
                    value="1"
                    @disabled(! $formBuilderPurchased)
                >
            </span>
        </div>
    </div>

    @if ($record?->exists && ! $formBuilderPurchased)
        <div class="existing-item sl-form-builder-purchase">
            @if ($canPurchaseFormBuilder && ! empty($formBuilderPurchaseUrl))
                <button
                    type="button"
                    class="green-btn"
                    wire:click="startFormBuilderPurchase"
                    wire:loading.attr="disabled"
                >
                    Activate Form Builder — $5 AUD
                </button>
            @else
                <span>Form Builder activation must be purchased by the primary account user.</span>
            @endif
        </div>
    @endif

    <div class="existing-item">
        <span>
            <input
                type="checkbox"
                id="add_existing"
                style="margin-right:4px;"
                @disabled(! ($record?->exists && ($canAccessFormBuilder ?? true)))
                wire:click.prevent="openExistingFormModal"
            >
        </span>
        <span><label for="add_existing">Use an existing form</label></span>
    </div>

    <div class="existing-item">
        <span>
            @if ($analyticsLocked)
                <input
                    type="checkbox"
                    id="enable_form_analytics"
                    value="1"
                    checked
                    disabled
                >
            @else
                <input
                    type="checkbox"
                    id="enable_form_analytics"
                    wire:model.live="data.enable_form_analytics"
                    value="1"
                >
            @endif
        </span>
        <span>
            <label for="enable_form_analytics">
                Enable Form Analytics
                <small>(Note: editing this form will not be possible once saved)</small>
            </label>
        </span>
    </div>

    <div class="existing-item existing-item--format">
        <span class="existing-item__label">Form submission format</span>
        <span class="existing-item__option">
            <input type="radio" name="form_submission_format_sidebar" wire:model.live="data.form_submission_format" value="0" id="email_only">
            <label for="email_only">Email only</label>
        </span>
        <span class="existing-item__option">
            <input type="radio" name="form_submission_format_sidebar" wire:model.live="data.form_submission_format" value="1" id="email_with_pdf">
            <label for="email_with_pdf">Email notification with PDF</label>
        </span>
    </div>

    <div class="clear">&nbsp;</div>

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

    <div class="clear">&nbsp;</div>
</div>

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

@include('filament.portal.profiles.existing-form-modal')
