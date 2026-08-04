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
    .sl-fb-expand-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: #ccc;
        box-sizing: border-box;
        cursor: pointer;
        user-select: none;
    }
    .sl-fb-expand-footer .expand-reduce {
        cursor: pointer;
    }
    /* Legacy code/index.php Code Preview column */
    .sl-legacy-preview-sidebar--code {
        text-align: center;
        padding-top: 8px;
    }
    .sl-legacy-preview-sidebar--code .graybar_preview {
        display: block;
        width: 220px;
        max-width: 100%;
        height: 35px;
        line-height: 35px;
        margin: 12px auto 3px;
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
        width: 220px;
        max-width: 100%;
        min-height: 190px;
        margin: 0 auto;
        border: 1px solid #eeeeee;
        background: #fff;
        box-sizing: border-box;
    }
    .sl-legacy-preview-sidebar--code .code_preview_qr .sl-qr-image {
        display: block;
        width: 185px;
        height: 185px;
    }
    .sl-legacy-preview-sidebar--code .code_review {
        width: 220px;
        max-width: 100%;
        margin: 12px auto 0;
        text-align: left;
    }
    .sl-legacy-preview-sidebar--code .sl-qr-actions {
        display: block;
        margin: 0;
        width: 100%;
    }
    .sl-legacy-preview-sidebar--code .sl-qr-download-select {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    .sl-legacy-preview-sidebar--code .download_div {
        margin: 10px 0 0;
        width: 100%;
        text-align: center;
    }
    .sl-legacy-preview-sidebar--code .download_code_as,
    .sl-legacy-preview-sidebar--code .sl-qr-download-btn {
        display: inline-block;
        width: auto;
        min-width: 140px;
        margin: 0 auto;
        padding: 8px 18px;
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
            var n = parseInt(value, 10);
            return isNaN(n) ? 0 : n;
        }

        // State is read from the button LABEL (the visible source of truth) and the
        // collapsed heights are cached on the elements themselves — never in a JS closure,
        // which would desync from the DOM whenever Livewire re-renders the panel or the
        // form-builder iframe reloads after a save. That desync was the "not working" bug.
        function toggleFormBuilderWindow() {
            var iframe = document.getElementById('iframe_frm_builder');
            var label = document.getElementById('sl-expand-reduce-label');
            var img = document.getElementById('expand_reduce_img');
            if (! iframe || ! label) {
                return;
            }

            // Inner canvas id varies across the ported markup ('div_drop_area' / 'drop_area').
            var innerDoc = null;
            try {
                innerDoc = iframe.contentWindow ? iframe.contentWindow.document : null;
            } catch (err) {
                innerDoc = null;
            }
            var drop = innerDoc
                ? (innerDoc.getElementById('div_drop_area') || innerDoc.getElementById('drop_area'))
                : null;

            var expanding = label.textContent.trim().toLowerCase().indexOf('expand') === 0;

            if (expanding) {
                var curIframe = iframe.offsetHeight || 1114;
                iframe.setAttribute('data-fb-collapsed-h', curIframe);
                iframe.style.height = Math.max(curIframe * 2, 1800) + 'px';

                if (drop) {
                    var curDrop = fbPx(drop.style.height) || drop.offsetHeight || 900;
                    drop.setAttribute('data-fb-collapsed-h', curDrop);
                    drop.style.height = Math.max(curDrop * 2, 1600) + 'px';
                }

                label.textContent = 'Reduce Window';
                if (img) {
                    img.src = @json(asset('images/reduce_window.png'));
                }
            } else {
                iframe.style.height = (fbPx(iframe.getAttribute('data-fb-collapsed-h')) || 1114) + 'px';

                if (drop) {
                    drop.style.height = (fbPx(drop.getAttribute('data-fb-collapsed-h')) || 900) + 'px';
                }

                label.textContent = 'Expand Window';
                if (img) {
                    img.src = @json(asset('images/expand_window.png'));
                }
            }
        }

        // Bind the delegated click listener exactly once, even if this partial is
        // re-evaluated on a Livewire update — otherwise it would stack and double-toggle.
        if (! window.__slFbExpandReduceBound) {
            window.__slFbExpandReduceBound = true;
            document.addEventListener('click', function (e) {
                if (e.target.closest('.sl-fb-expand-footer, .expand-reduce')) {
                    e.preventDefault();
                    toggleFormBuilderWindow();
                }
            });
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
                alert('Participant list is not available for this code yet. Save the profile first.');
                return;
            }

            closeParticipantModal();

            var overlay = document.createElement('div');
            overlay.id = 'sl-participant-modal';
            overlay.setAttribute('role', 'dialog');
            overlay.setAttribute('aria-modal', 'true');
            overlay.setAttribute('aria-label', 'Add/Edit Participant List');
            overlay.style.cssText = 'position:fixed;inset:0;z-index:100000;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;padding:20px 16px;';

            var dialog = document.createElement('div');
            dialog.className = 'sl-participant-dialog';
            dialog.style.cssText = 'position:relative;width:min(920px,96vw);height:min(82vh,640px);background:#fff;border-radius:6px;overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,.35);display:flex;flex-direction:column;';

            var closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.textContent = '×';
            closeBtn.setAttribute('aria-label', 'Close');
            closeBtn.style.cssText = 'position:absolute;top:12px;right:14px;z-index:3;border:0;background:#222;color:#fff;width:28px;height:28px;border-radius:14px;font-size:18px;line-height:26px;cursor:pointer;padding:0;';
            closeBtn.addEventListener('click', closeParticipantModal);

            var frame = document.createElement('iframe');
            frame.src = url;
            frame.title = 'Add/Edit Participant List';
            frame.style.cssText = 'display:block;width:100%;height:100%;border:0;background:#fff;flex:1 1 auto;min-height:0;';

            function fitParticipantDialog(contentHeight) {
                var maxH = Math.min(window.innerHeight * 0.82, 720);
                var next = Math.max(420, Math.min(maxH, (contentHeight || 0) + 2));
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
                alert(meta.error);
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
