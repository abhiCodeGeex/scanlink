{{--
    Mobile/tablet only: add a "Take photo" (camera capture) button to every image
    FileUpload (picture / logo fields), so users can shoot a photo — not only pick one
    from their library. Desktop is left untouched (no camera → a file picker is correct).

    The captured photo is fed into FilePond via its own API, so it uploads exactly like a
    browsed file (Livewire temp upload, preview, validation). Document uploads (no image in
    their accept list) are skipped, matching "wherever there is picture/logo upload".
--}}
<style>
    .sl-take-photo-btn {
        display: flex;
        width: fit-content;
        margin-left: auto;   /* right-align the button under the upload */
        align-items: center;
        gap: .4rem;
        margin-top: .5rem;
        padding: .5rem .9rem;
        font-size: .875rem;
        font-weight: 600;
        line-height: 1.1;
        color: #fff;
        background: #008C00;
        border: 0;
        border-radius: .5rem;
        cursor: pointer;
        transition: background .15s;
    }
    .sl-take-photo-btn:hover { background: #00a000; }
    .dark .sl-take-photo-btn { background: #16a34a; }
    .dark .sl-take-photo-btn:hover { background: #15803d; }
</style>
<script>
(function () {
    function isMobileOrTablet() {
        var ua = navigator.userAgent || '';
        // Client Hints: the browser's own mobile flag (most reliable when present).
        if (navigator.userAgentData && navigator.userAgentData.mobile === true) return true;
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|Tablet|Silk|Kindle/i.test(ua)) return true;
        // iPadOS 13+ masquerades as desktop Safari but reports multi-touch.
        if (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1) return true;
        // Real touch device with a coarse primary pointer and no hover — i.e. a phone or
        // tablet. Guards against touchscreen laptops (which still expose hover / a fine
        // pointer) and desktop Electron shells (which report maxTouchPoints 0).
        return navigator.maxTouchPoints > 0
            && !!window.matchMedia
            && window.matchMedia('(pointer: coarse)').matches
            && window.matchMedia('(hover: none)').matches;
    }
    if (!isMobileOrTablet()) return;

    function enhance(root) {
        if (!root || root.dataset.slCam) return;
        var input = root.querySelector('input[type="file"]');
        var pondRoot = root.querySelector('.filepond--root');
        if (!input || !pondRoot) return;
        // Only picture/logo (image-accepting) uploads get a camera option.
        var accept = (input.getAttribute('accept') || '').toLowerCase();
        if (accept.indexOf('image') === -1) return;
        root.dataset.slCam = '1';

        // Hidden input whose `capture` hint opens the rear camera directly.
        var cam = document.createElement('input');
        cam.type = 'file';
        cam.accept = 'image/*';
        cam.setAttribute('capture', 'environment');
        cam.style.display = 'none';

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'sl-take-photo-btn';
        btn.textContent = '📷 Take photo';

        pondRoot.insertAdjacentElement('afterend', btn);
        btn.insertAdjacentElement('afterend', cam);

        btn.addEventListener('click', function () { cam.click(); });
        cam.addEventListener('change', function () {
            if (!cam.files || !cam.files.length) return;
            var file = cam.files[0];
            var pond = null;
            try { pond = window.FilePond && window.FilePond.find(pondRoot); } catch (e) { /* not ready */ }
            if (pond && typeof pond.addFile === 'function') {
                // Uploads exactly like a browsed file (Livewire temp upload + preview).
                pond.addFile(file);
            } else {
                // Fallback for any non-FilePond input: feed it natively.
                try {
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                } catch (e2) { /* very old browser */ }
            }
            cam.value = '';
        });
    }

    function scan() {
        document.querySelectorAll('.fi-fo-file-upload').forEach(enhance);
    }

    // FileUploads appear on load and also later (repeater items, modals, Livewire swaps).
    var queued = false;
    function queueScan() {
        if (queued) return;
        queued = true;
        setTimeout(function () { queued = false; scan(); }, 150);
    }

    document.addEventListener('livewire:navigated', queueScan);
    document.addEventListener('livewire:update', queueScan);
    if (document.readyState !== 'loading') {
        queueScan();
    } else {
        document.addEventListener('DOMContentLoaded', queueScan);
    }
    // Coalesced observer: catches repeater/modal-injected uploads without rescanning per mutation.
    try {
        new MutationObserver(queueScan).observe(document.body, { childList: true, subtree: true });
    } catch (e) { /* no body yet — the load handlers still cover the initial render */ }
})();
</script>
