<x-filament-panels::page>
    {{-- Legacy ScanLink create/edit: form left (500px), iPhone + Form Builder right (450px) --}}
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}?v=legacy-profile-16">
    <link rel="stylesheet" href="{{ asset('css/filament/scanlink-theme.css') }}?v=legacy-profile-16">

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
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50" role="dialog" aria-modal="true">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl text-center">
                <h2 class="text-xl font-bold text-green-800">Thank you for your order.</h2>
                <p class="mt-3 text-sm text-gray-700">
                    The form builder function is now activated<br>
                    for this code profile.
                </p>
                <div class="mt-5">
                    <button type="button" class="green-btn" wire:click="closeFormBuilderOrderSuccess">OK</button>
                </div>
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
