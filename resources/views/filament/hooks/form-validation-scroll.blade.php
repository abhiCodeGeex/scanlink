{{-- Scroll to first invalid field and expand collapsed sections after save/validation errors. --}}
<style>
    .fi-fo-field:has([data-validation-error]) .fi-input-wrp,
    .fi-fo-field:has([data-validation-error]) .fi-input,
    .fi-fo-field:has([data-validation-error]) .fi-select-input,
    .fi-fo-field:has([data-validation-error]) .fi-textarea {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 1px rgb(220 38 38 / 0.35) !important;
    }

    .fi-fo-field:has([data-validation-error]) .fi-fo-field-wrp-label {
        color: #dc2626 !important;
    }

    .fi-fo-field-wrp-error-message {
        color: #dc2626 !important;
        font-weight: 600;
    }
</style>

<script>
    (() => {
        if (window.__scanlinkFormValidationScroll) {
            return;
        }
        window.__scanlinkFormValidationScroll = true;

        const expandAncestors = (element) => {
            let node = element?.parentElement;

            while (node) {
                if (node.classList?.contains('fi-section') || node.classList?.contains('fi-sc-section')) {
                    node.dispatchEvent(new CustomEvent('expand', { bubbles: true }));
                }

                node = node.parentElement;
            }
        };

        const scrollToFirstValidationError = () => {
            const errorNode = document.querySelector('[data-validation-error]');

            if (! errorNode) {
                return;
            }

            const field = errorNode.closest('[data-field-wrapper]') ?? errorNode;

            expandAncestors(field);

            window.setTimeout(() => {
                field.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });

                const focusable = field.querySelector(
                    'input:not([type="hidden"]):not([disabled]), textarea:not([disabled]), select:not([disabled]), [contenteditable="true"]',
                );

                focusable?.focus({ preventScroll: true });
            }, 200);
        };

        const scheduleScroll = () => window.setTimeout(scrollToFirstValidationError, 80);

        document.addEventListener('livewire:init', () => {
            window.Livewire.hook('commit', ({ succeed, fail }) => {
                succeed(({ payload }) => {
                    if (payload?.effects?.errors && Object.keys(payload.effects.errors).length > 0) {
                        scheduleScroll();
                    }
                });

                fail(() => scheduleScroll());
            });
        });

        document.addEventListener('livewire:navigated', scheduleScroll);
    })();
</script>
