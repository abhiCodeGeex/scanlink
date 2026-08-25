<x-filament-panels::page>
    {{-- Legacy ScanLink create/edit: form left (500px), iPhone + Form Builder right (450px) --}}
    @php
        $previewData = $this->legacyPreviewData();
        // Keep class + styles in sync for create (?type=code) and edit (slag=code).
        $isUrlLinkCode = ! empty($previewData['isUrlLinkCode']) || request('type') === 'code';
        $isSurvey = ! empty($previewData['isSurvey']);
        $isVoc = ! empty($previewData['isVoc']);
        $isExhibit = ! empty($previewData['isExhibit']);
        $isMisc = ! empty($previewData['isMisc']);
        $isAsset = ! empty($previewData['isAsset']);
        // CKEditor bridge retired: all rich-text fields now use Filament's native RichEditor
        // (Livewire-first — reliable sync, survives morphs, instant load). Keeping the flag
        // false skips the 500KB ckeditor.js and the fragile orphan/re-init machinery below.
        // (The legacy Form Builder iframe has its own CKEditor and is unaffected.)
        $usesCkEditor = false;
        $editorClass = trim(
            ($isUrlLinkCode ? 'sl-profile-editor--code' : '')
            .' '.($isSurvey ? 'sl-profile-editor--survey' : '')
            .' '.($isVoc ? 'sl-profile-editor--voc' : '')
            .' '.($isExhibit ? 'sl-profile-editor--exhibit' : '')
            .' '.($isMisc ? 'sl-profile-editor--misc' : '')
            .' '.($isAsset ? 'sl-profile-editor--asset' : '')
        );
    @endphp

    <link rel="stylesheet" href="{{ asset('styles/style.css') }}?v=legacy-profile-39">
    <link rel="stylesheet" href="{{ asset('css/filament/scanlink-theme.css') }}?v=legacy-profile-45">

    @if ($isUrlLinkCode)
    {{-- URL Link (code) editor — create + edit must match. Inline so SPA re-applies. --}}
    <style>
        .sl-profile-editor.sl-profile-editor--code {
            width: 100% !important;
            max-width: 980px !important;
            margin: 4px auto 0 !important;
            overflow: visible !important;
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) 280px !important;
            grid-template-areas: "form preview" !important;
            column-gap: 20px !important;
            align-items: start !important;
            row-gap: 12px !important;
        }
        .sl-profile-editor.sl-profile-editor--code .sl-add-form-left,
        .sl-profile-editor.sl-profile-editor--code .sl-add-form-right {
            width: auto !important;
            max-width: none !important;
            margin: 0 !important;
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 10px !important;
            padding: 10px 14px 12px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .05) !important;
            box-sizing: border-box !important;
            overflow: visible !important;
        }
        .sl-profile-editor.sl-profile-editor--code .sl-add-form-left { grid-area: form !important; }
        .sl-profile-editor.sl-profile-editor--code .sl-add-form-right {
            grid-area: preview !important;
            width: 280px !important;
            max-width: 280px !important;
            padding: 0 !important;
            overflow: hidden !important;
        }

        /* Kill Filament section ring + legacy SectionTitleBox inset (create & edit). */
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-section,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-sc-section,
        .sl-profile-editor--code .sl-add-form-left .fi-section,
        .sl-profile-editor--code .sl-add-form-left .fi-sc-section,
        .sl-profile-editor--code .sl-add-form-left .sl-code-url-form,
        .sl-profile-editor--code .sl-add-form-left .SectionTitleBox,
        .sl-profile-editor--code .sl-add-form-left .SectionTitleBoxCode {
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        /* Tight vertical rhythm — beat theme gap/margin rules. */
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-section-content,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-sc-section-content,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-section-content-ctn,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-sc-section-content-ctn,
        .sl-profile-editor--code .sl-add-form-left .fi-section-content,
        .sl-profile-editor--code .sl-add-form-left .fi-sc-section-content,
        .sl-profile-editor--code .sl-add-form-left .fi-section-content-ctn,
        .sl-profile-editor--code .sl-add-form-left .fi-sc-section-content-ctn {
            gap: 4px !important;
            padding: 0 !important;
            margin: 0 !important;
            row-gap: 4px !important;
        }
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-fo-field,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-fo-field-wrp,
        .sl-profile-editor--code .sl-add-form-left .fi-fo-field,
        .sl-profile-editor--code .sl-add-form-left .fi-fo-field-wrp {
            margin: 0 0 2px !important;
            padding: 0 !important;
            gap: 2px !important;
            max-width: 100% !important;
        }
        .sl-profile-editor--code .sl-add-form-left .fi-input-wrp {
            max-width: 100% !important;
        }
        .sl-profile-editor--code .SectionTitleCode {
            font-size: 15px !important;
            margin: 0 0 4px !important;
            line-height: 1.25 !important;
            color: #111827 !important;
        }
        .sl-profile-editor--code .sl-add-form-left .fi-fo-field-wrp-label,
        .sl-profile-editor--code .sl-add-form-left .fi-fo-field-label,
        .sl-profile-editor--code .codelabel {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #4b5563 !important;
            line-height: 1.2 !important;
            margin: 0 !important;
        }
        .sl-profile-editor--code .sl-add-form-left textarea,
        .sl-profile-editor--code .sl-code-popup-message textarea {
            min-height: 48px !important;
            max-height: 64px !important;
        }

        /* Data-collection checkboxes — compact, no extra field chrome. */
        .sl-profile-editor--code .sl-code-dc-compulsory.fi-fo-field,
        .sl-profile-editor--code .sl-code-dc-compulsory {
            display: block !important;
            width: auto !important;
            max-width: none !important;
            margin: 0 0 2px !important;
            padding: 0 !important;
            gap: 0 !important;
        }
        .sl-profile-editor--code .sl-code-dc-compulsory .fi-fo-field-label-col,
        .sl-profile-editor--code .sl-code-dc-compulsory .fi-fo-field-label-ctn,
        .sl-profile-editor--code .sl-code-dc-compulsory .fi-fo-field-label,
        .sl-profile-editor--code .sl-code-dc-fields .fi-fo-field-label-col,
        .sl-profile-editor--code .sl-code-dc-fields .fi-fo-field-label-ctn,
        .sl-profile-editor--code .sl-code-dc-fields .fi-fo-field-label {
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 6px !important;
            margin: 0 !important;
            padding: 0 !important;
            width: auto !important;
            max-width: none !important;
            min-height: 0 !important;
        }
        .sl-profile-editor--code .sl-code-dc-compulsory .fi-fo-field-content-col,
        .sl-profile-editor--code .sl-code-dc-fields .fi-fo-field-content-col {
            display: none !important;
        }
        .sl-profile-editor--code .sl-code-dc-compulsory .fi-fo-field-label-content,
        .sl-profile-editor--code .sl-code-dc-fields .fi-fo-field-label-content {
            display: inline !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 12.5px !important;
            font-weight: 400 !important;
            color: #555755 !important;
            line-height: 1.2 !important;
            border: 0 !important;
            background: none !important;
        }
        .sl-profile-editor--code .sl-code-dc-compulsory .fi-checkbox-input,
        .sl-profile-editor--code .sl-code-dc-fields .fi-checkbox-input {
            display: inline-block !important;
            width: 15px !important;
            height: 15px !important;
            min-width: 15px !important;
            min-height: 15px !important;
            margin: 0 !important;
            padding: 0 !important;
            flex: 0 0 15px !important;
            appearance: auto !important;
            -webkit-appearance: checkbox !important;
            opacity: 1 !important;
            position: static !important;
        }
        .sl-profile-editor--code .sl-code-dc-fields,
        .sl-profile-editor--code .sl-code-dc-fields.fi-sc-flex {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 0 12px !important;
            margin: 0 0 2px !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }
        .sl-profile-editor--code .sl-code-dc-fields > div,
        .sl-profile-editor--code .sl-code-dc-fields .fi-fo-field {
            display: inline-flex !important;
            align-items: center !important;
            width: auto !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            flex: 0 0 auto !important;
            gap: 0 !important;
        }

        .sl-profile-editor--code .fi-fo-radio .fi-fo-radio-options,
        .sl-profile-editor--code .sl-code-type-radios .fi-fo-radio-options,
        .sl-profile-editor--code .fi-fo-radio .fi-fo-radio-list,
        .sl-profile-editor--code .sl-code-type-radios .fi-radio-list {
            display: flex !important;
            flex-direction: row !important;
            gap: 14px !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            margin: 0 !important;
        }
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-checkbox,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-radio,
        .sl-profile-editor--code .sl-add-form-left .fi-checkbox,
        .sl-profile-editor--code .sl-add-form-left .fi-radio {
            margin: 0 !important;
        }

        .sl-profile-editor--code .sl-code-colour-type-row,
        .sl-profile-editor--code .sl-code-colour-type-row.fi-grid {
            display: grid !important;
            grid-template-columns: minmax(140px, 1fr) auto !important;
            gap: 12px 16px !important;
            align-items: end !important;
            margin: 0 !important;
        }
        .sl-profile-editor--code .sl-code-colour-picker .fi-input-wrp {
            max-width: 220px !important;
        }

        .sl-profile-editor--code .sl-add-form-right .sl-qr-block,
        .sl-profile-editor--code .sl-add-form-right .code_preview_qr,
        .sl-profile-editor--code .sl-add-form-right .code_review { text-align: center !important; }
        .sl-profile-editor--code .sl-add-form-right .sl-qr-image { max-width: 100% !important; height: auto !important; }
        .sl-profile-editor--code .sl-legacy-preview-sidebar--code { padding: 0 !important; }
        .sl-profile-editor--code .sl-legacy-preview-sidebar--code .sl-code-preview-block {
            width: 100% !important; max-width: 100% !important; margin: 0 !important;
        }

        /* Compact centered SAVE — must beat full-width plant/location CTA rules. */
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions .fi-ac,
        .sl-profile-editor--code .sl-add-form-left .fi-form-actions,
        .sl-profile-editor--code .sl-add-form-left .fi-form-actions .fi-ac {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100% !important;
            max-width: none !important;
            margin: 8px 0 0 !important;
            padding: 0 !important;
            text-align: center !important;
        }
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions .fi-btn,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions .fi-btn.fi-color-primary,
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-form-actions button[type="submit"],
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-btn[type="submit"],
        .fi-page:has(.sl-profile-editor--code) .sl-add-form-left .fi-btn.fi-color-primary,
        .sl-profile-editor--code .sl-add-form-left .fi-form-actions .fi-btn,
        .sl-profile-editor--code .sl-add-form-left .fi-form-actions button[type="submit"] {
            display: inline-flex !important;
            width: auto !important;
            min-width: 110px !important;
            max-width: 140px !important;
            min-height: 0 !important;
            height: auto !important;
            margin: 0 auto !important;
            padding: 7px 22px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            flex: 0 0 auto !important;
            text-transform: uppercase !important;
        }

        /* Hide empty wrappers from dehydrated/hidden form fields so they don't add gaps. */
        .sl-profile-editor--code .sl-add-form-left .fi-fo-field:has(input[type="hidden"]):not(:has(input:not([type="hidden"]))),
        .sl-profile-editor--code .sl-add-form-left [style*="display: none"],
        .sl-profile-editor--code .sl-add-form-left .fi-hidden {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
            height: 0 !important;
        }

        @media (max-width: 959px) {
            .sl-profile-editor.sl-profile-editor--code {
                grid-template-columns: minmax(0, 1fr) !important;
                grid-template-areas: "form" "preview" !important;
            }
            .sl-profile-editor.sl-profile-editor--code .sl-add-form-right {
                width: auto !important;
                max-width: none !important;
            }
        }
    </style>
    @endif

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
                    <li class="no-float" style="text-align:center; width:100%; float:none;">
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
                            var inst = CKEDITOR.instances[name];
                            var el = inst.element && inst.element.$;
                            // Never sync from an editor whose element Livewire replaced —
                            // updateElement() would write stale/empty content over the value.
                            if (! el || ! el.isConnected) {
                                return;
                            }
                            inst.updateElement();
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                            el.dispatchEvent(new FocusEvent('blur'));
                        } catch (e) {}
                    });
                }

                // Livewire morphs replace the textarea DOM but leave the CKEditor instance
                // registered. That orphaned instance blocks re-init (looks like a plain box)
                // and clears the field on save. Destroy instances whose element is detached
                // so initLegacyCkEditors can attach a fresh editor to the new DOM.
                function destroyOrphanedEditors() {
                    if (typeof CKEDITOR === 'undefined') {
                        return;
                    }
                    Object.keys(CKEDITOR.instances || {}).forEach(function (name) {
                        try {
                            var inst = CKEDITOR.instances[name];
                            var el = inst.element && inst.element.$;
                            if (! el || ! el.isConnected) {
                                inst.destroy(true);
                            }
                        } catch (e) {
                            try { delete CKEDITOR.instances[name]; } catch (e2) {}
                        }
                    });
                }

                function applyCkTheme(editor) {
                    try {
                        var doc = editor && editor.document && editor.document.$;
                        if (! doc) {
                            return;
                        }
                        var head = doc.head || doc.getElementsByTagName('head')[0];
                        if (! head) {
                            return;
                        }
                        var style = doc.getElementById('sl-ck-theme');
                        if (! style) {
                            style = doc.createElement('style');
                            style.id = 'sl-ck-theme';
                            head.appendChild(style);
                        }
                        style.textContent = document.documentElement.classList.contains('dark')
                            ? 'html,body{background:#111827 !important;color:#e5e7eb !important;}'
                            : '';
                    } catch (e) { /* cross-origin or not ready — ignore */ }
                }

                function applyCkThemeAll() {
                    if (typeof CKEDITOR === 'undefined') {
                        return;
                    }
                    Object.keys(CKEDITOR.instances || {}).forEach(function (name) {
                        applyCkTheme(CKEDITOR.instances[name]);
                    });
                }

                if (! window.__slCkThemeObserver) {
                    window.__slCkThemeObserver = new MutationObserver(applyCkThemeAll);
                    window.__slCkThemeObserver.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class'],
                    });
                }

                function initLegacyCkEditors() {
                    if (typeof CKEDITOR === 'undefined') {
                        return;
                    }
                    destroyOrphanedEditors();
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
                        // Dark mode: CKEditor's editing area is an iframe, so its
                        // body background can't be themed from the page stylesheet.
                        // Inject a style into the (same-origin) iframe instead.
                        editor.on('instanceReady', function () {
                            applyCkTheme(editor);
                        });
                        editor.on('change', function () {
                            editor.updateElement();
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                            el.dispatchEvent(new FocusEvent('blur'));
                        });
                        editor.on('blur', function () {
                            editor.updateElement();
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                            el.dispatchEvent(new FocusEvent('blur'));
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

    @if ($isExhibit || $isVoc)
        @include('filament.portal.profiles.legacy-tile-reorder')
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
                        // While a modal action is open, the CKEditor commit-sync dispatches
                        // synthetic input events; pushing preview drafts then cascades into
                        // extra Livewire requests and makes the modal slow to close. Skip it.
                        if (document.querySelector('.fi-modal-open')) {
                            return;
                        }
                        if (target.type === 'file') {
                            return;
                        }
                        // Colour Selector updates the QR client-side; don't push a phone-preview
                        // draft (Livewire morph would wipe the canvas recolour).
                        if (target.closest && target.closest('.sl-code-colour-picker, .fi-fo-color-picker, hex-color-picker')) {
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
