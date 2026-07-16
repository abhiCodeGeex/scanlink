<script>
    (() => {
        if (window.__scanlinkClientUsersScrollInit) {
            return;
        }
        window.__scanlinkClientUsersScrollInit = true;

        let usersScrollTop = null;
        let restoreUntil = 0;

        window.scanlinkScrollToClientUsers = () => {
            const section = document.querySelector('.fi-resource-relation-manager');

            if (! section) {
                return;
            }

            usersScrollTop = Math.max(0, section.getBoundingClientRect().top + window.scrollY - 80);
            window.scrollTo({ top: usersScrollTop, behavior: 'smooth' });
        };

        window.scanlinkStickOnClientUsers = () => {
            const section = document.querySelector('.fi-resource-relation-manager');

            if (! section) {
                return;
            }

            usersScrollTop = Math.max(0, section.getBoundingClientRect().top + window.scrollY - 80);
            restoreUntil = Date.now() + 2000;
            scheduleRestore();
        };

        const restoreUsersScroll = () => {
            if (usersScrollTop == null || Date.now() > restoreUntil) {
                return;
            }

            requestAnimationFrame(() => {
                if (document.activeElement instanceof HTMLElement) {
                    document.activeElement.blur();
                }

                window.scrollTo({ top: usersScrollTop, behavior: 'instant' });
            });
        };

        const scheduleRestore = () => {
            restoreUntil = Date.now() + 600;
            restoreUsersScroll();
            window.setTimeout(restoreUsersScroll, 0);
            window.setTimeout(restoreUsersScroll, 50);
            window.setTimeout(restoreUsersScroll, 150);
        };

        window.addEventListener('close-modal', scheduleRestore);
        document.addEventListener('close-modal', scheduleRestore);

        document.addEventListener('livewire:init', () => {
            window.Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    if (Date.now() <= restoreUntil) {
                        queueMicrotask(restoreUsersScroll);
                    }
                });
            });
        });

        document.addEventListener('livewire:navigated', () => {
            usersScrollTop = null;
            restoreUntil = 0;
        });
    })();
</script>
