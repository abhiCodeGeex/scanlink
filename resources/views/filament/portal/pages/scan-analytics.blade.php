<x-filament-panels::page>
    @if ($selectedProfileId && $viewMode === 'charts' && $formAnalyticsEnabled)
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    @endif
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script>
        (function () {
            if (window.__slSaMapBootBound) {
                return;
            }
            window.__slSaMapBootBound = true;

            var GKEY = @json((string) config('scanlink.google_maps_api_key'));

            /* ---- shared helpers ---- */
            function renderEmpty(el, message) {
                el.innerHTML = '<div class="sl-sa__map-note">' + message + '</div>';
            }
            function slEsc(s) {
                return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }
            function slPopupHtml(p) {
                var rows = ['<div class="sl-sa__popup-title">' + slEsc(p.label || ('Scan #' + p.id)) + '</div>'];
                function line(k, v) {
                    if (v !== undefined && v !== null && String(v) !== '') {
                        rows.push('<div class="sl-sa__popup-row"><span>' + k + ':</span> ' + slEsc(v) + '</div>');
                    }
                }
                line('Time', p.time);
                line('Coordinates', p.coords);
                line('IP', p.ip);
                line('Device', p.device);
                line('Platform', p.platform);
                line('Browser', p.browser);
                line('Scan type', p.scan_label);
                return '<div class="sl-sa__popup">' + rows.join('') + '</div>';
            }
            /* Green (GPS) / grey (non-GPS) teardrop pins — match the legacy markers. */
            function pinSvg(fill) {
                var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="27" height="41" viewBox="0 0 27 41">'
                    + '<path fill="' + fill + '" stroke="#ffffff" stroke-width="1.5" d="M13.5 1C6.9 1 1.5 6.4 1.5 13c0 9 12 27 12 27s12-18 12-27C25.5 6.4 20.1 1 13.5 1z"/>'
                    + '<circle cx="13.5" cy="13" r="4.4" fill="#ffffff"/></svg>';
                return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
            }
            var PIN_GPS = pinSvg('#008901');
            var PIN_IP = pinSvg('#8a8f98');

            /* Legacy drill: overview marker click redirects to the per-scan view
               (?view=map&scan=ID) — the SPA equivalent of scanalytics_map_country. */
            function slIsDrilled() {
                try { return !!(new URL(window.location.href).searchParams.get('scan')); } catch (e) { return false; }
            }
            function slDrillTo(id) {
                try {
                    var u = new URL(window.location.href);
                    u.searchParams.set('view', 'map');
                    u.searchParams.set('scan', id);
                    window.location.href = u.toString();
                } catch (e) {}
            }

            /* ---- Leaflet (fallback when no Google Maps key) ---- */
            var leafletCssHref = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            var leafletJsSrc = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            var leafletLoading = null;
            function ensureLeaflet(cb) {
                if (typeof L !== 'undefined') { cb(); return; }
                if (! leafletLoading) {
                    leafletLoading = new Promise(function (resolve, reject) {
                        if (! document.querySelector('link[data-sl-sa-leaflet]')) {
                            var link = document.createElement('link');
                            link.rel = 'stylesheet';
                            link.href = leafletCssHref;
                            link.setAttribute('data-sl-sa-leaflet', '1');
                            document.head.appendChild(link);
                        }
                        var script = document.createElement('script');
                        script.src = leafletJsSrc;
                        script.async = true;
                        script.onload = function () { resolve(); };
                        script.onerror = function () { reject(new Error('Leaflet failed to load')); };
                        document.head.appendChild(script);
                    });
                }
                leafletLoading.then(cb).catch(function () { /* ignore */ });
            }
            function leafletPin(type) {
                return L.icon({
                    iconUrl: (type === 'gps' ? PIN_GPS : PIN_IP),
                    iconSize: [27, 41],
                    iconAnchor: [13, 40],
                    popupAnchor: [0, -34]
                });
            }
            function bootLeaflet(el, points, focusId) {
                if (typeof L === 'undefined') { return; }
                if (el._slMap) { el._slMap.remove(); el._slMap = null; }
                el.innerHTML = '';
                var map = L.map(el).setView([points[0].lat, points[0].lng], 4);
                el._slMap = map;
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);
                var bounds = [];
                var focusLatLng = null;
                points.forEach(function (p) {
                    var marker = L.marker([p.lat, p.lng], { icon: leafletPin(String(p.scan_type).toLowerCase()) }).addTo(map);
                    marker.bindPopup(slPopupHtml(p));
                    if (! slIsDrilled()) {
                        // Overview: click a marker to drill into that scan (URL redirect).
                        marker.on('click', function () { slDrillTo(p.id); });
                    }
                    bounds.push([p.lat, p.lng]);
                    if (focusId && Number(p.id) === focusId) {
                        focusLatLng = [p.lat, p.lng];
                        marker.openPopup();
                    }
                });
                if (focusLatLng) { map.setView(focusLatLng, 14); }
                else if (bounds.length > 1) { map.fitBounds(bounds, { padding: [30, 30] }); }
                else { map.setView(bounds[0], 10); }
                setTimeout(function () { map.invalidateSize(); }, 150);
                setTimeout(function () { map.invalidateSize(); }, 400);
            }

            /* ---- Google Maps (exact legacy design; needs GOOGLE_MAPS_API_KEY) ---- */
            var gmapsLoading = null;
            function ensureGoogle(cb, onFail) {
                if (window.google && window.google.maps) { cb(); return; }
                if (! gmapsLoading) {
                    gmapsLoading = new Promise(function (resolve, reject) {
                        var script = document.createElement('script');
                        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(GKEY);
                        script.async = true;
                        script.defer = true;
                        script.onload = function () { (window.google && window.google.maps) ? resolve() : reject(new Error('gmaps missing')); };
                        script.onerror = function () { reject(new Error('gmaps failed')); };
                        document.head.appendChild(script);
                    });
                }
                gmapsLoading.then(cb).catch(function () { if (onFail) { onFail(); } });
            }
            function bootGoogle(el, points, focusId) {
                el.innerHTML = '';
                var map = new google.maps.Map(el, {
                    zoom: 4,
                    center: { lat: points[0].lat, lng: points[0].lng },
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                el._slGMap = map;
                var infowindow = new google.maps.InfoWindow();
                var bounds = new google.maps.LatLngBounds();
                var focusData = null;
                points.forEach(function (p) {
                    var isGps = String(p.scan_type).toLowerCase() === 'gps';
                    var position = { lat: p.lat, lng: p.lng };
                    var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        icon: {
                            url: (isGps ? PIN_GPS : PIN_IP),
                            scaledSize: new google.maps.Size(27, 41),
                            anchor: new google.maps.Point(13, 40)
                        }
                    });
                    bounds.extend(position);
                    marker.addListener('click', function () {
                        if (slIsDrilled()) {
                            infowindow.setContent(slPopupHtml(p));
                            infowindow.open(map, marker);
                        } else {
                            // Overview: click a marker to drill into that scan (URL redirect).
                            slDrillTo(p.id);
                        }
                    });
                    if (focusId && Number(p.id) === focusId) {
                        focusData = { pos: position, marker: marker, p: p };
                    }
                });
                if (focusData) {
                    map.setCenter(focusData.pos);
                    map.setZoom(15);
                    google.maps.event.addListenerOnce(map, 'idle', function () {
                        infowindow.setContent(slPopupHtml(focusData.p));
                        infowindow.open(map, focusData.marker);
                    });
                } else if (points.length > 1) {
                    map.fitBounds(bounds);
                } else {
                    map.setCenter({ lat: points[0].lat, lng: points[0].lng });
                    map.setZoom(11);
                }
                setTimeout(function () { google.maps.event.trigger(map, 'resize'); }, 200);
            }

            function bootMap(detail) {
                var attempts = 0;
                function tryBoot() {
                    var el = document.getElementById('sl-sa-map');
                    if (! el) {
                        if (attempts++ < 20) { setTimeout(tryBoot, 50); }
                        return;
                    }
                    var points = (detail && detail.points) ? detail.points : [];
                    if (! points.length) {
                        renderEmpty(el, 'No map coordinates recorded for this profile.');
                        return;
                    }
                    var focusId = parseInt((detail && detail.focus) || 0, 10) || 0;
                    if (el._slMap) { el._slMap.remove(); el._slMap = null; }
                    if (GKEY) {
                        ensureGoogle(
                            function () { bootGoogle(el, points, focusId); },
                            function () { ensureLeaflet(function () { bootLeaflet(el, points, focusId); }); }
                        );
                    } else {
                        ensureLeaflet(function () { bootLeaflet(el, points, focusId); });
                    }
                }
                tryBoot();
            }

            window.addEventListener('sl-sa-boot-map', function (event) {
                bootMap(event.detail || {});
            });
        })();
    </script>

    <style>
        .sl-sa {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0 0 1.5rem;
            box-sizing: border-box;
        }
        .sl-sa__panel {
            background: #fff;
            border: 1px solid #e5ebe5;
            border-radius: 4px;
            padding: 18px 22px 28px;
            box-sizing: border-box;
        }
        .sl-sa__head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }
        .sl-sa__title {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            line-height: 1.2;
        }
        .sl-sa__profile {
            margin-top: 4px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .sl-sa__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }
        /* Force green pill buttons even on <button> (Filament/Tailwind resets button bg). */
        .sl-sa__btn,
        .sl-sa .scananalytic-buttons input,
        .sl-sa .scananalytic-buttons .link-button,
        .sl-sa .scananalytic-buttons a.link-button,
        .sl-sa .scananalytic-buttons button.link-button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #008901 !important;
            color: #fff !important;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            text-decoration: none !important;
            border: 1px solid #006201 !important;
            border-radius: 4px !important;
            padding: 6px 10px !important;
            min-height: 30px !important;
            cursor: pointer;
            line-height: 1.2;
            white-space: nowrap;
            box-sizing: border-box;
        }
        .sl-sa__btn:hover,
        .sl-sa .scananalytic-buttons .link-button:hover,
        .sl-sa .scananalytic-buttons input:hover,
        .sl-sa__chrome-actions .link-button:hover,
        .sl-sa__chrome-actions .sl-sa__btn:hover:not(.sl-sa__btn--ghost) {
            background: #00a001 !important;
        }
        .sl-sa__btn.is-active,
        .sl-sa__chrome-actions .link-button.is-active,
        .sl-sa__chrome-actions button.link-button.is-active {
            background: #006201 !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, .15);
        }

        /* Compact page chrome: title on its own row, then one aligned toolbar */
        .sl-sa__chrome {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 0 0 14px;
            padding: 0 0 12px;
            border-bottom: 1px solid #e5ebe5;
        }
        .sl-sa__chrome-title {
            margin: 0;
            padding: 0;
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.35;
        }
        .sl-sa__chrome-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px 16px;
            width: 100%;
        }
        .sl-sa__chrome-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            flex: 0 1 auto;
        }
        .sl-sa__chrome-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            flex: 1 1 auto;
            margin: 0;
            padding: 0;
            list-style: none;
            min-width: min(100%, 280px);
        }
        @media (min-width: 1100px) {
            .sl-sa__chrome-bar {
                flex-wrap: nowrap;
            }
            .sl-sa__chrome-actions {
                flex-wrap: nowrap;
                flex: 0 1 auto;
                min-width: 0;
            }
        }
        .sl-sa__chrome-actions li {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .sl-sa__chrome-actions .link-button,
        .sl-sa__chrome-actions .sl-sa__btn,
        .sl-sa__chrome-actions a.link-button,
        .sl-sa__chrome-actions button.link-button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .sl-sa__btn--ghost {
            background: #fff !important;
            color: #008901 !important;
            border-color: #008901 !important;
        }
        .sl-sa__btn--ghost:hover {
            background: #f0faf0 !important;
            color: #006201 !important;
        }
        .sl-sa__sort {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            font-size: 12px;
            font-weight: 600;
            color: #555;
            white-space: nowrap;
        }
        .sl-sa__sort select {
            height: 30px;
            min-width: 130px;
            padding: 4px 8px;
            border: 1px solid #cfd5cf;
            border-radius: 4px;
            background: #fff;
            font-size: 12px;
            color: #333;
            font-family: Arial, Helvetica, sans-serif;
        }
        .sl-sa__toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: space-between;
            margin: 0 0 12px;
        }
        /* Keep legacy list class usable, but chrome owns the layout */
        .sl-sa .scananalytic-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 0;
            padding: 0;
            justify-content: flex-end;
        }
        .sl-sa .scananalytic-buttons li {
            list-style: none;
            float: none;
            margin: 0;
            padding: 0;
        }
        .sl-sa__hint {
            text-align: center;
            color: #777;
            font-size: 13px;
            margin: 8px 0 18px;
        }
        .sl-sa__totals {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            justify-content: flex-end;
            margin: 0 0 10px;
        }
        .sl-sa__box {
            min-width: 140px;
            text-align: center;
        }
        .sl-sa__box b {
            display: block;
            font-size: 13px;
            margin-bottom: 6px;
            color: #444;
        }
        /* Legacy scanalytics-box (label above the bordered count) — not in style.css */
        .sl-sa .scanalytics-box { text-align: center; }
        .sl-sa .scanalytics-box b { display: block; font-size: 13px; color: #444; margin-bottom: 6px; }
        .sl-sa__box-count {
            border: 3px solid #A2A2A2;
            padding: 8px 12px;
            font-size: 26px;
            font-weight: bold;
            line-height: 1.1;
            background: #fff;
        }
        .sl-sa__print-btn {
            background: #eee;
            border: 1px solid #bbb;
            border-radius: 3px;
            padding: 6px 10px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }
        .sl-sa__charts {
            position: relative;
            margin-top: 12px;
        }
        .sl-sa__chart-wrap {
            height: 500px !important;
            margin: 0 0 24px;
            position: relative;
            width: 100%;
        }
        .sl-sa__chart-inner {
            position: relative;
            width: 100%;
            height: 95%;
        }
        .sl-sa__chart-inner > div {
            position: relative;
            height: 95%;
            width: 100%;
        }
        .sl-sa__loading {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,.7);
            z-index: 5;
            font-weight: bold;
            color: #555;
        }
        .sl-sa__empty {
            text-align: center;
            font-weight: bold;
            color: #5286BE;
            padding: 24px 12px;
        }
        .sl-sa table.listing-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .sl-sa table.listing-table th {
            background: #e8e8e8;
            padding: 8px 6px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            white-space: nowrap;
        }
        .sl-sa table.listing-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .sl-sa table.listing-table tr:nth-child(even) td { background: #f7faf7; }
        .sl-sa__map {
            width: 100%;
            height: 480px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #eef3f7;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .sl-sa__map-note {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            text-align: center;
            color: #555;
            font-size: 14px;
            z-index: 2;
        }
        .sl-sa__map-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            font-size: 13px;
        }
        .sl-sa__dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
        }
        .sl-sa__dot--gps { background: #008901; }
        .sl-sa__dot--ip { background: #8a8f98; }
        /* Map marker detail popup (legacy info-window parity). */
        .sl-sa__popup { font-size: 12px; color: #222; min-width: 190px; line-height: 1.5; }
        .sl-sa__popup-title { font-weight: 700; margin-bottom: 6px; color: #185FA5; font-size: 13px; }
        .sl-sa__popup-row { margin: 2px 0; }
        .sl-sa__popup-row span { font-weight: 700; color: #444; }
        .sl-sa__pager {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-top: 14px;
        }
        .sl-sa__pager-info { font-size: 13px; color: #333; }
        .sl-sa__pager .sl-sa__btn[disabled] { opacity: 0.5; cursor: not-allowed; }
        .sl-sa__link {
            color: #008901;
            font-weight: bold;
            text-decoration: underline;
            cursor: pointer;
            background: none;
            border: 0;
            padding: 0;
            font-size: 12px;
        }
        .sl-sa__table-wrap { overflow-x: auto; }
        /*
         * Match live screenshot (old):
         * - Y-axis: green top / blue bottom
         * - Plot canvas: WHITE (so the sky-blue bar is visible)
         * - Bar: light sky blue
         * - Category strip (Australia/Desktop/Chrome): medium blue + white text
         * - Context label (Country Statistics): black on white
         */
        .sl-sa .ui-widget {
            font-family: Tahoma, Arial, sans-serif !important;
        }
        .sl-sa .ui-widget-content {
            border: 1px solid #4297d7 !important;
            background: #ffffff !important;
            color: #222222 !important;
        }
        /* Y-axis green */
        .sl-sa .ddchart-y-axis.ui-state-active {
            border: 1px solid #459e00 !important;
            background: #6eac2c !important;
            background-image: none !important;
            color: #ffffff !important;
            font-weight: bold !important;
        }
        /* Y-axis blue */
        .sl-sa .ddchart-y-axis.ui-state-default {
            border: 1px solid #0077b6 !important;
            background: #0078ae !important;
            background-image: none !important;
            color: #ffffff !important;
            font-weight: bold !important;
        }
        /* Category name strip under the bar (Australia / Desktop / Chrome) */
        .sl-sa .ddchart-x-axis.ui-state-default,
        .sl-sa .ddchart-x-axis.ui-state-active {
            border: 1px solid #0077b6 !important;
            background: #0078ae !important;
            background-image: none !important;
            color: #ffffff !important;
            font-weight: bold !important;
        }
        .sl-sa .ddchart-x-axis .x-axis-text {
            color: #ffffff !important;
            font-weight: bold !important;
        }
        /* The actual data bar — light sky blue */
        .sl-sa .ddchart-chart.ui-state-focus,
        .sl-sa .ddchart-chart.ui-corner-top {
            border: 1px solid #448dae !important;
            background: #82caed !important;
            background-image: none !important;
            color: #026890 !important;
        }
        .sl-sa .ddchart-chart.ui-state-highlight {
            border: 1px solid #fcd113 !important;
            background: #f8da4e !important;
            color: #915608 !important;
        }
        /* Plot canvas MUST stay white so the sky-blue bar reads like the old site.
           No teal border line — live ddBarChart clears wrapper borders after load. */
        .sl-sa .ddchart-chart-wrapper-sub {
            background-color: #ffffff !important;
            border: none !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
        }
        .sl-sa .ddchart-chart-final > .ddchart-chart-wrapper-sub,
        .sl-sa .ddchart-chart-init > .ddchart-chart-wrapper-sub {
            border: none !important;
        }
        /* Kill leftover ui-widget-content outline that draws the horizontal line */
        .sl-sa .ddchart-chart-wrapper.ui-widget-content,
        .sl-sa .ddchart-chart-final.ui-widget-content,
        .sl-sa .ddchart-chart-init.ui-widget-content {
            border: none !important;
            background: transparent !important;
        }
        .sl-sa .ddchart-x-axis-wrapper {
            background: transparent !important;
        }
        /* "Country Statistics" / "Device Statistics" / "Browser Statistics" */
        .sl-sa .ddchart-x-axis-label {
            background: #ffffff !important;
            color: #222222 !important;
            font-weight: bold !important;
            text-align: center !important;
        }
        .sl-sa .ddchart-x-axis-label .dd-chart-context,
        .sl-sa .dd-chart-context {
            color: #222222 !important;
            font-weight: bold !important;
        }
        .sl-sa .dd-chart-context-drillup {
            color: #0078ae !important;
            cursor: pointer;
        }
        .sl-sa__chart-wrap {
            height: 500px !important;
            margin: 0 auto 28px;
            max-width: 100%;
        }
        .sl-sa__btn {
            background: #008901 !important;
            border-color: #006201 !important;
        }
        .sl-sa__btn.sl-sa__btn--ghost,
        .sl-sa a.sl-sa__btn.sl-sa__btn--ghost {
            background: #fff !important;
            color: #008901 !important;
            border-color: #008901 !important;
        }
        .sl-sa__btn.sl-sa__btn--ghost:hover,
        .sl-sa a.sl-sa__btn.sl-sa__btn--ghost:hover {
            background: #f0faf0 !important;
            color: #006201 !important;
        }
        .sl-sa .scananalytic-buttons .link-button.is-active,
        .sl-sa .scananalytic-buttons button.link-button.is-active {
            background: #006201 !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, .15);
        }
        .sl-sa__box-count {
            border: 3px solid #A2A2A2;
            background: #fff;
        }
        .sl-sa__empty-charts {
            text-align: center;
            padding: 48px 20px 36px;
            color: #555;
            border: 1px dashed #c5c5c5;
            border-radius: 4px;
            background: #fafafa;
            margin: 12px 0 8px;
        }
        .sl-sa__empty-charts strong {
            display: block;
            font-size: 18px;
            color: #333;
            margin-bottom: 8px;
        }
        .sl-sa__empty-charts p {
            margin: 0;
            font-size: 14px;
            line-height: 1.45;
            color: #666;
        }
        .sl-sa__error {
            text-align: center;
            padding: 36px 20px;
            color: #8a1f1f;
            font-weight: bold;
        }
        /* Theme switcher in Filament layout is legacy chrome — keep compact */
        .sl-sa #switcher {
            min-height: 28px;
        }
        .sl-sa #switcher .jquery-ui-switcher-link,
        .sl-sa #switcher a {
            font-size: 12px !important;
        }
        html.dark .sl-sa__panel { background: rgb(17 24 39) !important; border-color: rgb(55 65 81) !important; }
        html.dark .sl-sa,
        html.dark .sl-sa__title,
        html.dark .sl-sa__profile { color: rgb(243 244 246) !important; }
        html.dark .sl-sa__box-count { background: rgb(31 41 55) !important; border-color: rgb(55 65 81) !important; color: rgb(243 244 246) !important; }
        html.dark .sl-sa input,
        html.dark .sl-sa select { background: rgb(31 41 55) !important; border-color: rgb(75 85 99) !important; color: rgb(243 244 246) !important; }
    </style>

    <div class="sl-sa">
        <div class="sl-sa__panel" id="divToPrint">
            @if ($loadError)
                <div class="sl-sa__error">{{ $loadError }}</div>
                <div style="text-align:center;margin-top:16px;">
                    <a class="sl-sa__btn" href="{{ $this->returnToListUrl() }}">Return to list</a>
                </div>
            @elseif (! $selectedProfileId)
                <div class="sl-sa__empty">Select a profile from Master Code List to view Scanalytics.</div>
            @else
                {{-- Compact chrome: title row + one toolbar (controls left, all actions right).
                     Charts-with-data uses the iframe's own legacy header/buttons. --}}
                @if (! ($viewMode === 'charts' && $analyticsCount > 0))
                    <div class="sl-sa__chrome">
                        <h3 class="sl-sa__chrome-title">
                            Profile {{ $selectedProfileId }}.@if (filled($profileName)) {{ ucwords($profileName) }}@endif
                        </h3>

                        <div class="sl-sa__chrome-bar">
                            <div class="sl-sa__chrome-controls">
                                @if ($viewMode === 'locations')
                                    <button type="button" class="sl-sa__btn sl-sa__btn--ghost" wire:click="showCharts">Back</button>
                                    <label class="sl-sa__sort">
                                        Sort By
                                        <select wire:change="setLocationSort($event.target.value)">
                                            <option value="created_at" @selected($locationSort === 'created_at')>Scan Order</option>
                                            <option value="city_name" @selected($locationSort === 'city_name')>Location</option>
                                        </select>
                                    </label>
                                @elseif ($viewMode === 'map')
                                    <div class="sl-sa__map-legend">
                                        <span><span class="sl-sa__dot sl-sa__dot--ip"></span> Non GPS Location</span>
                                        <span><span class="sl-sa__dot sl-sa__dot--gps"></span> Scan GPS Location</span>
                                    </div>
                                    @if ($drillScanId)
                                        <a class="sl-sa__btn sl-sa__btn--ghost" href="{{ $this->mapOverviewUrl() }}">Back</a>
                                    @else
                                        <button type="button" class="sl-sa__btn sl-sa__btn--ghost" wire:click="backFromMap">Back</button>
                                    @endif
                                @endif
                            </div>

                            <ul class="sl-sa__chrome-actions scananalytic-buttons">
                                <li>
                                    <button
                                        type="button"
                                        class="link-button @if ($viewMode === 'map') is-active @endif"
                                        wire:click="showMap"
                                    >Location Map</button>
                                </li>
                                <li>
                                    <button
                                        type="button"
                                        class="link-button @if ($viewMode === 'locations') is-active @endif"
                                        wire:click="showLocations"
                                    >Location List</button>
                                </li>
                                @if ($viewMode === 'locations')
                                    <li>
                                        <button type="button" class="sl-sa__btn" wire:click="exportXlsx">Export Analytics</button>
                                    </li>
                                    <li>
                                        <button type="button" class="sl-sa__btn" wire:click="downloadPdf">Download PDF</button>
                                    </li>
                                @endif
                                <li>
                                    <a class="link-button" href="{{ $this->returnToListUrl() }}">Return to list</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif

                @if ($viewMode === 'charts')
                    @if ($analyticsCount === 0)
                        <div class="sl-sa__empty-charts">
                            <strong>No scan activity recorded yet</strong>
                            <p>
                                Country, device and browser charts appear here after this code is scanned.
                                Use Location Map / Location List once scan data is available.
                            </p>
                        </div>
                    @else
                        {{-- Full legacy scanalytics (heading + green buttons + theme switcher + stats
                             + hover/drill charts) in an isolated iframe — pixel-accurate, untouched
                             by Filament's Tailwind reset or Livewire/Alpine. --}}
                        <iframe
                            id="sl-sa-charts-frame"
                            src="{{ route('portal.graphengine.charts', ['pid' => $selectedProfileId]) }}"
                            style="width:100%;height:1720px;border:0;overflow:hidden;display:block;"
                            scrolling="no"
                            title="Scanalytics"
                        ></iframe>
                        <script>
                            (function () {
                                window.addEventListener('message', function (e) {
                                    if (e && e.data && e.data.slChartsHeight) {
                                        var f = document.getElementById('sl-sa-charts-frame');
                                        if (f) { f.style.height = (parseInt(e.data.slChartsHeight, 10) + 24) + 'px'; }
                                    }
                                });
                            })();
                        </script>
                    @endif

                    {{-- Legacy Form Analytics pie section (enable_form_analytics=1 && form_id!=0) --}}
                    @if ($formAnalyticsEnabled && count($formCharts) > 0)
                        @php($scanPct = $analyticsCount == 0 ? ($formSubmissionCount == 0 ? '0%' : '100%') : number_format((($formSubmissionCount * 100) / $analyticsCount), 2).'%')
                        <div>
                            <h2 style="padding-left:50px;font-size:35px;">
                                <img align="top" src="https://scanlink.com.au/images/pie_icon.png" width="50" onerror="this.style.display='none'" />&nbsp;&nbsp;Form Analytics
                            </h2>
                            <button type="button" wire:click="exportAnalytics" style="border:0;background:none;padding:0;cursor:pointer;">
                                <div class="export-analytics-btn">EXPORT ANALYTICS</div>
                            </button>

                            <table style="padding-left:50px;" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td width="52%">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td class="scanalytics_count" width="3%" align="right">{{ $analyticsCount }}</td>
                                                <td width="1%">&nbsp;</td>
                                                <td class="scanalytics_text" align="left"> Scans</td>
                                            </tr>
                                            <tr>
                                                <td class="scanalytics_count" width="3%" align="right">{{ $formSubmissionCount }}</td>
                                                <td width="1%">&nbsp;</td>
                                                <td class="scanalytics_text" align="left"> Form Submissions</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="48%" valign="middle" class="scanalytics_percent">{{ $scanPct }}</td>
                                </tr>
                            </table>
                            <div style="height:50px;">&nbsp;</div>

                            @foreach ($formCharts as $j => $chart)
                                @php($total = array_sum(array_column($chart['slices'], 'value')))
                                <div class="analytics-question-text">{{ $chart['title'] }}
                                    <br>
                                    @foreach ($chart['slices'] as $slice)
                                        <div><span style="font-weight:bold;color:{{ $slice['color'] }}">{{ $slice['value'] }}</span><span class="legend-text">&nbsp;&nbsp;&nbsp;{{ $slice['label'] }}</span></div>
                                    @endforeach
                                </div>
                                <div id="piechart_3d{{ $j }}" style="width:220px;height:200px;float:left;" wire:ignore></div>
                                <div class="clear" style="clear:both;">&nbsp;</div>
                                <div class="clear" style="clear:both;">&nbsp;</div>
                                <div class="clear" style="clear:both;">&nbsp;</div>
                            @endforeach

                            <script type="text/javascript">
                                (function () {
                                    var charts = @json($formCharts);
                                    function drawFormPies() {
                                        if (typeof google === 'undefined' || ! google.visualization) { return; }
                                        charts.forEach(function (chart, j) {
                                            var rows = [['Task', chart.title]];
                                            var colors = [];
                                            chart.slices.forEach(function (s) { rows.push([String(s.label), s.value]); colors.push(s.color); });
                                            var data = google.visualization.arrayToDataTable(rows);
                                            var options = {
                                                title: chart.title, is3D: true, legend: 'none',
                                                chartArea: { width: '200', height: '200' },
                                                width: 220, height: 200, sliceVisibilityThreshold: 0, colors: colors
                                            };
                                            var el = document.getElementById('piechart_3d' + j);
                                            if (el) { new google.visualization.PieChart(el).draw(data, options); }
                                        });
                                    }
                                    if (typeof google !== 'undefined' && google.load) {
                                        google.load('visualization', '1', { packages: ['corechart'], callback: drawFormPies });
                                    } else if (typeof google !== 'undefined' && google.charts) {
                                        google.charts.load('current', { packages: ['corechart'] });
                                        google.charts.setOnLoadCallback(drawFormPies);
                                    }
                                })();
                            </script>
                        </div>
                    @endif

                @elseif ($viewMode === 'map')
                    {{-- wire:ignore keeps Leaflet's DOM intact; map is booted via sl-sa-boot-map. --}}
                    <div
                        id="sl-sa-map"
                        class="sl-sa__map"
                        wire:ignore
                        wire:key="sl-sa-map-shell"
                    ></div>
                    <script>
                        window.dispatchEvent(new CustomEvent('sl-sa-boot-map', {
                            detail: @json(['points' => $mapPoints, 'focus' => $focusRowId])
                        }));
                    </script>

                @else
                    <div class="sl-sa__table-wrap">
                        <table class="listing-table" width="100%" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Date Time</th>
                                    <th>Ip address</th>
                                    <th>Location</th>
                                    <th>Coordinates</th>
                                    <th>Devics Name</th>
                                    <th>Platform Name</th>
                                    <th>Screen Size</th>
                                    <th>Browser Name</th>
                                    <th>Browser Version</th>
                                    <th>Scan Type</th>
                                    <th>OPTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($locationRows as $index => $row)
                                    <tr wire:key="sa-loc-{{ $row->id }}">
                                        <td>{{ ($locationPage - 1) * $locationPerPage + $index + 1 }}</td>
                                        <td>{{ $row->created_at?->format('d/m/Y H:i') }}</td>
                                        <td>{{ $row->ip_add }}</td>
                                        <td>{{ $row->location_label }}</td>
                                        <td>{{ $row->latitude }}, {{ $row->longitude }}</td>
                                        <td>{{ $row->device_name }}</td>
                                        <td>{{ $row->platform_name }}</td>
                                        <td>{{ $row->screen_size }}</td>
                                        <td>{{ $row->browser_name }}</td>
                                        <td>{{ $row->browser_version }}</td>
                                        <td>{{ $row->scan_type }}</td>
                                        <td>
                                            @if (filled($row->latitude) && filled($row->longitude))
                                                <button type="button" class="sl-sa__link" wire:click="showMap({{ (int) $row->id }})">Show on map</button>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="sl-sa__empty">No records found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($this->locationLastPage() > 1)
                        <div class="sl-sa__pager">
                            <button type="button" class="sl-sa__btn" wire:click="setLocationPage({{ $locationPage - 1 }})" @disabled($locationPage <= 1)>Prev</button>
                            <span class="sl-sa__pager-info">Page {{ $locationPage }} of {{ $this->locationLastPage() }} &middot; {{ number_format($locationTotal) }} scans</span>
                            <button type="button" class="sl-sa__btn" wire:click="setLocationPage({{ $locationPage + 1 }})" @disabled($locationPage >= $this->locationLastPage())>Next</button>
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
</x-filament-panels::page>
