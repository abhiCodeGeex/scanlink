@php
    /** @var \App\Models\Profile|null $record */
    $participantsUrl = $participantsUrl ?? null;
    $isUrlLinkCode = (bool) ($isUrlLinkCode ?? false);
    $isSurvey = (bool) ($isSurvey ?? false);
    $isExhibit = (bool) ($isExhibit ?? false);
    $isVoc = (bool) ($isVoc ?? false);
    $isCreateEditor = (bool) ($isCreateEditor ?? false);
    $showSidebarFormBuilder = ! $isUrlLinkCode && ! $isSurvey;
    $showPhonePreview = ! $isUrlLinkCode;
    // Legacy parity: every create screen shows a blank iphone shell (empty
    // <section class="iphone-preview">); the live preview only appears on edit.
    $blankCreatePhone = $isCreateEditor;
    // Legacy location/plant/… create has no QR download block — only edit (and code preview).
    $showQrBlock = $isUrlLinkCode
        || (! $isCreateEditor && (
            (($isExhibit || $isVoc) && ($qrImageUrl || $qrUrl))
            || ($isSurvey)
            || (! $isSurvey && ! $isExhibit && ! $isVoc && ($qrImageUrl || $qrUrl))
        ));
@endphp

<div class="sl-legacy-preview-sidebar {{ $isUrlLinkCode ? 'sl-legacy-preview-sidebar--code' : '' }} {{ $isSurvey ? 'sl-legacy-preview-sidebar--survey' : '' }}">
    @if ($showPhonePreview)
        <section class="iphone-preview">
            <div class="iphone-preview-container">
                @if ($previewUrl && ! $blankCreatePhone)
                    <iframe
                        width="320"
                        height="882"
                        frameborder="0"
                        scrolling="auto"
                        src="{{ $previewUrl }}"
                        title="Mobile preview"
                        style="display:block;width:320px;height:882px;border:0;"
                        wire:key="preview-{{ $record?->updated_at?->timestamp ?? 'new' }}-{{ $previewRefreshKey ?? 0 }}"
                    ></iframe>
                @elseif (! $blankCreatePhone)
                    <div class="sl-iphone-placeholder">Save the profile to preview the mobile page here.</div>
                @endif
            </div>
        </section>
    @endif

    @if ($isUrlLinkCode)
        <div class="sl-qr-block form-view1 sl-code-preview-block">
            <div class="graybar_preview">Code Preview</div>
            <div class="code_preview_qr">
                @if (($showCodePreviewImage ?? true) && $qrImageUrl)
                    <img class="sl-qr-image" src="{{ $qrImageUrl }}" alt="QR code" width="185" height="185">
                @else
                    <div class="sl-code-preview-empty">
                        @if ($isCreateEditor || ! ($record?->exists ?? false))
                            Save this code to generate the QR preview.
                        @else
                            QR preview unavailable. Try refreshing the page.
                        @endif
                    </div>
                @endif
            </div>
            <div class="code_review">
                @if ($canDownloadQr ?? true)
                    <select
                        wire:model="qrDownloadFormat"
                        class="sl-qr-download-select noUniform"
                        aria-label="Download As"
                        @disabled(! $qrImageUrl)
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
                        class="green-btn sl-qr-download-btn download_code_as"
                        wire:click="downloadQrCode"
                        @disabled(! $qrImageUrl)
                    >Download</button>
                @endif
            </div>
        </div>
    @elseif ($showQrBlock && ($qrImageUrl || $qrUrl))
        {{-- Legacy plant/edit: QR float-left + button-section float-right, then URL below. --}}
        <div class="sl-qr-block form-view1">
            <div class="sl-qr-row">
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
                        >Download</button>
                    @endif
                    @if ($orderLabelUrl)
                        <a class="gray-btn" href="{{ $orderLabelUrl }}">Order Label</a>
                    @endif
                </section>
            </div>
            @if ($qrUrl)
                <div class="sl-qr-url-block">
                    URL<br>
                    <div class="qr-url-div">{{ $qrUrl }}</div>
                </div>
            @endif
        </div>
    @endif

    @if ($showSidebarFormBuilder)
        @include('filament.portal.profiles.legacy-form-builder-panel', [
            'formBuilderHelpVideo' => ($isSurvey ?? false) || ($isExhibit ?? false) || ($isVoc ?? false)
                ? 'https://www.youtube.com/embed/CfLEhcgvgrA?rel=0'
                : 'https://www.youtube.com/embed/cYQnzxkp528?rel=0',
        ])
        {{-- Legacy Colorbox trigger target for Add/Edit Participant List --}}
        <div
            id="sl-participant-list-host"
            data-participants-url="{{ $participantsUrl }}"
            hidden
        ></div>
    @endif
</div>

@include('filament.portal.profiles.legacy-form-builder-assets')

@if ($isUrlLinkCode)
    {{-- Live QR colour preview: recolour the QR the instant the Colour Selector changes,
         client-side (canvas), so the user sees it without saving / reloading. --}}
    <script>
        (function () {
            var baseImg = null;   // cached original QR bitmap (recolour from this each time)

            function qrEl() {
                return document.querySelector('.sl-code-preview-block .sl-qr-image, .code_preview_qr .sl-qr-image');
            }

            function hexToRgb(h) {
                h = String(h || '').trim().replace('#', '');
                if (h.length === 3) { h = h.split('').map(function (c) { return c + c; }).join(''); }
                if (!/^[0-9a-fA-F]{6}$/.test(h)) { return null; }
                return { r: parseInt(h.slice(0, 2), 16), g: parseInt(h.slice(2, 4), 16), b: parseInt(h.slice(4, 6), 16) };
            }

            function ensureBase(cb) {
                var img = qrEl();
                if (!img) { return; }
                var src = img.getAttribute('data-qr-base') || img.src;
                if (!img.getAttribute('data-qr-base')) { img.setAttribute('data-qr-base', src); }
                if (baseImg && baseImg.__src === src) { cb(baseImg); return; }
                var im = new Image();
                im.onload = function () { baseImg = im; baseImg.__src = src; cb(im); };
                im.onerror = function () {};
                im.src = src;
            }

            function recolour(hex) {
                var rgb = hexToRgb(hex);
                var img = qrEl();
                if (!rgb || !img) { return; }
                ensureBase(function (base) {
                    var w = base.naturalWidth || base.width, h = base.naturalHeight || base.height;
                    if (!w || !h) { return; }
                    var c = document.createElement('canvas');
                    c.width = w; c.height = h;
                    var ctx = c.getContext('2d');
                    ctx.drawImage(base, 0, 0);
                    try {
                        var d = ctx.getImageData(0, 0, w, h), p = d.data;
                        for (var i = 0; i < p.length; i += 4) {
                            // Any pixel noticeably darker than white is a QR module → paint it.
                            if (p[i + 3] > 10 && ((255 - p[i]) + (255 - p[i + 1]) + (255 - p[i + 2])) > 60) {
                                p[i] = rgb.r; p[i + 1] = rgb.g; p[i + 2] = rgb.b;
                            }
                        }
                        ctx.putImageData(d, 0, 0);
                        img.src = c.toDataURL('image/png');
                    } catch (e) { /* tainted canvas — ignore */ }
                });
            }

            function colourInput() {
                var el = document.querySelector('[wire\\:model="data.color_code"], [wire\\:model\\.live="data.color_code"], [wire\\:model\\.lazy="data.color_code"]');
                if (el) { return el; }
                var labels = document.querySelectorAll('.sl-add-form-left .fi-fo-field-wrp-label, .sl-add-form-left label');
                for (var i = 0; i < labels.length; i++) {
                    if (/colour selector/i.test(labels[i].textContent || '')) {
                        var wrap = labels[i].closest('.fi-fo-field-wrp');
                        if (wrap) { var inp = wrap.querySelector('input[type="text"], input:not([type="hidden"])'); if (inp) { return inp; } }
                    }
                }
                return null;
            }

            function onChange(e) {
                var t = e.target;
                if (!t || t.tagName !== 'INPUT') { return; }
                var ci = colourInput();
                if (!ci || t !== ci) { return; }
                var v = t.value;
                if (/^#?[0-9a-fA-F]{6}$/.test(v)) { recolour(v.charAt(0) === '#' ? v : '#' + v); }
            }

            document.addEventListener('input', onChange, true);
            document.addEventListener('change', onChange, true);
        })();
    </script>
@endif
