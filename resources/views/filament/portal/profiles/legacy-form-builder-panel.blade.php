{{-- Shared Form Builder chrome (location right column / survey left column). --}}
@php
    $formBuilderHelpVideo = $formBuilderHelpVideo
        ?? 'https://www.youtube.com/embed/cYQnzxkp528?rel=0';
    $formBuilderPanelClass = $formBuilderPanelClass ?? '';
@endphp

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
                >
            </span>
        </div>
    </div>

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
            <input type="checkbox" id="enable_form_analytics" wire:model.live="data.enable_form_analytics" value="1">
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
