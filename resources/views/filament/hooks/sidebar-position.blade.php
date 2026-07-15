{{-- Sidebar drag-to-resize on the divider wall (not over the nav scrollbar). --}}
<style>
    .fi-sidebar.fi-sidebar-open {
        width: var(--scanlink-sidebar-width, 20rem) !important;
        max-width: none !important;
    }

    .fi-layout {
        position: relative;
    }

    /* Resize handle sits on the sidebar/content wall — never over the scroll track */
    .scanlink-sidebar-grip {
        position: absolute;
        top: 0;
        width: 6px;
        z-index: 25;
        cursor: col-resize;
        touch-action: none;
        user-select: none;
        background: transparent;
        display: none;
    }

    .scanlink-sidebar-grip:hover,
    html.scanlink-sidebar-resizing .scanlink-sidebar-grip {
        background: rgb(156 163 175 / 0.35);
    }

    html.scanlink-sidebar-resizing .scanlink-sidebar-grip {
        background: rgb(107 114 128 / 0.45);
    }

    html.scanlink-sidebar-resizing,
    html.scanlink-sidebar-resizing .scanlink-sidebar-grip {
        cursor: col-resize !important;
        user-select: none !important;
    }

    @media (max-width: 1023px) {
        .scanlink-sidebar-grip {
            display: none !important;
        }
    }
</style>

<script>
    (() => {
        if (window.__scanlinkSidebarResizeInit) {
            return;
        }
        window.__scanlinkSidebarResizeInit = true;

        const WIDTH_KEY = document.body?.classList.contains('fi-panel-portal')
            ? 'scanlink.portal.sidebar.width'
            : 'scanlink.admin.sidebar.width';
        const MIN_W = 240;
        const MAX_W = 520;
        const DEFAULT_W = 300;
        const DESKTOP_BP = 1024;

        const root = document.documentElement;
        root.classList.remove('scanlink-sidebar-right');

        const clamp = (n) => Math.min(MAX_W, Math.max(MIN_W, Math.round(n)));

        const sidebar = () => document.querySelector('.fi-sidebar');
        const layout = () => document.querySelector('.fi-layout');

        const applyWidth = (px) => {
            const widthPx = clamp(px);
            const width = `${widthPx}px`;

            root.style.setProperty('--scanlink-sidebar-width', width);
            root.style.setProperty('--sidebar-width', width);
            document.body?.style.setProperty('--sidebar-width', width);

            document.querySelectorAll('.fi-sidebar, .fi-main-ctn, .fi-layout').forEach((el) => {
                el.style.setProperty('--sidebar-width', width);
            });

            const el = sidebar();
            if (el?.classList.contains('fi-sidebar-open')) {
                el.style.width = width;
            }

            localStorage.setItem(WIDTH_KEY, String(widthPx));
            positionGrip();
        };

        let dragging = false;
        let gripEl = null;

        const positionGrip = () => {
            if (!gripEl) {
                return;
            }

            const sb = sidebar();
            const ly = layout();

            if (
                !sb ||
                !ly ||
                window.innerWidth < DESKTOP_BP ||
                !sb.classList.contains('fi-sidebar-open')
            ) {
                gripEl.style.display = 'none';

                return;
            }

            const sbRect = sb.getBoundingClientRect();
            const lyRect = ly.getBoundingClientRect();

            gripEl.style.display = 'block';
            gripEl.style.left = `${sbRect.right - lyRect.left - 3}px`;
            gripEl.style.height = `${sbRect.height}px`;
        };

        const bindGrip = (grip) => {
            if (grip.dataset.bound === '1') {
                return;
            }

            grip.dataset.bound = '1';

            grip.addEventListener('pointerdown', (event) => {
                if (event.button !== 0) {
                    return;
                }

                dragging = true;
                root.classList.add('scanlink-sidebar-resizing');
                grip.setPointerCapture?.(event.pointerId);
                event.preventDefault();
                event.stopPropagation();
            });
        };

        const ensureGrip = () => {
            const ly = layout();

            if (!ly) {
                return null;
            }

            if (!gripEl) {
                gripEl = ly.querySelector('.scanlink-sidebar-grip');

                if (!gripEl) {
                    gripEl = document.createElement('div');
                    gripEl.className = 'scanlink-sidebar-grip';
                    gripEl.setAttribute('role', 'separator');
                    gripEl.setAttribute('aria-orientation', 'vertical');
                    gripEl.setAttribute('aria-label', 'Drag to resize sidebar');
                    gripEl.title = 'Drag to resize sidebar';
                    ly.appendChild(gripEl);
                    bindGrip(gripEl);
                }
            }

            positionGrip();

            return gripEl;
        };

        document.addEventListener('pointermove', (event) => {
            if (!dragging) {
                return;
            }

            applyWidth(event.clientX);
        });

        const stopDragging = () => {
            if (!dragging) {
                return;
            }

            dragging = false;
            root.classList.remove('scanlink-sidebar-resizing');
        };

        document.addEventListener('pointerup', stopDragging);
        document.addEventListener('pointercancel', stopDragging);

        window.addEventListener('resize', positionGrip);

        const watchSidebar = () => {
            const sb = sidebar();

            if (!sb || sb.dataset.scanlinkWatch === '1') {
                return;
            }

            sb.dataset.scanlinkWatch = '1';

            new MutationObserver(() => {
                const el = sidebar();

                if (el && !el.classList.contains('fi-sidebar-open')) {
                    el.style.removeProperty('width');
                }

                positionGrip();
            }).observe(sb, { attributes: true, attributeFilter: ['class'] });
        };

        const boot = () => {
            const storedW = parseInt(localStorage.getItem(WIDTH_KEY) || '', 10);
            applyWidth(Number.isFinite(storedW) ? storedW : DEFAULT_W);

            requestAnimationFrame(() => {
                ensureGrip();
                watchSidebar();
                setTimeout(() => {
                    ensureGrip();
                    watchSidebar();
                }, 250);
            });
        };

        boot();
        document.addEventListener('livewire:navigated', boot);
    })();
</script>
