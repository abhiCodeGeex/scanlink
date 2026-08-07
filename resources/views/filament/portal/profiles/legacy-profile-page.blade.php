<x-filament-panels::page>
    {{-- Legacy ScanLink create/edit: form left (500px), iPhone + Form Builder right (450px) --}}
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}?v=legacy-profile-23">
    <link rel="stylesheet" href="{{ asset('css/filament/scanlink-theme.css') }}?v=legacy-profile-23">

    @if (! empty($previewData['isUrlLinkCode'] ?? null) || request('type') === 'code')
    {{-- URL Link (code) editor design — inline so the SPA re-applies it on every load
         (external CSS is cached/kept across SPA navigations). 2-column card layout. --}}
    <style>
        .sl-profile-editor.sl-profile-editor--code {
            width: 100% !important;
            max-width: 1060px !important;
            margin: 8px auto 0 !important;
            overflow: visible !important;
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) 320px !important;
            grid-template-areas: "form preview" !important;
            column-gap: 32px !important;
            align-items: start !important;
        }
        .sl-profile-editor.sl-profile-editor--code .sl-add-form-left,
        .sl-profile-editor.sl-profile-editor--code .sl-add-form-right {
            width: auto !important;
            max-width: none !important;
            margin: 0 !important;
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 12px !important;
            padding: 24px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06) !important;
            box-sizing: border-box !important;
            overflow: visible !important;
        }
        .sl-profile-editor.sl-profile-editor--code .sl-add-form-left { grid-area: form !important; }
        .sl-profile-editor.sl-profile-editor--code .sl-add-form-right {
            grid-area: preview !important;
            width: 320px !important;
            max-width: 320px !important;
        }
        /* Form fields fill the card; sections are seamless inside the card. */
        .sl-profile-editor--code .sl-add-form-left .fi-input-wrp,
        .sl-profile-editor--code .sl-add-form-left .fi-fo-field-wrp { max-width: 100% !important; }
        .sl-profile-editor--code .sl-add-form-left .fi-section,
        .sl-profile-editor--code .sl-add-form-left .fi-sc-section { padding: 0 !important; border: 0 !important; box-shadow: none !important; background: transparent !important; }
        .sl-profile-editor--code .sl-add-form-left .fi-section-content { gap: 14px !important; }
        .sl-profile-editor--code .SectionTitleCode { font-size: 16px !important; margin: 0 0 4px !important; color: #111827 !important; }
        /* Labels a touch smaller/greyer for a cleaner form rhythm. */
        .sl-profile-editor--code .sl-add-form-left .fi-fo-field-wrp-label,
        .sl-profile-editor--code .codelabel { font-size: 12.5px !important; font-weight: 600 !important; color: #4b5563 !important; }
        .sl-profile-editor--code .sl-add-form-left textarea { min-height: 60px !important; }

        /* Data-collection checkboxes → one tidy inline group (no tall stack). */
        .sl-profile-editor--code .sl-code-dc-fields {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 6px 26px !important;
            margin: 2px 0 0 !important;
            max-width: none !important;
        }
        .sl-profile-editor--code .sl-code-dc-fields .fi-fo-field-wrp,
        .sl-profile-editor--code .sl-code-dc-fields > * { width: auto !important; margin: 0 !important; }
        .sl-profile-editor--code .sl-code-dc-compulsory { margin: 2px 0 0 !important; }
        /* Yes/No + QR/DM radios sit inline and compact. */
        .sl-profile-editor--code .fi-fo-radio .fi-fo-radio-options,
        .sl-profile-editor--code .sl-code-type-radios .fi-fo-radio-options {
            display: flex !important; flex-direction: row !important; gap: 22px !important; flex-wrap: wrap !important;
        }
        /* Colour + Code Type on one row. */
        .sl-profile-editor--code .sl-code-colour-type-row {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto !important;
            gap: 26px !important;
            align-items: start !important;
            margin-top: 2px !important;
        }

        /* QR card content centred. */
        .sl-profile-editor--code .sl-add-form-right .sl-qr-block,
        .sl-profile-editor--code .sl-add-form-right .code_preview_qr,
        .sl-profile-editor--code .sl-add-form-right .code_review { text-align: center !important; }
        .sl-profile-editor--code .sl-add-form-right .sl-qr-image { max-width: 100% !important; height: auto !important; }

        /* Small, CENTERED Save — override the general full-width editor button rule. */
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions,
        .sl-profile-editor--code .sl-add-form-left .fi-form-actions,
        .sl-profile-editor--code .fi-form-actions {
            display: flex !important;
            justify-content: center !important;
            max-width: none !important;
            width: 100% !important;
            margin: 22px 0 2px !important;
        }
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions .fi-btn,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions button[type="submit"],
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions .fi-ac,
        .sl-profile-editor--code .fi-form-actions .fi-btn,
        .sl-profile-editor--code .fi-form-actions button[type="submit"] {
            display: inline-flex !important;
            width: auto !important;
            min-width: 150px !important;
            max-width: 210px !important;
            margin: 0 !important;
            padding: 10px 34px !important;
            font-size: 14px !important;
            flex: 0 0 auto !important;
        }
        @media (max-width: 959px) {
            .sl-profile-editor.sl-profile-editor--code {
                grid-template-columns: minmax(0, 1fr) !important;
                grid-template-areas: "form" "preview" !important;
            }
            .sl-profile-editor.sl-profile-editor--code .sl-add-form-right { width: auto !important; max-width: none !important; }
        }
    </style>
    @endif

    @php
        $previewData = $this->legacyPreviewData();
        $isUrlLinkCode = ! empty($previewData['isUrlLinkCode']);
        $isSurvey = ! empty($previewData['isSurvey']);
        $isVoc = ! empty($previewData['isVoc']);
        $isExhibit = ! empty($previewData['isExhibit']);
        $isMisc = ! empty($previewData['isMisc']);
        $isAsset = ! empty($previewData['isAsset']);
        $usesCkEditor = $isVoc || $isExhibit || $isMisc || $isAsset;
        $editorClass = trim(
            ($isUrlLinkCode ? 'sl-profile-editor--code' : '')
            .' '.($isSurvey ? 'sl-profile-editor--survey' : '')
            .' '.($isVoc ? 'sl-profile-editor--voc' : '')
            .' '.($isExhibit ? 'sl-profile-editor--exhibit' : '')
            .' '.($isMisc ? 'sl-profile-editor--misc' : '')
            .' '.($isAsset ? 'sl-profile-editor--asset' : '')
        );
    @endphp
    <div class="scanlink-container sl-profile-editor clearfix {{ $editorClass }}">
        {{-- DOM order matches legacy: right column first, left second --}}
        <section class="add-form-right sl-add-form-right {{ $isUrlLinkCode ? 'add-form-right-code' : '' }}">
            @include('filament.portal.profiles.legacy-preview-sidebar', $previewData)
        </section>

        <section class="add-form-left sl-add-form-left {{ $isSurvey ? 'sl-add-form-left--survey' : '' }}">
            {{ $this->content }}

            {{-- Legacy survey/index.php: Form Builder then Save under Logo on the LEFT. --}}
            @if ($isSurvey)
                @include('filament.portal.profiles.legacy-form-builder-panel', array_merge($previewData, [
                    'formBuilderHelpVideo' => 'https://www.youtube.com/embed/CfLEhcgvgrA?rel=0',
                    'formBuilderPanelClass' => 'sl-survey-form-builder',
                ]))
                <ul class="form-view clearfix sl-survey-save-wrap">
                    <li style="text-align:center;">
                        <button
                            type="button"
                            class="green-btn sl-survey-save-btn"
                            wire:click="save"
                            wire:loading.attr="disabled"
                        >SAVE</button>
                    </li>
                </ul>
            @endif
        </section>
    </div>

    @if ($showFormBuilderOrderSuccess)
        <div
            class="sl-fb-order-success-overlay"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sl-fb-order-success-title"
            wire:key="fb-order-success-modal"
        >
            <div class="sl-fb-order-success-dialog">
                <p class="sl-fb-order-success-body">
                    <span id="sl-fb-order-success-title" class="nearly-done-title">Thank you for your order.</span>
                    <br><br>
                    The form builder function is now activated<br>
                    for this code profile.
                    <br><br>
                    <button
                        type="button"
                        class="green-btn sl-fb-order-success-ok"
                        wire:click="closeFormBuilderOrderSuccess"
                    >OK</button>
                </p>
            </div>
        </div>
    @endif

    @if ($usesCkEditor)
        <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
        <style>
            .sl-profile-editor--voc .cke,
            .sl-profile-editor--exhibit .cke,
            .sl-profile-editor--misc .cke,
            .sl-profile-editor--asset .cke {
                max-width: 456px !important;
                margin: 4px 0 10px !important;
            }
            .sl-profile-editor--voc .cke_chrome,
            .sl-profile-editor--exhibit .cke_chrome,
            .sl-profile-editor--misc .cke_chrome,
            .sl-profile-editor--asset .cke_chrome {
                border: 1px solid #ccc !important;
                visibility: visible !important;
            }
            .sl-profile-editor--voc textarea.sl-ckeditor,
            .sl-profile-editor--exhibit textarea.sl-ckeditor,
            .sl-profile-editor--misc textarea.sl-ckeditor,
            .sl-profile-editor--asset textarea.sl-ckeditor {
                min-height: 80px !important;
                max-width: 456px !important;
            }
            .sl-profile-editor--exhibit,
            .sl-profile-editor--misc {
                overflow: visible !important;
            }
        </style>
        <script>
            (function () {
                function syncEditorsToTextareas() {
                    if (typeof CKEDITOR === 'undefined') {
                        return;
                    }
                    Object.keys(CKEDITOR.instances || {}).forEach(function (name) {
                        try {
                            CKEDITOR.instances[name].updateElement();
                            var el = CKEDITOR.instances[name].element && CKEDITOR.instances[name].element.$;
                            if (el) {
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                            }
                        } catch (e) {}
                    });
                }

                function initLegacyCkEditors() {
                    if (typeof CKEDITOR === 'undefined') {
                        return;
                    }
                    document.querySelectorAll('textarea.sl-ckeditor').forEach(function (el) {
                        if (! el.id) {
                            el.id = 'sl_ck_' + Math.random().toString(36).slice(2, 10);
                        }
                        if (CKEDITOR.instances[el.id]) {
                            return;
                        }
                        var toolbar = el.getAttribute('data-ck-toolbar') || 'MyToolbar';
                        var editor = CKEDITOR.replace(el.id, {
                            toolbar: toolbar,
                            enterMode: CKEDITOR.ENTER_BR,
                            height: 120,
                            width: '100%'
                        });
                        editor.on('change', function () {
                            editor.updateElement();
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                        });
                        editor.on('blur', function () {
                            editor.updateElement();
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                        });
                    });
                }

                function boot() {
                    initLegacyCkEditors();
                    setTimeout(initLegacyCkEditors, 400);
                    setTimeout(initLegacyCkEditors, 1200);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', boot);
                } else {
                    boot();
                }

                document.addEventListener('livewire:navigated', boot);
                document.addEventListener('livewire:init', function () {
                    Livewire.hook('commit', function ({ succeed }) {
                        syncEditorsToTextareas();
                        succeed(function () {
                            setTimeout(initLegacyCkEditors, 50);
                        });
                    });
                });

                document.addEventListener('click', function (e) {
                    if (e.target.closest('button[type="submit"], .fi-btn[type="submit"], [wire\\:click="save"]')) {
                        syncEditorsToTextareas();
                    }
                }, true);
            })();
        </script>
    @endif

    {{-- Legacy help-icon video tutorial popup --}}
    <div
        id="sl-help-video-modal"
        class="sl-help-video-modal"
        hidden
        aria-hidden="true"
    >
        <div class="sl-help-video-backdrop" data-sl-help-close></div>
        <div class="sl-help-video-window" role="dialog" aria-modal="true" aria-label="Video Tutorial">
            <button type="button" class="sl-help-video-close" data-sl-help-close aria-label="Close">&times;</button>
            <div class="sl-help-video-frame" id="sl-help-video-frame"></div>
        </div>
    </div>

    <style>
        .sl-help-video-modal[hidden] { display: none !important; }
        .sl-help-video-modal {
            position: fixed; inset: 0; z-index: 80;
            display: flex; align-items: center; justify-content: center;
        }
        .sl-help-video-backdrop {
            position: absolute; inset: 0; background: rgba(0,0,0,.55);
        }
        .sl-help-video-window {
            position: relative; z-index: 1; background: #fff;
            padding: 28px 16px 16px; border-radius: 6px;
            box-shadow: 0 8px 30px rgba(0,0,0,.35); width: min(520px, 94vw);
        }
        .sl-help-video-close {
            position: absolute; top: 4px; right: 8px; border: 0; background: transparent;
            font-size: 28px; line-height: 1; cursor: pointer; color: #333;
        }
        .sl-help-video-frame iframe {
            display: block; width: 100%; height: 300px; border: 0;
        }
    </style>
    <script>
        (function () {
            var modal = document.getElementById('sl-help-video-modal');
            var frame = document.getElementById('sl-help-video-frame');
            if (! modal || ! frame) return;

            function openVideo(url) {
                frame.innerHTML = '<iframe src="' + url + '" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>';
                modal.hidden = false;
                modal.setAttribute('aria-hidden', 'false');
            }

            function closeVideo() {
                frame.innerHTML = '';
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
            }

            document.addEventListener('click', function (e) {
                var link = e.target.closest('a.sl-help-video');
                if (link) {
                    e.preventDefault();
                    e.stopPropagation();
                    openVideo(link.getAttribute('data-video') || link.getAttribute('href'));
                    return;
                }
                if (e.target.closest('[data-sl-help-close]')) {
                    closeVideo();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && ! modal.hidden) {
                    closeVideo();
                }
            });

            // The Form Builder iframe (same origin) posts its help-video requests here
            // so the in-iframe "?" help icons reuse this working modal.
            window.addEventListener('message', function (e) {
                if (e.origin !== window.location.origin) return;
                var d = e.data || {};
                if (d && d.type === 'scanlink-open-help-video' && d.url) {
                    openVideo(d.url);
                }
            });
        })();
    </script>

    {{-- Live phone preview: commit deferred form fields + reload iframe from session draft --}}
    <script>
        (function () {
            var timer = null;

            function previewIframe() {
                return document.querySelector('.iphone-preview-container iframe');
            }

            function reloadPreviewIframe() {
                var iframe = previewIframe();
                if (! iframe || ! iframe.src) {
                    return;
                }
                try {
                    var url = new URL(iframe.src, window.location.origin);
                    url.searchParams.set('_r', String(Date.now()));
                    iframe.src = url.toString();
                } catch (err) {
                    iframe.src = iframe.src;
                }
            }

            document.addEventListener('livewire:init', function () {
                Livewire.on('refresh-phone-preview', function () {
                    // wire:key remount usually handles this; force reload as backup.
                    setTimeout(reloadPreviewIframe, 30);
                });
            });

                function scheduleDraftPush() {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        try {
                            (Livewire.all() || []).forEach(function (component) {
                                try {
                                    component.call('pushPhonePreviewDraft');
                                } catch (e) {}
                            });
                        } catch (e) {}
                    }, 450);
                }

                function bindLivePreview() {
                    var root = document.querySelector('.sl-add-form-left');
                    if (! root || root.dataset.slPreviewBound === '1') {
                        return;
                    }
                    root.dataset.slPreviewBound = '1';

                    var handler = function (event) {
                        var target = event.target;
                        if (! target || ! root.contains(target)) {
                            return;
                        }
                        if (target.type === 'file') {
                            return;
                        }
                        if (typeof Livewire === 'undefined') {
                            return;
                        }
                        scheduleDraftPush();
                    };

                    root.addEventListener('input', handler, true);
                    root.addEventListener('change', handler, true);
                }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bindLivePreview);
            } else {
                bindLivePreview();
            }
            document.addEventListener('livewire:navigated', bindLivePreview);
        })();
    </script>
</x-filament-panels::page>
