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
    // Exhibit/survey/voc create: blank phone like legacy index (empty iphone shell).
    $blankCreatePhone = ($isSurvey || $isExhibit || $isVoc) && $isCreateEditor;
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
                @endif
            </div>
            <div class="code_review">
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
                    @endif
                </section>
                <div class="download_div">
                    @if ($canDownloadQr ?? true)
                        <button
                            type="button"
                            class="green-btn sl-qr-download-btn download_code_as"
                            wire:click="downloadQrCode"
                        >DOWNLOAD</button>
                    @endif
                </div>
            </div>
        </div>
    @elseif ($showQrBlock && ($qrImageUrl || $qrUrl))
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
