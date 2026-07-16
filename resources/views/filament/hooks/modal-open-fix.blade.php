{{-- Ensure Filament action/modals open on the first click (SPA + Alpine race / overlay hit-testing). --}}
<style>
    /* Closed modals must never intercept clicks (covers pre-Alpine / SPA morph gaps). */
    .fi-modal:not(.fi-modal-open) {
        pointer-events: none !important;
    }

    .fi-modal.fi-modal-open {
        pointer-events: auto !important;
    }

    /* Belt-and-suspenders if Alpine has not processed x-cloak yet */
    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    (() => {
        if (window.__scanlinkModalOpenFix) {
            return;
        }
        window.__scanlinkModalOpenFix = true;

        const retriedOpenIds = new Set();

        const isModalOpen = (id) => {
            const el = document.getElementById(id);
            return !!el && el.classList.contains('fi-modal-open');
        };

        const reopenOnce = (id, delayMs) => {
            if (!id || retriedOpenIds.has(id)) {
                return;
            }

            window.setTimeout(() => {
                if (isModalOpen(id)) {
                    return;
                }

                retriedOpenIds.add(id);
                document.dispatchEvent(new CustomEvent('open-modal', {
                    bubbles: true,
                    composed: true,
                    detail: { id },
                }));

                window.setTimeout(() => retriedOpenIds.delete(id), 750);
            }, delayMs);
        };

        // If open-modal fires before Alpine binds the listener, retry once shortly after.
        document.addEventListener('open-modal', (event) => {
            const id = event?.detail?.id;
            reopenOnce(id, 60);
        });

        // After Livewire mounts an action, Filament syncs modals — re-check open state.
        window.addEventListener('sync-action-modals', (event) => {
            const livewireId = event?.detail?.id;
            const index = event?.detail?.newActionNestingIndex;

            if (livewireId == null || index == null) {
                return;
            }

            reopenOnce(`fi-${livewireId}-action-${index}`, 100);
        });

        // After SPA navigations, clear any leftover "eat next click" handlers Filament
        // may have registered for text-selection close prevention.
        const clearStaleClickGuards = () => {
            // Dispatch a no-op capture click that Filament's one-shot preventer will
            // consume and self-remove if present — but only when no modal is open.
            if (document.querySelector('.fi-modal.fi-modal-open')) {
                return;
            }

            // Soft nudge: ensure closed modals cannot sit above content.
            document.querySelectorAll('.fi-modal:not(.fi-modal-open)').forEach((el) => {
                el.style.pointerEvents = 'none';
            });
        };

        document.addEventListener('livewire:navigated', clearStaleClickGuards);
        document.addEventListener('livewire:init', clearStaleClickGuards);
        window.addEventListener('load', clearStaleClickGuards);
    })();
</script>
