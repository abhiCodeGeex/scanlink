<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Form Builder' }}</title>
    @filamentStyles
    @livewireStyles
    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
        }
        *, *::before, *::after { box-sizing: border-box !important; }
        /* Neutralize Filament/Livewire chrome in embed iframe */
        .fi-body, .fi-simple-layout, .fi-simple-main, .fi-page,
        .fi-page-main, .fi-page-content, .fi-main, .fi-main-ctn,
        [wire\:id] {
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            overflow-x: hidden !important;
            box-shadow: none !important;
            background: transparent !important;
            border: 0 !important;
        }
    </style>
</head>
<body>
    {{ $slot }}

    @livewireScripts
    @filamentScripts

    {{-- Document-level DnD (survives Livewire morph; @script is unreliable in this embed layout) --}}
    <script>
        (function () {
            if (window.__slFbDndBound) return;
            window.__slFbDndBound = true;
            window.__slFbTypeId = 0;
            window.__slFbDragging = false;

            function wireFrom(el) {
                // Prefer closest ancestor; fall back to FormBuilder page root (embed has one component).
                let root = el?.closest?.('[wire\\:id]');
                if (!root) {
                    root = document.querySelector('[wire\\:name="App\\Filament\\Portal\\Pages\\FormBuilder"]')
                        || document.querySelector('.fb-embed-wrap[wire\\:id], [wire\\:id]');
                }
                if (!root || !window.Livewire) return null;
                try {
                    return Livewire.find(root.getAttribute('wire:id'));
                } catch (e) {
                    return null;
                }
            }

            function callQuickAdd(component, typeId) {
                if (!component || !typeId) return;
                try {
                    if (typeof component.call === 'function') {
                        component.call('quickAdd', typeId);
                        return;
                    }
                } catch (e) {}
                try {
                    if (component.$wire && typeof component.$wire.quickAdd === 'function') {
                        component.$wire.quickAdd(typeId);
                        return;
                    }
                } catch (e) {}
                try {
                    // Livewire v3 entangle-style proxy
                    component.quickAdd?.(typeId);
                } catch (e) {}
            }

            function parseTypeId(raw) {
                const n = parseInt(raw || '0', 10);
                return Number.isFinite(n) && n > 0 ? n : 0;
            }

            document.addEventListener('dragstart', function (e) {
                const item = e.target.closest?.('.fb-palette-item');
                if (!item) return;
                window.__slFbDragging = true;
                window.__slFbTypeId = parseTypeId(item.getAttribute('data-type-id'));
                try {
                    e.dataTransfer.setData('text/plain', String(window.__slFbTypeId));
                    e.dataTransfer.setData('text', String(window.__slFbTypeId));
                    e.dataTransfer.effectAllowed = 'copy';
                } catch (err) {}
                item.classList.add('is-dragging');
            }, true);

            document.addEventListener('dragend', function (e) {
                const item = e.target.closest?.('.fb-palette-item');
                if (item) item.classList.remove('is-dragging');
                setTimeout(function () { window.__slFbDragging = false; }, 250);
            }, true);

            document.addEventListener('dragover', function (e) {
                const zone = e.target.closest?.('#fb-canvas-drop-zone');
                if (!zone) return;
                e.preventDefault();
                try { e.dataTransfer.dropEffect = 'copy'; } catch (err) {}
                zone.classList.add('is-over');
            }, true);

            document.addEventListener('dragleave', function (e) {
                const zone = e.target.closest?.('#fb-canvas-drop-zone');
                if (!zone) return;
                if (!zone.contains(e.relatedTarget)) {
                    zone.classList.remove('is-over');
                }
            }, true);

            document.addEventListener('drop', function (e) {
                const zone = e.target.closest?.('#fb-canvas-drop-zone');
                if (!zone) return;
                e.preventDefault();
                e.stopPropagation();
                zone.classList.remove('is-over');
                window.__slFbDragging = true;

                let typeId = 0;
                try {
                    typeId = parseTypeId(e.dataTransfer.getData('text/plain') || e.dataTransfer.getData('text'));
                } catch (err) {}
                if (!typeId) typeId = window.__slFbTypeId;

                const component = wireFrom(zone);
                if (typeId && component) {
                    callQuickAdd(component, typeId);
                } else if (typeId && window.Livewire) {
                    // Last resort: first component on the page
                    const first = document.querySelector('[wire\\:id]');
                    if (first) {
                        try { callQuickAdd(Livewire.find(first.getAttribute('wire:id')), typeId); } catch (e) {}
                    }
                }

                setTimeout(function () { window.__slFbDragging = false; }, 250);
            }, true);

            document.addEventListener('click', function (e) {
                const item = e.target.closest?.('.fb-palette-item');
                if (!item) return;
                if (window.__slFbDragging) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                // Prefer Livewire wire:click when present; this is a fallback.
                if (item.hasAttribute('wire:click')) return;
                const typeId = parseTypeId(item.getAttribute('data-type-id'));
                const component = wireFrom(item);
                if (typeId && component) {
                    e.preventDefault();
                    callQuickAdd(component, typeId);
                }
            }, true);
        })();
    </script>
</body>
</html>
