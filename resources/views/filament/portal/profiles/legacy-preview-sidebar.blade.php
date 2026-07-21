@php
    /** @var \App\Models\Profile|null $record */
    $participantsUrl = $participantsUrl ?? null;
@endphp

<div class="sl-legacy-preview-sidebar">
    <section class="iphone-preview">
        <div class="iphone-preview-container">
            @if ($previewUrl)
                <iframe
                    width="320"
                    height="882"
                    frameborder="0"
                    scrolling="auto"
                    src="{{ $previewUrl }}"
                    title="Mobile preview"
                    style="display:block;width:320px;height:882px;border:0;"
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
                @if ($canDownloadQr ?? true)
                    <select
                        wire:model="qrDownloadFormat"
                        class="sl-qr-download-select noUniform"
                        aria-label="Download As"
                    >
                        <option value="">Download As</option>
                        <option value="pdf">PDF</option>
                        <option value="tiff">TIFF</option>
                        <option value="eps">Eps(Vector)</option>
                        <option value="png">PNG</option>
                        <option value="jpg">JPG</option>
                    </select>
                    <button
                        type="button"
                        class="green-btn sl-qr-download-btn"
                        wire:click="downloadQrCode"
                    >DOWNLOAD</button>
                @endif
                @if ($orderLabelUrl)
                    <a class="gray-btn" href="{{ $orderLabelUrl }}">ORDER LABEL</a>
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

    {{-- Live Form Builder: parent chrome + iframe (matches location/index.php) --}}
    <div class="form-builder-box sl-form-builder-panel">
        <div class="form-builder-title">
            Form Builder
            <span class="sl-help-wrap">
                <img src="{{ asset('images/help_icon.jpg') }}" class="help-icon" alt="Help" width="18" height="18">
                <span class="help-div" role="tooltip">
                    <a href="https://www.youtube.com/embed/cYQnzxkp528?rel=0" class="sl-help-video" data-video="https://www.youtube.com/embed/cYQnzxkp528?rel=0">Video Tutorial</a>
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
            <span><input type="checkbox" id="add_existing" style="margin-right:4px;" @disabled(! ($formLibraryUrl ?? null))></span>
            @if (($formLibraryUrl ?? null) && ($canAccessFormBuilder ?? true))
                <span><label for="add_existing"><a class="sl-open-form-builder" href="{{ $formLibraryUrl }}">Use an existing form</a></label></span>
            @else
                <span><label for="add_existing">Use an existing form</label></span>
            @endif
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

        <div class="existing-item">
            <span>Form submission format</span>
            <span>
                <input type="radio" name="form_submission_format_sidebar" wire:model.live="data.form_submission_format" value="0" id="email_only">
            </span>
            <span><label for="email_only">Email only</label></span>
            <span>
                <input type="radio" name="form_submission_format_sidebar" wire:model.live="data.form_submission_format" value="1" id="email_with_pdf">
            </span>
            <span><label for="email_with_pdf">Email notification with PDF</label></span>
        </div>

        <div class="clear">&nbsp;</div>

        <div id="iframe_container" wire:key="fb-iframe-host-{{ $record?->getKey() ?? 'new' }}">
            @if ($formBuilderEmbedUrl ?? null)
                <iframe
                    id="iframe_frm_builder"
                    width="448"
                    height="1114"
                    frameborder="0"
                    scrolling="auto"
                    src="{{ $formBuilderEmbedUrl }}"
                    title="Form Builder"
                    wire:ignore
                ></iframe>
            @else
                <div class="sl-fb-iframe-placeholder">
                    @if ($record?->exists)
                        Form Builder failed to load. Refresh the page.
                    @else
                        Opening Form Builder… If this stays blank, use
                        <strong>Add a New Location Code</strong> from Master Code List
                        (<code>?type=location</code>).
                    @endif
                </div>
            @endif
        </div>

        <div class="clear">&nbsp;</div>
    </div>

    <div class="footer-rouded" style="display:flex;justify-content:flex-end;align-items:center;gap:8px;padding:6px 10px;background:#e8e8e8;">
        <span class="expand-reduce">Expand Window</span>
        <span aria-hidden="true">▾</span>
    </div>
</div>

<style>
    #iframe_container { width: 448px; max-width: 100%; margin: 0 auto; }
    #iframe_frm_builder { display: block; width: 448px; max-width: 100%; border: 0; background: #fff; }
    .sl-fb-iframe-placeholder {
        min-height: 200px; display: flex; align-items: center; justify-content: center;
        color: #999; font-size: 13px; font-weight: 600; border: 1px dashed #ccc; margin: 10px; padding: 20px;
    }
</style>
<script>
    window.addEventListener('message', function (event) {
        if (event.origin !== window.location.origin) {
            return;
        }
        if (! event.data || event.data.type !== 'scanlink-form-builder-saved') {
            return;
        }
        var preview = document.querySelector('.iphone-preview-container iframe');
        if (! preview || ! preview.src) {
            return;
        }
        try {
            var url = new URL(preview.src, window.location.origin);
            url.searchParams.set('_fb', String(Date.now()));
            preview.src = url.toString();
        } catch (e) {
            preview.src = preview.src;
        }
    });
</script>
