{{--
    Legacy parity: exhibit/voc content sections are drag-reorderable by a handle in the
    top-right of each section (legacy exhibit/edit.php: jQuery UI sortable on `.sortables`
    saving `order-id`s to `#hdn_order`). Here each reorderable Section carries
    `data-tile-id` + `.sl-sortable-tile`; dragging updates the hidden `tiles_order` field,
    which saves to profiles.tiles_order — the same order the scanned mobile page renders.

    Filament renders sections in fixed schema order and Livewire morph re-imposes that DOM
    order on every commit, so we DON'T move DOM nodes (that would also reload the CKEditor
    iframes). Instead we reorder visually with the CSS `order` property on the grid items and
    re-apply after every morph. Pointer capture keeps the drag alive over the CKEditor iframes.
--}}
<style>
    .sl-profile-editor--exhibit .sl-sortable-tile,
    .sl-profile-editor--voc .sl-sortable-tile {
        position: relative;
    }

    .sl-tile-drag {
        position: absolute;
        top: 6px;
        right: 8px;
        z-index: 6;
        width: 28px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: grab;
        color: #9aa0a6;
        border-radius: 5px;
        border: 1px solid transparent;
        background: transparent;
        touch-action: none;
        -webkit-user-select: none;
        user-select: none;
    }
    .sl-tile-drag:hover {
        color: #008C00;
        background: rgba(0, 140, 0, .08);
        border-color: rgba(0, 140, 0, .25);
    }
    .sl-tile-drag:active { cursor: grabbing; }
    .sl-tile-drag svg { display: block; pointer-events: none; }

    .sl-tile-dragging {
        outline: 2px dashed #008C00 !important;
        outline-offset: -2px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .12) !important;
    }
    body.sl-tiles-reordering { cursor: grabbing !important; }
    body.sl-tiles-reordering * { -webkit-user-select: none !important; user-select: none !important; }

    /* Dark mode handle */
    html.dark .sl-tile-drag { color: #7c8794; }
    html.dark .sl-tile-drag:hover { color: #34d058; background: rgba(52, 208, 88, .12); border-color: rgba(52, 208, 88, .28); }

    /* Keep the hidden tiles_order field from taking layout space (its grid cell is
       collapsed from JS in hideOrderField(); this hides the field element itself). */
    .sl-profile-editor .sl-tiles-order-field { display: none !important; }
</style>

<script>
    (function () {
        var GRIP = '<svg width="12" height="18" viewBox="0 0 12 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">'
            + '<circle cx="3" cy="3" r="1.6"/><circle cx="9" cy="3" r="1.6"/>'
            + '<circle cx="3" cy="9" r="1.6"/><circle cx="9" cy="9" r="1.6"/>'
            + '<circle cx="3" cy="15" r="1.6"/><circle cx="9" cy="15" r="1.6"/></svg>';

        // Per-run state (rebuilt on each init because Livewire morph can replace nodes).
        var editor, container, tiles, tileItems, currentOrder, dragging = null;
        // `locked` becomes true once we have an authoritative order — either the profile's
        // saved order was read from Livewire, or the user dragged. Until then, early inits
        // (Livewire not ready yet) only hold a provisional DOM-default order and keep retrying.
        var locked = false;

        function getEditor() {
            var el = document.querySelector('.sl-profile-editor--exhibit, .sl-profile-editor--voc');
            return el || null;
        }

        function getComponent() {
            var host = editor && editor.closest('[wire\\:id]');
            if (host && window.Livewire) {
                try { return window.Livewire.find(host.getAttribute('wire:id')); } catch (e) {}
            }
            return null;
        }

        // Filament resource pages keep form state under `data`; be tolerant of either path.
        var STATE_PATHS = ['data.tiles_order', 'tiles_order'];

        function readSavedOrder() {
            var c = getComponent();
            if (c) {
                for (var i = 0; i < STATE_PATHS.length; i++) {
                    try {
                        var v = c.get(STATE_PATHS[i]);
                        if (v !== undefined && v !== null && String(v).trim() !== '') {
                            return String(v).split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                        }
                    } catch (e) {}
                }
            }
            return null;
        }

        function persistOrder() {
            var c = getComponent();
            var value = currentOrder.join(',');
            if (c) {
                for (var i = 0; i < STATE_PATHS.length; i++) {
                    try {
                        // Only write the path that already exists in state (avoid creating a stray key).
                        if (c.get(STATE_PATHS[i]) !== undefined) {
                            c.set(STATE_PATHS[i], value, false);
                            break;
                        }
                    } catch (e) {}
                }
            }
            // Belt-and-suspenders: reflect into any rendered hidden input so a plain form
            // submit still carries the order even if the $wire path guess was wrong.
            var field = editor && editor.querySelector('.sl-tiles-order-field');
            var input = field && (field.matches('input') ? field : field.querySelector('input'));
            if (input) { input.value = value; }
        }

        // Filament v5 wraps each schema component in a grid-item <div wire:key> inside the
        // schema's CSS-grid container. Find that grid item by climbing to the first ancestor
        // whose PARENT computes to display:grid — robust to Filament's internal class names.
        function gridItemOf(tile) {
            var el = tile;
            while (el && el.parentElement) {
                var d = window.getComputedStyle(el.parentElement).display;
                if (d === 'grid' || d === 'inline-grid') { return el; }
                el = el.parentElement;
            }
            return null;
        }

        function collect() {
            editor = getEditor();
            if (! editor) { return false; }

            var found = editor.querySelectorAll('.sl-sortable-tile[data-tile-id]');
            if (! found.length) { return false; }

            tiles = Array.prototype.slice.call(found);

            var firstItem = gridItemOf(tiles[0]);
            if (! firstItem || ! firstItem.parentElement) { return false; }
            container = firstItem.parentElement;

            tileItems = {};
            tiles.forEach(function (t) {
                var item = gridItemOf(t);
                if (item && item.parentElement === container) {
                    tileItems[t.getAttribute('data-tile-id')] = { tile: t, item: item };
                }
            });

            // Only keep tiles that actually resolved to a grid item in this container.
            tiles = tiles.filter(function (t) { return tileItems[t.getAttribute('data-tile-id')]; });
            return tiles.length > 0;
        }

        function present() {
            return tiles.map(function (t) { return t.getAttribute('data-tile-id'); });
        }

        function normalise(order) {
            var ids = present();
            var out = (order || []).filter(function (id) { return ids.indexOf(id) !== -1; });
            ids.forEach(function (id) { if (out.indexOf(id) === -1) { out.push(id); } });
            return out;
        }

        function applyOrder() {
            if (! container) { return; }
            var children = Array.prototype.slice.call(container.children);

            // Baseline: everything keeps its DOM order.
            children.forEach(function (child, i) { child.style.order = String(i); });

            // Slots = the DOM indices the tile block occupies (ascending).
            var slots = present().map(function (id) {
                return children.indexOf(tileItems[id].item);
            }).sort(function (a, b) { return a - b; });

            // Place each tile into a slot following the desired order.
            currentOrder.forEach(function (id, k) {
                var entry = tileItems[id];
                if (entry && slots[k] != null) { entry.item.style.order = String(slots[k]); }
            });
        }

        function reorderToPointer(y) {
            var siblings = currentOrder
                .filter(function (id) { return id !== dragging.id; })
                .map(function (id) { return { id: id, rect: tileItems[id].item.getBoundingClientRect() }; });

            var next = [];
            var placed = false;
            siblings.forEach(function (s) {
                if (! placed && y < s.rect.top + s.rect.height / 2) {
                    next.push(dragging.id);
                    placed = true;
                }
                next.push(s.id);
            });
            if (! placed) { next.push(dragging.id); }

            if (next.join(',') !== currentOrder.join(',')) {
                currentOrder = next;
                applyOrder();
            }
        }

        function onPointerDown(e) {
            if (e.button != null && e.button !== 0) { return; }
            var handle = e.currentTarget;
            var tile = handle.closest('.sl-sortable-tile');
            if (! tile) { return; }
            e.preventDefault();
            e.stopPropagation();

            dragging = { id: tile.getAttribute('data-tile-id'), pointerId: e.pointerId, tile: tile };
            try { handle.setPointerCapture(e.pointerId); } catch (_) {}
            tile.classList.add('sl-tile-dragging');
            document.body.classList.add('sl-tiles-reordering');
        }

        function onPointerMove(e) {
            if (! dragging || e.pointerId !== dragging.pointerId) { return; }
            e.preventDefault();
            reorderToPointer(e.clientY);
        }

        function endDrag(e, handle) {
            if (! dragging) { return; }
            if (e && dragging.pointerId != null) {
                try { handle.releasePointerCapture(e.pointerId); } catch (_) {}
            }
            if (dragging.tile) { dragging.tile.classList.remove('sl-tile-dragging'); }
            document.body.classList.remove('sl-tiles-reordering');
            dragging = null;
            locked = true; // user intent is now authoritative
            persistOrder();
        }

        function attach(handle) {
            handle.addEventListener('pointerdown', onPointerDown);
            handle.addEventListener('pointermove', onPointerMove);
            handle.addEventListener('pointerup', function (e) { endDrag(e, handle); });
            handle.addEventListener('pointercancel', function (e) { endDrag(e, handle); });
            handle.addEventListener('lostpointercapture', function (e) { endDrag(e, handle); });
            // Never let the handle toggle/collapse the section or select text.
            handle.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); });
        }

        function injectHandles() {
            tiles.forEach(function (t) {
                if (t.querySelector(':scope > .sl-tile-drag')) { return; }
                var handle = document.createElement('span');
                handle.className = 'sl-tile-drag';
                handle.setAttribute('title', 'Drag to reorder this section');
                handle.setAttribute('aria-label', 'Drag to reorder this section');
                handle.innerHTML = GRIP;
                t.appendChild(handle);
                attach(handle);
            });
        }

        function hideOrderField() {
            // Collapse the hidden tiles_order field's whole grid cell so it adds no gap.
            var field = editor && editor.querySelector('.sl-tiles-order-field');
            if (! field) { return; }
            var cell = gridItemOf(field);
            if (cell) { cell.style.display = 'none'; }
        }

        function resolveOrder() {
            if (locked && currentOrder && currentOrder.length) {
                return normalise(currentOrder);
            }
            var saved = readSavedOrder();
            if (saved) {
                locked = true; // authoritative saved order obtained
                return normalise(saved);
            }
            // Provisional: Livewire not ready / no saved order — hold the default for now.
            return normalise(currentOrder && currentOrder.length ? currentOrder : present());
        }

        function init() {
            if (! collect()) { return; }
            currentOrder = resolveOrder();
            hideOrderField();
            injectHandles();
            applyOrder();
        }

        function boot() {
            init();
            setTimeout(init, 300);
            setTimeout(init, 1000);
            setTimeout(init, 2000);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', boot);
        } else {
            boot();
        }

        // SPA navigation lands on a different profile — drop any carried-over order state.
        document.addEventListener('livewire:navigated', function () {
            locked = false;
            currentOrder = null;
            boot();
        });
        document.addEventListener('livewire:init', function () {
            Livewire.hook('commit', function ({ succeed }) {
                succeed(function () {
                    // Morph re-imposed schema DOM order and may have dropped our handles /
                    // inline order styles — re-establish them, preserving the current order.
                    setTimeout(init, 30);
                });
            });
        });
    })();
</script>
