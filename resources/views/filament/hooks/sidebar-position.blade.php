{{-- Sidebar drag-to-resize grip on the sidebar edge. --}}
<style>
    .fi-sidebar.fi-sidebar-open {
        width: var(--scanlink-sidebar-width, 20rem) !important;
        max-width: none !important;
    }

    .scanlink-sidebar-grip {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 10px;
        z-index: 60;
        cursor: col-resize;
        touch-action: none;
        user-select: none;
        background: transparent;
    }

    .scanlink-sidebar-grip::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 4px;
        height: 48px;
        border-radius: 999px;
        background: #008c00;
        opacity: 0.55;
        box-shadow: 0 0 0 1px rgb(255 255 255 / 0.8);
        pointer-events: none;
        transition: opacity 0.15s ease, height 0.15s ease;
    }

    .scanlink-sidebar-grip:hover::after,
    html.scanlink-sidebar-resizing .scanlink-sidebar-grip::after {
        opacity: 1;
        height: 72px;
    }

    html.scanlink-sidebar-resizing,
    html.scanlink-sidebar-resizing * {
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

        const WIDTH_KEY = 'scanlink.sidebar.width';
        const MIN_W = 220;
        const MAX_W = 520;
        const DEFAULT_W = 320;

        const root = document.documentElement;
        root.classList.remove('scanlink-sidebar-right');

        const clamp = (n) => Math.min(MAX_W, Math.max(MIN_W, Math.round(n)));

        const applyWidth = (px) => {
            const widthPx = clamp(px);
            const width = `${widthPx}px`;

            root.style.setProperty('--scanlink-sidebar-width', width);
            root.style.setProperty('--sidebar-width', width);
            document.body?.style.setProperty('--sidebar-width', width);

            document.querySelectorAll('.fi-sidebar, .fi-main-ctn, .fi-layout').forEach((el) => {
                el.style.setProperty('--sidebar-width', width);
            });

            const sidebar = document.querySelector('.fi-sidebar');
            if (sidebar?.classList.contains('fi-sidebar-open')) {
                sidebar.style.width = width;
            }

            localStorage.setItem(WIDTH_KEY, String(widthPx));
        };

        let dragging = false;

        const bindGrip = (grip) => {
            if (grip.dataset.bound === '1') return;
            grip.dataset.bound = '1';

            grip.addEventListener('pointerdown', (event) => {
                if (event.button !== 0) return;
                dragging = true;
                root.classList.add('scanlink-sidebar-resizing');
                grip.setPointerCapture?.(event.pointerId);
                event.preventDefault();
                event.stopPropagation();
            });
        };

        const ensureGrip = () => {
            const sidebar = document.querySelector('.fi-sidebar');
            if (!sidebar) return null;

            let grip = sidebar.querySelector('.scanlink-sidebar-grip');
            if (!grip) {
                if (getComputedStyle(sidebar).position === 'static') {
                    sidebar.style.position = 'relative';
                }
                grip = document.createElement('div');
                grip.className = 'scanlink-sidebar-grip';
                grip.setAttribute('role', 'separator');
                grip.setAttribute('aria-orientation', 'vertical');
                grip.setAttribute('aria-label', 'Drag to resize sidebar');
                grip.title = 'Drag to resize sidebar';
                sidebar.appendChild(grip);
                bindGrip(grip);
            }
            return grip;
        };

        document.addEventListener('pointermove', (event) => {
            if (!dragging) return;
            applyWidth(event.clientX);
        });

        document.addEventListener('pointerup', () => {
            if (!dragging) return;
            dragging = false;
            root.classList.remove('scanlink-sidebar-resizing');
        });

        document.addEventListener('pointercancel', () => {
            dragging = false;
            root.classList.remove('scanlink-sidebar-resizing');
        });

        const boot = () => {
            const storedW = parseInt(localStorage.getItem(WIDTH_KEY) || '', 10);
            applyWidth(Number.isFinite(storedW) ? storedW : DEFAULT_W);
            // Delay grip insert so Livewire/Alpine can finish mounting first.
            requestAnimationFrame(() => {
                ensureGrip();
                setTimeout(ensureGrip, 250);
            });
        };

        boot();
        document.addEventListener('livewire:navigated', boot);
    })();
</script>
