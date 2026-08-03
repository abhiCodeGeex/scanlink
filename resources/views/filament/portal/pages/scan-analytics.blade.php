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

            var leafletCssHref = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            var leafletJsSrc = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            var leafletLoading = null;

            function ensureLeaflet(cb) {
                if (typeof L !== 'undefined') {
                    cb();
                    return;
                }
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

            function renderEmpty(el, message) {
                el.innerHTML = '<div class="sl-sa__map-note">' + message + '</div>';
            }

            function bootMap(detail) {
                var attempts = 0;
                function tryBoot() {
                    var el = document.getElementById('sl-sa-map');
                    if (! el) {
                        if (attempts++ < 20) {
                            setTimeout(tryBoot, 50);
                        }
                        return;
                    }
                    ensureLeaflet(function () {
                        if (typeof L === 'undefined') {
                            return;
                        }
                        if (el._slMap) {
                            el._slMap.remove();
                            el._slMap = null;
                        }
                        el.innerHTML = '';

                        var points = (detail && detail.points) ? detail.points : [];
                        if (! points.length) {
                            renderEmpty(el, 'No map coordinates recorded for this profile.');
                            return;
                        }

                        var focusId = parseInt((detail && detail.focus) || 0, 10) || 0;
                        var map = L.map(el).setView([points[0].lat, points[0].lng], 4);
                        el._slMap = map;
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 18,
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(map);

                        var bounds = [];
                        var focusLatLng = null;
                        points.forEach(function (p) {
                            var color = (String(p.scan_type).toLowerCase() === 'gps') ? '#008901' : '#c0392b';
                            var marker = L.circleMarker([p.lat, p.lng], {
                                radius: 8,
                                color: '#fff',
                                weight: 2,
                                fillColor: color,
                                fillOpacity: 0.95
                            }).addTo(map);
                            marker.bindPopup(p.label || ('Scan #' + p.id));
                            bounds.push([p.lat, p.lng]);
                            if (focusId && Number(p.id) === focusId) {
                                focusLatLng = [p.lat, p.lng];
                                marker.openPopup();
                            }
                        });

                        if (focusLatLng) {
                            map.setView(focusLatLng, 10);
                        } else if (bounds.length > 1) {
                            map.fitBounds(bounds, { padding: [30, 30] });
                        } else {
                            map.setView(bounds[0], 8);
                        }

                        setTimeout(function () { map.invalidateSize(); }, 150);
                        setTimeout(function () { map.invalidateSize(); }, 400);
                    });
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
            display: inline-block !important;
            background: #008901 !important;
            color: #fff !important;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            text-decoration: none !important;
            border: 1px solid #006201 !important;
            border-radius: 5px !important;
            padding: 8px 12px !important;
            cursor: pointer;
            line-height: 1.2;
        }
        /* Buttons sit in a horizontal row (not stacked). */
        .sl-sa .scananalytic-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
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
        .sl-sa__btn:hover,
        .sl-sa .scananalytic-buttons .link-button:hover,
        .sl-sa .scananalytic-buttons input:hover { background: #00a001 !important; }
        .sl-sa__btn.is-active { background: #006201; }
        .sl-sa__toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            margin: 8px 0 14px;
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
        .sl-sa__dot--ip { background: #c0392b; }
        .sl-sa__sort select {
            margin-left: 6px;
            padding: 4px 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
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
                {{-- Parent header (Profile + buttons) is shown for map/locations + charts-no-data.
                     Charts-with-data uses the iframe's own legacy header/buttons. --}}
                @if (! ($viewMode === 'charts' && $analyticsCount > 0))
                    <div style="float:left;" id="divToPrint">
                        <h3 style="margin:0px;">Profile {{ $selectedProfileId }}.@if (filled($profileName)) {{ ucwords($profileName) }}@endif</h3>
                    </div>
                    <div class="btn-inline" style="float:right;">
                        <ul class="scananalytic-buttons">
                            <li><button type="button" class="link-button" wire:click="showMap">Location Map</button></li>
                            <li><button type="button" class="link-button" wire:click="showLocations">Location List</button></li>
                            <li><a class="link-button" href="{{ $this->returnToListUrl() }}">Return to list</a></li>
                        </ul>
                    </div>
                    <div class="clear" style="clear:both;"></div>
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
                    <div class="sl-sa__toolbar">
                        <div class="sl-sa__map-legend">
                            <span><span class="sl-sa__dot sl-sa__dot--ip"></span> Non GPS Location</span>
                            <span><span class="sl-sa__dot sl-sa__dot--gps"></span> Scan GPS Location</span>
                        </div>
                        <button type="button" class="sl-sa__btn" wire:click="showCharts">Back</button>
                    </div>
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
                    <div class="sl-sa__toolbar">
                        <button type="button" class="sl-sa__btn" wire:click="showCharts">Back</button>
                        <label class="sl-sa__sort">
                            Sort By
                            <select wire:change="setLocationSort($event.target.value)">
                                <option value="created_at" @selected($locationSort === 'created_at')>Scan Order</option>
                                <option value="city_name" @selected($locationSort === 'city_name')>Location</option>
                            </select>
                        </label>
                        <button type="button" class="sl-sa__btn" wire:click="exportXlsx">Export Analytics</button>
                        <button type="button" class="sl-sa__btn" wire:click="downloadPdf">Download PDF</button>
                    </div>
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
                                        <td>{{ $index + 1 }}</td>
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
                @endif
            @endif
        </div>
    </div>
</x-filament-panels::page>
