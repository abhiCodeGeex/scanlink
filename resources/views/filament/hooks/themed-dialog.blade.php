{{-- Themed confirm/alert dialog to replace native window.confirm()/alert() and
     Livewire wire:confirm across the portal. Exposes:
       window.slConfirm(message)  -> Promise<boolean>
       window.slAlert(message)    -> Promise<void>
     Self-contained (creates its own DOM) and SPA-safe (re-attaches on navigate). --}}
<script>
    (function () {
        if (window.slConfirm) { return; }

        var overlay, msgEl, okBtn, cancelBtn, resolver;

        function build() {
            overlay = document.createElement('div');
            overlay.className = 'sl-dialog-overlay';
            overlay.setAttribute('hidden', '');
            overlay.innerHTML =
                '<div class="sl-dialog" role="alertdialog" aria-modal="true" aria-live="assertive">'
              + '<p class="sl-dialog-msg"></p>'
              + '<div class="sl-dialog-actions">'
              + '<button type="button" class="sl-dialog-btn sl-dialog-btn--ghost" data-sl-cancel>Cancel</button>'
              + '<button type="button" class="sl-dialog-btn sl-dialog-btn--primary" data-sl-ok>OK</button>'
              + '</div></div>';
            document.body.appendChild(overlay);
            msgEl = overlay.querySelector('.sl-dialog-msg');
            okBtn = overlay.querySelector('[data-sl-ok]');
            cancelBtn = overlay.querySelector('[data-sl-cancel]');
            okBtn.addEventListener('click', function () { settle(true); });
            cancelBtn.addEventListener('click', function () { settle(false); });
            overlay.addEventListener('click', function (e) { if (e.target === overlay) { settle(false); } });
        }

        function ensure() {
            if (!overlay || !document.body.contains(overlay)) { build(); }
        }

        function settle(result) {
            if (overlay) { overlay.setAttribute('hidden', ''); }
            var r = resolver;
            resolver = null;
            if (r) { r(result); }
        }

        function open(message, isConfirm) {
            ensure();
            msgEl.textContent = message == null ? '' : String(message);
            cancelBtn.style.display = isConfirm ? '' : 'none';
            overlay.removeAttribute('hidden');
            setTimeout(function () { try { okBtn.focus(); } catch (e) {} }, 20);
            return new Promise(function (resolve) { resolver = resolve; });
        }

        document.addEventListener('keydown', function (e) {
            if (!overlay || overlay.hasAttribute('hidden')) { return; }
            if (e.key === 'Escape') { e.preventDefault(); settle(false); }
            else if (e.key === 'Enter') { e.preventDefault(); settle(true); }
        });

        window.slConfirm = function (message) { return open(message, true); };
        window.slAlert = function (message) { return open(message, false); };
    })();
</script>
