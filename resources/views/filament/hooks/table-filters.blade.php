<script>
    (() => {
        if (window.__scanlinkTableFiltersInit) {
            return;
        }
        window.__scanlinkTableFiltersInit = true;

        const closeFilterDropdowns = () => {
            document.querySelectorAll('.fi-dropdown.fi-active').forEach((dropdown) => {
                dropdown.dispatchEvent(new CustomEvent('close-dropdown', { bubbles: true }));
            });

            document.querySelectorAll('[x-data]').forEach((element) => {
                if (element.classList.contains('fi-active') && element.querySelector('.fi-tables-filters-form')) {
                    element.dispatchEvent(new CustomEvent('close-dropdown', { bubbles: true }));
                }
            });

            document.dispatchEvent(new KeyboardEvent('keydown', {
                key: 'Escape',
                code: 'Escape',
                bubbles: true,
            }));
        };

        const enhanceFilterForms = () => {
            document.querySelectorAll('.fi-tables-filters-form').forEach((form) => {
                const actions = form.querySelector('.fi-sc-actions, .fi-fo-actions, .fi-form-actions');

                if (! actions || actions.querySelector('[data-scanlink-filter-cancel]')) {
                    return;
                }

                const cancel = document.createElement('button');
                cancel.type = 'button';
                cancel.textContent = 'Cancel';
                cancel.className = 'fi-btn fi-color fi-color-gray fi-btn-color-gray fi-size-md';
                cancel.dataset.scanlinkFilterCancel = 'true';
                cancel.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    closeFilterDropdowns();
                });

                actions.insertBefore(cancel, actions.firstChild);
            });
        };

        document.addEventListener('click', (event) => {
            const target = event.target instanceof Element ? event.target : null;
            const applyButton = target?.closest('[data-scanlink-apply-filters], button');

            if (! applyButton) {
                return;
            }

            const label = applyButton.textContent?.trim().toLowerCase() ?? '';

            if (label.includes('apply filter')) {
                window.setTimeout(closeFilterDropdowns, 150);
            }
        }, true);

        const boot = () => {
            enhanceFilterForms();
        };

        boot();
        document.addEventListener('livewire:navigated', boot);
        document.addEventListener('livewire:init', () => {
            window.Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    queueMicrotask(boot);
                });
            });
        });
    })();
</script>
