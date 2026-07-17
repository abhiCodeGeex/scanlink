@php
    /** @var \App\Models\Profile|null $record */
@endphp

<div class="sl-legacy-preview-sidebar">
    <section class="iphone-preview">
        <div class="iphone-preview-container">
            @if ($previewUrl)
                <iframe
                    width="320"
                    height="480"
                    frameborder="0"
                    scrolling="auto"
                    src="{{ $previewUrl }}"
                    title="Mobile preview"
                    wire:key="preview-{{ $record?->updated_at?->timestamp ?? 'new' }}"
                ></iframe>
            @else
                <div class="sl-iphone-placeholder">Save the profile to preview the mobile page here.</div>
            @endif
        </div>
    </section>

    @if ($qrImageUrl || $qrUrl)
        <div class="sl-qr-block form-view1">
            @if ($qrImageUrl)
                <img class="sl-qr-image" src="{{ $qrImageUrl }}" alt="QR code" width="175" height="175">
            @endif
            <section class="button-section sl-qr-actions">
                @if ($orderLabelUrl)
                    <a class="gray-btn" href="{{ $orderLabelUrl }}">Order Label</a>
                @endif
            </section>
            <div class="clear"></div>
        </div>
        @if ($qrUrl)
            <div class="sl-qr-url-block">
                URL<br>
                <div class="qr-url-div">{{ $qrUrl }}</div>
            </div>
        @endif
    @endif

    <div class="form-builder-box">
        <div class="form-builder-title">
            Form Builder
            <img src="{{ asset('images/help_icon.jpg') }}" class="help-icon" alt="Help" style="vertical-align:text-bottom;">
        </div>

        @if ($formBuilderUrl && ($canAccessFormBuilder ?? true))
            <div class="existing-item" style="padding: 8px 10px 12px;">
                <a class="sl-open-form-builder" href="{{ $formBuilderUrl }}">Open Form Builder for this profile</a>
            </div>
            <div id="iframe_container">
                <iframe
                    id="iframe_frm_builder"
                    width="448"
                    height="700"
                    frameborder="0"
                    src="{{ $formBuilderUrl }}"
                    title="Form Builder"
                ></iframe>
            </div>
        @else
            <div class="existing-item" style="padding: 10px;">
                @if (! $record)
                    Save the profile first to use Form Builder.
                @else
                    Form Builder is not available for this account.
                @endif
            </div>
        @endif
    </div>
</div>
