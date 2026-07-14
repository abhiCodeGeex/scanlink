<style>
    #nav-feedback-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.12s ease;
        background: linear-gradient(90deg, transparent, rgb(59 130 246), transparent);
        background-size: 200% 100%;
    }

    #nav-feedback-loader[data-active="true"] {
        opacity: 1;
        animation: nav-feedback-shimmer 0.9s linear infinite;
    }

    body.nav-busy {
        cursor: progress;
    }

    body.nav-busy a.fi-sidebar-item-btn,
    body.nav-busy a.fi-topbar-item-button,
    body.nav-busy a.fi-section {
        cursor: progress !important;
        opacity: 0.85;
    }

    @keyframes nav-feedback-shimmer {
        from { background-position: 200% 0; }
        to { background-position: -200% 0; }
    }
</style>

<div id="nav-feedback-loader" data-active="false" aria-hidden="true"></div>

<script>
    (() => {
        const loader = document.getElementById('nav-feedback-loader');
        if (!loader) return;

        let timeoutId = null;

        const setBusy = (busy) => {
            loader.setAttribute('data-active', busy ? 'true' : 'false');
            document.body.classList.toggle('nav-busy', busy);
        };

        const start = () => {
            clearTimeout(timeoutId);
            setBusy(true);
            timeoutId = setTimeout(() => setBusy(false), 8000);
        };

        const stop = () => {
            clearTimeout(timeoutId);
            setBusy(false);
        };

        const isNavigationLink = (el) => {
            if (!(el instanceof HTMLAnchorElement)) {
                return false;
            }

            const href = el.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return false;
            }

            try {
                const url = new URL(href, window.location.origin);
                return url.origin === window.location.origin && url.href !== window.location.href;
            } catch (e) {
                return false;
            }
        };

        document.addEventListener('click', (event) => {
            const target = event.target instanceof Element
                ? event.target.closest('a[href], .fi-sidebar-item-btn, .fi-topbar-item-button')
                : null;

            if (!target || !isNavigationLink(target instanceof HTMLAnchorElement ? target : target.closest('a'))) {
                return;
            }

            start();
        }, true);

        document.addEventListener('livewire:navigate', start);
        document.addEventListener('livewire:navigating', start);
        document.addEventListener('livewire:navigated', stop);
        document.addEventListener('livewire:navigated', stop);
        window.addEventListener('pageshow', stop);
        window.addEventListener('load', stop);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                stop();
            }
        });
    })();
</script>
