<style>
    /* Indeterminate top progress bar shown during any Livewire request
       (navigation, filters, pagination, tab switches, table/page actions). */
    #nav-feedback-loader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        z-index: 99999;
        opacity: 0;
        pointer-events: none;
        overflow: hidden;
        background: rgba(0, 122, 1, 0.12);
        transition: opacity 0.18s ease;
    }

    #nav-feedback-loader::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: -35%;
        width: 35%;
        border-radius: 3px;
        background: linear-gradient(90deg, rgba(0, 179, 0, 0) 0%, #00b400 45%, #007a01 80%, rgba(0, 122, 1, 0) 100%);
        box-shadow: 0 0 10px rgba(0, 179, 0, 0.45);
        animation: nav-feedback-slide 1.05s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    #nav-feedback-loader[data-active="true"] {
        opacity: 1;
    }

    body.nav-busy {
        cursor: progress;
    }

    body.nav-busy a.fi-sidebar-item-btn,
    body.nav-busy a.fi-topbar-item-button,
    body.nav-busy a.fi-section {
        cursor: progress !important;
    }

    @keyframes nav-feedback-slide {
        0% { left: -35%; }
        100% { left: 100%; }
    }

    @media (prefers-reduced-motion: reduce) {
        #nav-feedback-loader::before { animation-duration: 2.2s; }
    }

    /* Centred "Please wait" card for slower round-trips (saves, heavy filters,
       page loads) — deliberately non-blocking (pointer-events:none). */
    #nav-feedback-overlay {
        position: fixed;
        inset: 0;
        z-index: 99998;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(17, 24, 39, 0.06);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }

    #nav-feedback-overlay[data-active="true"] {
        opacity: 1;
        visibility: visible;
    }

    #nav-feedback-overlay .nav-feedback-card {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        padding: 15px 26px;
        border-radius: 12px;
        background: rgba(74, 74, 74, 0.94);
        color: #fff;
        font: 500 14px/1.2 ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
        box-shadow: 0 12px 34px rgba(0, 0, 0, 0.28);
        -webkit-backdrop-filter: blur(1.5px);
        backdrop-filter: blur(1.5px);
    }

    #nav-feedback-overlay .nav-feedback-spin {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.32);
        border-top-color: #fff;
        animation: nav-feedback-spin 0.7s linear infinite;
    }

    @keyframes nav-feedback-spin {
        to { transform: rotate(360deg); }
    }
</style>

<div id="nav-feedback-loader" data-active="false" aria-hidden="true"></div>
<div id="nav-feedback-overlay" data-active="false" aria-hidden="true">
    <div class="nav-feedback-card">
        <span class="nav-feedback-spin"></span>
        <span>Please wait&hellip;</span>
    </div>
</div>

<script>
    (() => {
        if (window.__scanlinkNavFeedbackInit) {
            return;
        }
        window.__scanlinkNavFeedbackInit = true;

        const loader = document.getElementById('nav-feedback-loader');
        if (!loader) return;

        // Only reveal the bar once a request is slow enough to notice, so quick
        // interactions don't flicker; a hard cap clears it if a response is lost.
        const BAR_DELAY = 110;    // slim top bar — quick feedback
        const BOX_DELAY = 320;    // centred "Please wait" card — for slower waits
        const MAX_VISIBLE = 15000;

        const overlay = document.getElementById('nav-feedback-overlay');

        let navActive = false;
        let requestCount = 0;
        let barVisible = false;
        let boxVisible = false;
        let barTimer = null;
        let boxTimer = null;
        let maxTimer = null;

        const setBar = (on) => {
            loader.setAttribute('data-active', on ? 'true' : 'false');
            document.body.classList.toggle('nav-busy', on);
        };

        const setBox = (on) => {
            if (overlay) {
                overlay.setAttribute('data-active', on ? 'true' : 'false');
            }
        };

        const forceClear = () => {
            navActive = false;
            requestCount = 0;
            if (barTimer !== null) { clearTimeout(barTimer); barTimer = null; }
            if (boxTimer !== null) { clearTimeout(boxTimer); boxTimer = null; }
            clearTimeout(maxTimer);
            barVisible = false;
            boxVisible = false;
            setBar(false);
            setBox(false);
        };

        const apply = () => {
            const busy = navActive || requestCount > 0;

            if (busy) {
                if (!barVisible && barTimer === null) {
                    barTimer = setTimeout(() => {
                        barTimer = null;
                        barVisible = true;
                        setBar(true);
                        clearTimeout(maxTimer);
                        maxTimer = setTimeout(forceClear, MAX_VISIBLE);
                    }, BAR_DELAY);
                }

                if (!boxVisible && boxTimer === null) {
                    boxTimer = setTimeout(() => {
                        boxTimer = null;
                        boxVisible = true;
                        setBox(true);
                    }, BOX_DELAY);
                }

                return;
            }

            if (barTimer !== null) { clearTimeout(barTimer); barTimer = null; }
            if (boxTimer !== null) { clearTimeout(boxTimer); boxTimer = null; }
            clearTimeout(maxTimer);
            if (barVisible) { barVisible = false; setBar(false); }
            if (boxVisible) { boxVisible = false; setBox(false); }
        };

        // Safety: a real SPA navigation clears on `livewire:navigated`, but if that
        // event never arrives (query-only URL syncs, aborted nav) auto-clear so the
        // loader can never get stuck on.
        let navSafetyTimer = null;

        const navStart = () => {
            navActive = true;
            clearTimeout(navSafetyTimer);
            navSafetyTimer = setTimeout(() => { navActive = false; apply(); }, 10000);
            apply();
        };

        const navEnd = () => {
            navActive = false;
            requestCount = 0;
            clearTimeout(navSafetyTimer);
            apply();
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

        const resolveAnchor = (target) => {
            if (!(target instanceof Element)) {
                return null;
            }

            const el = target.closest('a[href], .fi-sidebar-item-btn, .fi-topbar-item-button');
            if (!el) {
                return null;
            }

            return el instanceof HTMLAnchorElement ? el : el.closest('a');
        };

        const prefetched = new Set();

        // Prefetch on hover so the next click feels instant (Livewire SPA).
        document.addEventListener('pointerover', (event) => {
            const anchor = resolveAnchor(event.target);
            if (!anchor || !isNavigationLink(anchor)) {
                return;
            }

            const href = anchor.href;
            if (prefetched.has(href)) {
                return;
            }

            prefetched.add(href);

            try {
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = href;
                link.as = 'document';
                document.head.appendChild(link);
            } catch (e) {
                // ignore
            }
        }, { passive: true });

        // Immediate feedback on a real page navigation (sidebar/topbar/menu), so the loader
        // shows the instant a link is clicked — even before livewire:navigate fires, and
        // even when the link does a full page load. Crucially, this only triggers when the
        // target PATH differs from the current one: same-page query-string links (tabs,
        // filters, pagination) are Livewire commits and must NOT be flagged here, otherwise
        // livewire:navigated never arrives and the loader would get stuck.
        document.addEventListener('click', (event) => {
            var anchor = resolveAnchor(event.target);
            if (!anchor) return;

            var href = anchor.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

            try {
                var url = new URL(anchor.href, window.location.origin);
                if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                    navStart();
                }
            } catch (e) {
                // ignore malformed href
            }
        }, true);

        document.addEventListener('livewire:navigate', navStart);
        document.addEventListener('livewire:navigating', navStart);
        document.addEventListener('livewire:navigated', navEnd);
        window.addEventListener('pageshow', navEnd);
        window.addEventListener('load', navEnd);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                forceClear();
            }
        });

        const saveListPageState = () => {
            const match = window.location.pathname.match(/^\/admin\/([^/]+)\/?$/);

            if (!match) {
                return;
            }

            sessionStorage.setItem('scanlink:list:' + match[1], window.location.href);
        };

        saveListPageState();
        document.addEventListener('livewire:navigated', saveListPageState);
        window.addEventListener('popstate', saveListPageState);

        // Show the bar for user-driven Livewire round-trips (filters, pagination,
        // tabs, table/page actions) — not background polls like the bell badge.
        const isSilentBackgroundCommit = (component, commit) => {
            const name = String(
                (component && (component.name || component?.snapshot?.memo?.name)) || ''
            ).toLowerCase();

            // Livewire 4 registers Filament\Livewire\DatabaseNotifications (FQCN).
            // Also cover toast Notifications and any kebab aliases.
            if (
                name.indexOf('notification') !== -1
                || name.indexOf('databasenotifications') !== -1
            ) {
                return true;
            }

            try {
                const el = component && (component.el || component.entrypoint);
                if (el && typeof el.closest === 'function' && el.closest('.fi-no-database')) {
                    return true;
                }
            } catch (e) {
                // ignore
            }

            // wire:poll.$interval defaults to $refresh — never treat as user action.
            const calls = commit && commit.calls;
            if (Array.isArray(calls) && calls.length > 0) {
                const onlyRefresh = calls.every((call) => {
                    const method = String(
                        (call && (call.method || call.path || call[0])) || ''
                    );

                    return method === '$refresh' || method === 'refresh';
                });

                if (onlyRefresh) {
                    return true;
                }
            }

            return false;
        };

        const attachCommitHook = () => {
            if (window.__scanlinkNavFeedbackCommitHook || ! window.Livewire || typeof window.Livewire.hook !== 'function') {
                return;
            }

            window.__scanlinkNavFeedbackCommitHook = true;

            window.Livewire.hook('commit', ({ component, commit, succeed, fail }) => {
                if (isSilentBackgroundCommit(component, commit)) {
                    return;
                }

                requestCount++;
                apply();

                succeed(() => {
                    requestCount = Math.max(0, requestCount - 1);
                    apply();
                    queueMicrotask(saveListPageState);
                });

                fail(() => {
                    requestCount = Math.max(0, requestCount - 1);
                    apply();
                });
            });
        };

        // Livewire may already be booted by BODY_END (init already fired).
        attachCommitHook();
        document.addEventListener('livewire:init', attachCommitHook);

        window.scanlinkAdminBack = (button) => {
            const fallbackUrl = button.dataset.fallbackUrl;
            const resource = button.dataset.resource;
            const savedListUrl = resource
                ? sessionStorage.getItem('scanlink:list:' + resource)
                : null;

            const navigate = (url) => {
                if (typeof window.Livewire !== 'undefined' && typeof window.Livewire.navigate === 'function') {
                    window.Livewire.navigate(url);
                    return;
                }

                window.location.href = url;
            };

            if (savedListUrl) {
                try {
                    const saved = new URL(savedListUrl, window.location.origin);
                    const fallback = new URL(fallbackUrl, window.location.origin);

                    if (saved.pathname === fallback.pathname) {
                        navigate(savedListUrl);
                        return;
                    }
                } catch (e) {
                    // ignore malformed stored URL
                }
            }

            try {
                const referrer = new URL(document.referrer, window.location.origin);
                const fallback = new URL(fallbackUrl, window.location.origin);

                if (
                    referrer.origin === window.location.origin
                    && referrer.pathname === fallback.pathname
                ) {
                    window.history.back();
                    return;
                }
            } catch (e) {
                // ignore malformed referrer
            }

            if (window.history.length > 1) {
                window.history.back();
                return;
            }

            navigate(fallbackUrl);
        };
    })();
</script>
