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
        var collapsedIframeHeight = null;
        var collapsedDropHeight = null;

        function toggleFormBuilderWindow() {
            var iframe = document.getElementById('iframe_frm_builder');
            var label = document.getElementById('sl-expand-reduce-label');
            var img = document.getElementById('expand_reduce_img');
            if (! iframe) {
                return;
            }

            var drop = iframe.contentWindow && iframe.contentWindow.document
                ? iframe.contentWindow.document.getElementById('drop')
                : null;

            if (collapsedIframeHeight === null) {
                collapsedIframeHeight = iframe.offsetHeight || 1114;
                collapsedDropHeight = drop ? (drop.offsetHeight || 900) : 900;
                iframe.style.height = Math.max(collapsedIframeHeight * 2, 1800) + 'px';
                if (drop) {
                    drop.style.height = Math.max(collapsedDropHeight * 2, 1600) + 'px';
                }
                if (label) {
                    label.textContent = 'Reduce Window';
                }
                if (img) {
                    img.src = @json(asset('images/reduce_window.png'));
                }
            } else {
                iframe.style.height = collapsedIframeHeight + 'px';
                if (drop) {
                    drop.style.height = collapsedDropHeight + 'px';
                }
                collapsedIframeHeight = null;
                collapsedDropHeight = null;
                if (label) {
                    label.textContent = 'Expand Window';
                }
                if (img) {
                    img.src = @json(asset('images/expand_window.png'));
                }
            }
        }

        document.addEventListener('click', function (e) {
            if (e.target.closest('.sl-fb-expand-footer, .expand-reduce')) {
                e.preventDefault();
                toggleFormBuilderWindow();
            }
        });

        window.addEventListener('message', function (event) {
            if (! event.data || event.data.type !== 'scanlink-form-builder-saved') {
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
    })();
</script>
