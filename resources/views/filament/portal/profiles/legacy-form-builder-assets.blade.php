<style>
    #iframe_container {
        width: 100%;
        max-width: 100%;
        margin: 0;
        overflow: hidden;
        box-sizing: border-box;
    }
    #iframe_frm_builder {
        display: block;
        width: 100% !important;
        max-width: 100%;
        min-height: 300px;
        border: 0;
        background: #fff;
        overflow: hidden;
    }
    .sl-fb-iframe-placeholder {
        min-height: 200px; display: flex; align-items: center; justify-content: center;
        color: #999; font-size: 13px; font-weight: 600; border: 1px dashed #ccc; margin: 8px 0; padding: 16px;
        box-sizing: border-box;
    }
    .sl-form-builder-panel,
    .form-builder-box.sl-form-builder-panel {
        overflow-x: hidden;
        box-sizing: border-box;
    }
    /* Match legacy .footer-rouded under the builder iframe */
    .sl-fb-expand-footer.footer-rouded,
    .sl-fb-expand-footer {
        display: block;
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 10px;
        box-sizing: border-box;
        background-color: #cccccc;
        border: 0;
        border-radius: 0 0 8px 8px;
        text-align: right;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
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
    /* Legacy code/index.php Code Preview column — equal-width stack */
    .sl-legacy-preview-sidebar--code {
        text-align: center;
        padding: 8px 0 16px;
    }
    .sl-legacy-preview-sidebar--code .sl-code-preview-block {
        width: 220px;
        max-width: 100%;
        margin: 0 auto;
        text-align: left;
    }
    .sl-legacy-preview-sidebar--code .graybar_preview {
        display: block;
        width: 100%;
        height: 35px;
        line-height: 35px;
        margin: 0 0 0;
        padding: 0;
        background-color: #857d7a;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        text-align: center;
        box-sizing: border-box;
    }
    .sl-legacy-preview-sidebar--code .code_preview_qr {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 190px;
        margin: 0;
        border: 1px solid #eeeeee;
        border-top: 0;
        background: #fff;
        box-sizing: border-box;
    }
    .sl-legacy-preview-sidebar--code .code_preview_qr .sl-qr-image {
        display: block;
        width: 185px;
        height: 185px;
    }
    .sl-legacy-preview-sidebar--code .sl-code-preview-empty {
        padding: 24px 16px;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.4;
        text-align: center;
    }
    .sl-legacy-preview-sidebar--code .sl-qr-download-select {
        display: block;
        width: 100% !important;
        max-width: 100%;
        height: 38px;
        margin: 0;
        padding: 6px 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #fff;
        font-size: 13px;
        font-family: Arial, Helvetica, sans-serif;
        color: #555755;
        box-sizing: border-box;
        box-shadow: none;
    }
    .sl-legacy-preview-sidebar--code .sl-qr-download-select:focus {
        border-color: #009401;
        outline: 0;
        box-shadow: 0 0 0 1px rgba(0, 148, 1, .25);
    }
    .sl-legacy-preview-sidebar--code .download_code_as,
    .sl-legacy-preview-sidebar--code .sl-qr-download-btn {
        display: block !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100%;
        margin: 0 !important;
        padding: 0 12px !important;
        height: 42px;
        line-height: 40px;
        text-align: center;
        box-sizing: border-box;
        background: #008901 !important;
        background-image: linear-gradient(to bottom, #008901 0%, #007a01 100%) !important;
        color: #fff !important;
        border: 1px solid #006201 !important;
        border-radius: 6px !important;
        box-shadow: none !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-family: Arial, Helvetica, sans-serif !important;
        font-size: 12px !important;
        cursor: pointer;
    }
    .sl-legacy-preview-sidebar--code .code_review {
        width: 100%;
        margin: 12px 0 0;
        padding: 0 0 4px;
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 10px;
        border: 0;
        box-shadow: none;
    }
    .sl-legacy-preview-sidebar--code .sl-qr-download-btn:disabled,
    .sl-legacy-preview-sidebar--code .sl-qr-download-select:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }
    /* Survey: Form Builder sits in left column */
    .sl-survey-form-builder {
        width: 100%;
        max-width: 460px;
        margin: 12px 0 0;
    }
    .sl-survey-form-builder .sl-fb-expand-footer {
        width: 100%;
        max-width: 440px;
    }
</style>
<script>
    (function () {
        function fbPx(value) {
            var n = parseInt(String(value || '').replace('px', ''), 10);
            return isNaN(n) ? 0 : n;
        }

        function fbInnerDoc(iframe) {
            try {
                return iframe.contentWindow ? iframe.contentWindow.document : null;
            } catch (err) {
                return null;
            }
        }

        /**
         * Size the collapsed iframe to its actual content so there is no large empty
         * white area (the legacy iframe was locked to 1114px). It still GROWS as the
         * user drops elements in — so nothing clips — and shrinks for short forms.
         * Skipped while the user has manually expanded the window.
         */
        function fbFitToContent(iframe) {
            iframe = iframe || document.getElementById('iframe_frm_builder');
            if (! iframe) {
                return;
            }
            var doc = fbInnerDoc(iframe);
            if (! doc || ! doc.body) {
                return;
            }

            // Measure the ACTUAL content bottom, not body.scrollHeight: the form-builder
            // body stretches to the iframe's current height, so scrollHeight reports the
            // (possibly tall) iframe rather than the content — which left white space and
            // stopped "Reduce" from shrinking. The drop area is the last content block, so
            // its bottom edge (the iframe never scrolls) is the true content height.
            var contentH = 0;
            var anchors = [doc.getElementById('div_drop_area'), doc.querySelector('.from-box')];
            for (var i = 0; i < anchors.length; i++) {
                if (! anchors[i]) {
                    continue;
                }
                var bottom = anchors[i].getBoundingClientRect().bottom;
                if (bottom > contentH) {
                    contentH = bottom;
                }
            }
            if (contentH < 80) {
                contentH = doc.body.scrollHeight || 0;
            }
            if (contentH < 80) {
                return;
            }

            var fitH = Math.max(Math.ceil(contentH) + 8, 300);
            if (Math.abs((iframe.offsetHeight || 0) - fitH) > 4) {
                iframe.style.height = fitH + 'px';
                iframe.setAttribute('height', String(fitH));
                var iframeHInput = doc.getElementById('iframe_current_height');
                if (iframeHInput) {
                    iframeHInput.value = String(fitH);
                }
            }
        }

        // Keep the iframe fitted as the user adds/removes elements inside it.
        function fbObserveContent(iframe) {
            var doc = fbInnerDoc(iframe);
            if (! doc || ! doc.body || iframe.__slFitObserved || typeof MutationObserver === 'undefined') {
                return;
            }
            iframe.__slFitObserved = true;
            var scheduled = false;
            var obs = new MutationObserver(function () {
                if (scheduled) {
                    return;
                }
                scheduled = true;
                setTimeout(function () {
                    scheduled = false;
                    fbFitToContent(iframe);
                }, 80);
            });
            obs.observe(doc.body, { childList: true, subtree: true });
        }

        /**
         * Expand/Reduce Window. Default: the drop area is capped (compact) and scrolls
         * for very long forms. "Expand" adds .sl-fb-expanded to lift the cap and reveal
         * the whole form; "Reduce" removes it. The iframe auto-fits to whichever height,
         * so there is never dead empty space.
         */
        function toggleFormBuilderWindow() {
            var iframe = document.getElementById('iframe_frm_builder');
            var label = document.getElementById('sl-expand-reduce-label');
            var img = document.getElementById('expand_reduce_img');
            if (! iframe || ! label) {
                return;
            }

            var innerDoc = fbInnerDoc(iframe);
            var drop = innerDoc
                ? (innerDoc.getElementById('div_drop_area') || innerDoc.querySelector('.ui-widget-content'))
                : null;
            if (! drop) {
                return;
            }

            var expanding = label.textContent.trim().toLowerCase().indexOf('expand') === 0;

            if (expanding) {
                drop.classList.add('sl-fb-expanded');
                label.textContent = 'Reduce Window';
                if (img) {
                    img.src = @json(asset('images/reduce_window.png'));
                    img.alt = 'Reduce Window';
                }
            } else {
                drop.classList.remove('sl-fb-expanded');
                label.textContent = 'Expand Window';
                if (img) {
                    img.src = @json(asset('images/expand_window.png'));
                    img.alt = 'Expand Window';
                }
            }

            // Re-fit the iframe to the new drop-area height.
            fbFitToContent(iframe);
            setTimeout(function () { fbFitToContent(iframe); }, 60);
        }

        // Bind the delegated click listener exactly once, even if this partial is
        // re-evaluated on a Livewire update — otherwise it would stack and double-toggle.
        if (! window.__slFbExpandReduceBound) {
            window.__slFbExpandReduceBound = true;
            document.addEventListener('click', function (e) {
                if (e.target.closest('.sl-fb-expand-footer')) {
                    e.preventDefault();
                    toggleFormBuilderWindow();
                }
            });
        }

        // After iframe load, keep collapsed baseline in sync with legacy hidden fields.
        if (! window.__slFbIframeLoadBound) {
            window.__slFbIframeLoadBound = true;
            document.addEventListener('load', function (e) {
                var t = e.target;
                if (! t || t.id !== 'iframe_frm_builder') {
                    return;
                }
                var doc = fbInnerDoc(t);
                if (! doc) {
                    return;
                }
                var iframeHInput = doc.getElementById('iframe_current_height');
                var dropHInput = doc.getElementById('div_drop_area_current_height');
                var drop = doc.getElementById('div_drop_area');
                // Prefer values written by iframe jQuery init; otherwise seed from DOM.
                if (iframeHInput && ! fbPx(iframeHInput.value)) {
                    iframeHInput.value = String(t.offsetHeight || 1114);
                }
                if (dropHInput && drop && ! fbPx(dropHInput.value)) {
                    dropHInput.value = drop.style.height || (drop.offsetHeight + 'px');
                }
                var label = document.getElementById('sl-expand-reduce-label');
                var img = document.getElementById('expand_reduce_img');
                if (label) {
                    label.textContent = 'Expand Window';
                }
                if (img) {
                    img.src = @json(asset('images/expand_window.png'));
                }

                // Fit the iframe to its content (removes the large empty area) and keep
                // it fitted as elements are dropped in. Retry a few times because the
                // legacy jQuery inside the iframe lays out slightly after 'load'.
                fbFitToContent(t);
                setTimeout(function () { fbFitToContent(t); }, 150);
                setTimeout(function () { fbFitToContent(t); }, 500);
                setTimeout(function () { fbFitToContent(t); }, 1200);
                fbObserveContent(t);
            }, true);
        }

        function closeParticipantModal() {
            var overlay = document.getElementById('sl-participant-modal');
            if (overlay) {
                overlay.remove();
            }
        }

        function openParticipantList() {
            var host = document.getElementById('sl-participant-list-host');
            var url = host && host.getAttribute('data-participants-url');
            if (! url) {
                window.slAlert('Participant list is not available for this code yet. Save the profile first.');
                return;
            }

            closeParticipantModal();

            var overlay = document.createElement('div');
            overlay.id = 'sl-participant-modal';
            overlay.setAttribute('role', 'dialog');
            overlay.setAttribute('aria-modal', 'true');
            overlay.setAttribute('aria-label', 'Add/Edit Participant List');
            overlay.style.cssText = 'position:fixed;inset:0;z-index:100000;background:rgba(15,23,42,.48);display:flex;align-items:center;justify-content:center;padding:24px 16px;backdrop-filter:blur(2px);';

            var dialog = document.createElement('div');
            dialog.className = 'sl-participant-dialog';
            dialog.style.cssText = 'position:relative;width:min(920px,96vw);height:min(86vh,700px);background:' + (document.documentElement.classList.contains('dark') ? 'rgb(17,24,39)' : '#fff') + ';color:' + (document.documentElement.classList.contains('dark') ? 'rgb(243,244,246)' : 'inherit') + ';border-radius:14px;overflow:hidden;box-shadow:0 24px 64px rgba(15,23,42,.28);display:flex;flex-direction:column;border:1px solid rgba(15,23,42,.08);';

            var closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.textContent = '×';
            closeBtn.setAttribute('aria-label', 'Close');
            closeBtn.style.cssText = 'position:absolute;top:14px;right:14px;z-index:3;border:1px solid #e5e7eb;background:#fff;color:#374151;width:32px;height:32px;border-radius:999px;font-size:20px;line-height:28px;cursor:pointer;padding:0;box-shadow:0 1px 2px rgba(0,0,0,.06);';
            closeBtn.addEventListener('mouseenter', function () { closeBtn.style.background = '#f3f4f6'; });
            closeBtn.addEventListener('mouseleave', function () { closeBtn.style.background = '#fff'; });
            closeBtn.addEventListener('click', closeParticipantModal);

            var frame = document.createElement('iframe');
            frame.src = url;
            frame.title = 'Add/Edit Participant List';
            frame.style.cssText = 'display:block;width:100%;height:100%;border:0;background:' + (document.documentElement.classList.contains('dark') ? 'rgb(17,24,39)' : '#fff') + ';flex:1 1 auto;min-height:0;';

            function fitParticipantDialog(contentHeight) {
                var maxH = Math.min(window.innerHeight * 0.9, 760);
                var next = Math.max(460, Math.min(maxH, (contentHeight || 0) + 2));
                dialog.style.height = next + 'px';
            }

            dialog.appendChild(closeBtn);
            dialog.appendChild(frame);
            overlay.appendChild(dialog);
            overlay.addEventListener('click', function (ev) {
                if (ev.target === overlay) {
                    closeParticipantModal();
                }
            });
            overlay._fitParticipantDialog = fitParticipantDialog;
            document.body.appendChild(overlay);
        }

        window.scanlinkOpenParticipantList = openParticipantList;

        window.addEventListener('message', function (event) {
            if (! event.data) {
                return;
            }
            if (event.data.type === 'scanlink-open-participants') {
                openParticipantList();
                return;
            }
            if (event.data.type === 'scanlink-close-participants') {
                closeParticipantModal();
                return;
            }
            if (event.data.type === 'scanlink-participants-height') {
                var modal = document.getElementById('sl-participant-modal');
                if (modal && typeof modal._fitParticipantDialog === 'function') {
                    modal._fitParticipantDialog(Number(event.data.height) || 0);
                }
                return;
            }
            if (event.data.type !== 'scanlink-form-builder-saved') {
                return;
            }
            var preview = document.querySelector('.iphone-preview-container iframe');
            if (! preview || ! preview.src) {
                return;
            }
            try {
                var url = new URL(preview.src, window.location.origin);
                url.searchParams.set('_r', String(Date.now()));
                preview.src = url.toString();
            } catch (err) {
                preview.src = preview.src;
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeParticipantModal();
            }
        });

        /**
         * Legacy plant/edit.php Save: copy Form Name / Email Tag / recipients out of the iframe
         * before the parent save runs.
         */
        function collectFormBuilderIframeMeta() {
            var iframe = document.getElementById('iframe_frm_builder');
            if (! iframe || ! iframe.contentDocument) {
                return { formName: '', emailTag: '', recipients: [], error: null, present: false };
            }

            var doc = iframe.contentDocument;
            var formNameEl = doc.getElementById('form_name') || doc.querySelector('input[name="form_name"]');
            var tagEls = doc.getElementsByName('email_tag');
            var tagEl = null;
            for (var t = 0; t < tagEls.length; t++) {
                if (tagEls[t].tagName === 'INPUT' || tagEls[t].tagName === 'TEXTAREA') {
                    tagEl = tagEls[t];
                    break;
                }
            }

            var recipients = [];
            var emailInputs = doc.getElementsByName('email_recipient[]');
            var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            for (var i = 0; i < emailInputs.length; i++) {
                var val = (emailInputs[i].value || '').replace(/\s+/g, '');
                if (val === '') {
                    continue;
                }
                if (! emailRegex.test(emailInputs[i].value.trim())) {
                    return { formName: '', emailTag: '', recipients: [], error: 'Enter a valid email.', present: true };
                }
                recipients.push(emailInputs[i].value.trim());
            }

            var formName = formNameEl ? (formNameEl.value || '').trim() : '';
            var emailTag = tagEl ? (tagEl.value || '').trim() : '';
            var enable = document.getElementById('enable_form');
            var enabled = enable ? !!enable.checked : false;

            if (enabled) {
                if (formName === '') {
                    return { formName: '', emailTag: '', recipients: [], error: 'Please enter form name', present: true };
                }
                if (recipients.length === 0) {
                    return { formName: '', emailTag: '', recipients: [], error: 'Please enter at least one recipient email', present: true };
                }
            }

            return {
                formName: formName,
                emailTag: emailTag,
                recipients: recipients,
                error: null,
                present: true,
            };
        }

        function findLivewireComponent(el) {
            if (typeof Livewire === 'undefined') {
                return null;
            }
            var root = el.closest('[wire\\:id]');
            if (! root) {
                root = document.querySelector('.sl-profile-editor')?.closest('[wire\\:id]')
                    || document.querySelector('[wire\\:id]');
            }
            if (! root) {
                return null;
            }
            return Livewire.find(root.getAttribute('wire:id'));
        }

        function isProfileSaveButton(btn) {
            if (! btn || btn.tagName !== 'BUTTON') {
                return false;
            }
            if (btn.classList.contains('sl-qr-download-btn') || btn.classList.contains('download_code_as')) {
                return false;
            }
            var text = (btn.textContent || '').replace(/\s+/g, ' ').trim();
            return /^(Save changes|SAVE|Save)$/i.test(text);
        }

        var formBuilderSaveInFlight = false;

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('button');
            if (! isProfileSaveButton(btn)) {
                return;
            }
            if (! document.getElementById('iframe_frm_builder')) {
                return;
            }
            if (formBuilderSaveInFlight) {
                return;
            }

            var meta = collectFormBuilderIframeMeta();
            if (! meta.present) {
                return;
            }
            if (meta.error) {
                e.preventDefault();
                e.stopImmediatePropagation();
                window.slAlert(meta.error);
                return;
            }

            var component = findLivewireComponent(btn);
            if (! component || typeof component.call !== 'function') {
                return;
            }

            e.preventDefault();
            e.stopImmediatePropagation();
            formBuilderSaveInFlight = true;

            component.call('syncFormBuilderIframeMeta', meta.formName, meta.emailTag, meta.recipients)
                .then(function () {
                    return component.call('save');
                })
                .catch(function () {
                    // Validation halt / network — leave user on page
                })
                .finally(function () {
                    formBuilderSaveInFlight = false;
                });
        }, true);
    })();
</script>
