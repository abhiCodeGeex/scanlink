<x-filament-panels::page>
    <style>
        .sl-ca {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0 0 1.5rem;
            box-sizing: border-box;
        }
        .sl-ca__panel {
            background: #fff;
            border: 1px solid #e5ebe5;
            border-radius: 4px;
            padding: 18px 22px 28px;
            box-sizing: border-box;
        }
        .sl-ca__picker {
            margin-bottom: 16px;
        }
        .sl-ca__head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }
        .sl-ca__title {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            line-height: 1.2;
        }
        .sl-ca__profiles {
            margin-top: 8px;
            font-size: 15px;
            line-height: 1.45;
            color: #333;
        }
        .sl-ca__profiles strong {
            color: #222;
        }
        .sl-ca__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }
        .sl-ca__btn {
            display: inline-block;
            background: #008901;
            color: #fff !important;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            text-decoration: none !important;
            border: 1px solid #006201;
            border-radius: 5px;
            padding: 8px 12px;
            cursor: pointer;
            line-height: 1.2;
        }
        .sl-ca__btn:hover { background: #00a001; }
        .sl-ca__btn.is-active { background: #006201; }
        .sl-ca__toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin: 8px 0 18px;
        }
        .sl-ca__totals {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            justify-content: flex-end;
        }
        .sl-ca__box {
            min-width: 150px;
            text-align: center;
        }
        .sl-ca__box b {
            display: block;
            font-size: 13px;
            margin-bottom: 6px;
            color: #444;
        }
        .sl-ca__box-count {
            border: 3px solid #A2A2A2;
            padding: 8px 12px;
            font-size: 26px;
            font-weight: bold;
            line-height: 1.1;
            background: #fff;
        }
        .sl-ca__section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 8px 0 18px;
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        .sl-ca__section-title img {
            width: 46px;
            height: 46px;
        }
        .sl-ca__hint {
            color: #777;
            font-size: 13px;
            margin: 0 0 16px;
        }
        .sl-ca__notes {
            color: #777;
            font-size: 13px;
            margin: 0 0 14px;
        }
        .sl-ca__empty {
            text-align: center;
            font-weight: bold;
            color: #5286BE;
            padding: 28px 12px;
        }
        .sl-ca__chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
            margin-bottom: 28px;
        }
        .sl-ca__chart-card {
            border: 1px solid #e4e8e4;
            border-radius: 4px;
            padding: 14px 16px 18px;
            background: #fafcfa;
        }
        .sl-ca__chart-card h4 {
            margin: 0 0 12px;
            font-size: 15px;
            color: #333;
        }
        .sl-ca__bar-row {
            display: grid;
            grid-template-columns: minmax(90px, 34%) 1fr auto;
            gap: 8px;
            align-items: center;
            margin-bottom: 8px;
            font-size: 12px;
        }
        .sl-ca__bar-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #444;
        }
        .sl-ca__bar-track {
            height: 14px;
            background: #e8eee8;
            border-radius: 3px;
            overflow: hidden;
        }
        .sl-ca__bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #0078ae, #82caed);
            border-radius: 3px;
        }
        .sl-ca__bar-value {
            font-weight: bold;
            color: #222;
            min-width: 36px;
            text-align: right;
        }
        .sl-ca__form-block {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 240px;
            gap: 16px;
            align-items: start;
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 1px solid #eee;
        }
        .sl-ca__legend-row {
            margin-bottom: 6px;
            font-size: 13px;
        }
        .sl-ca__legend-count {
            font-weight: bold;
            margin-right: 8px;
        }
        .sl-ca__pie {
            width: 220px;
            height: 200px;
            margin: 0 auto;
        }
        .sl-ca table.listing-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .sl-ca table.listing-table th {
            background: #e8e8e8;
            padding: 8px 6px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            white-space: nowrap;
        }
        .sl-ca table.listing-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .sl-ca table.listing-table tr:nth-child(even) td { background: #f7faf7; }
        .sl-ca__badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: #fff;
        }
        .sl-ca__badge--gps { background: #008901; }
        .sl-ca__badge--ip { background: #c0392b; }
        .sl-ca__pager {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 14px;
            font-size: 13px;
            color: #444;
        }
        .sl-ca__pager-btns {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }
        .sl-ca__pager-btn {
            display: inline-block;
            background: #008901;
            color: #fff !important;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            border: 1px solid #006201;
            border-radius: 4px;
            padding: 6px 10px;
            cursor: pointer;
            line-height: 1.2;
        }
        .sl-ca__pager-btn:hover { background: #00a001; }
        .sl-ca__pager-btn:disabled,
        .sl-ca__pager-btn.is-disabled {
            background: #9bb89b;
            border-color: #8aa88a;
            cursor: default;
            opacity: 0.7;
        }
        .sl-ca__pager-btn.is-active {
            background: #006201;
        }
        @media (max-width: 720px) {
            .sl-ca__form-block {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="sl-ca">
        <div class="sl-ca__picker">
            {{ $this->form }}
        </div>

        <div class="sl-ca__panel">
            @if ($selectedProfileIds === [])
                <div class="sl-ca__empty">Select one or more profiles to view Cumulative Analytics.</div>
            @else
                <div class="sl-ca__head">
                    <div>
                        <h2 class="sl-ca__title">Cumulative Analytics</h2>
                        <div class="sl-ca__profiles">
                            @foreach ($selectedProfiles as $profile)
                                <div>
                                    <strong>Profile {{ $profile['id'] }}.</strong>
                                    {{ ucwords($profile['name']) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <style>
                        @media print {
                            .fi-sidebar, .fi-topbar, .fi-topbar-ctn, .fi-sidebar-header,
                            .sl-ca__actions, .sl-ca__modal-overlay, .sl-ca__notes { display: none !important; }
                            .fi-main-ctn, .fi-main { margin: 0 !important; padding: 0 !important; }
                        }
                        [x-cloak] { display: none !important; }
                    </style>
                    <div class="sl-ca__actions" x-data="{ exportOpen: false }">
                        <button type="button" class="sl-ca__btn {{ $viewMode === 'charts' ? 'is-active' : '' }}" wire:click="showCharts">Overview</button>
                        <button type="button" class="sl-ca__btn {{ $viewMode === 'locations' ? 'is-active' : '' }}" wire:click="showLocations">Location List</button>
                        <button type="button" class="sl-ca__btn" @click="exportOpen = true">Export Analytics</button>
                        <button type="button" class="sl-ca__btn" wire:click="downloadPdf">Download PDF</button>
                        <button type="button" class="sl-ca__btn" onclick="window.print()">Print</button>
                        <a class="sl-ca__btn" href="{{ $this->returnToListUrl() }}">Return to list</a>

                        {{-- Legacy EXPORT ANALYTICS modal: Name + Description --}}
                        <div x-show="exportOpen" x-cloak class="sl-ca__modal-overlay" @click.self="exportOpen = false"
                             style="position:fixed;inset:0;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;z-index:60;">
                            <div class="sl-ca__modal"
                                 style="background:#fff;color:#1f2937;border-radius:10px;padding:20px;width:min(420px,92vw);box-shadow:0 10px 40px rgba(0,0,0,.3);">
                                <h3 style="font-size:16px;font-weight:700;margin:0 0 12px;">Export Analytics</h3>
                                <label style="display:block;font-size:13px;margin-bottom:4px;">Name</label>
                                <input type="text" wire:model="exportName"
                                       style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;margin-bottom:12px;">
                                <label style="display:block;font-size:13px;margin-bottom:4px;">Description</label>
                                <textarea wire:model="exportDescription" rows="3"
                                          style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;margin-bottom:16px;"></textarea>
                                <div style="display:flex;justify-content:flex-end;gap:8px;">
                                    <button type="button" class="sl-ca__btn" @click="exportOpen = false">Cancel</button>
                                    <button type="button" class="sl-ca__btn is-active" wire:click="exportSpreadsheet" @click="exportOpen = false">Export Analytics</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($analyticsStatusNotes !== [])
                    <div class="sl-ca__notes">
                        @foreach ($analyticsStatusNotes as $note)
                            <p>{{ $note }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="sl-ca__toolbar">
                    <div class="sl-ca__hint">Combined results for {{ count($selectedProfiles) }} selected profile{{ count($selectedProfiles) === 1 ? '' : 's' }}.</div>
                    <div class="sl-ca__totals">
                        <div class="sl-ca__box">
                            <b>Form Submissions</b>
                            <div class="sl-ca__box-count">{{ $formSubmissionCount }}</div>
                        </div>
                        <div class="sl-ca__box">
                            <b>Analytics Count</b>
                            <div class="sl-ca__box-count">{{ $scanTotal }}</div>
                        </div>
                    </div>
                </div>

                @if ($viewMode === 'charts')
                    <div class="sl-ca__section-title">
                        @if (file_exists(public_path('images/pie_icon.png')))
                            <img src="{{ asset('images/pie_icon.png') }}" alt="">
                        @endif
                        Form Analytics
                    </div>

                    @if ($formCharts === [])
                        <p class="sl-ca__hint">No form analytics questions for these profiles. Scan overview is shown below.</p>
                    @else
                        <script src="https://www.gstatic.com/charts/loader.js"></script>
                        @foreach ($formCharts as $index => $chart)
                            <div class="sl-ca__form-block" wire:key="form-chart-{{ $index }}">
                                <div>
                                    <div class="analytics-question-text" style="font-weight:bold;margin-bottom:10px;">{{ $chart['title'] }}</div>
                                    @foreach ($chart['slices'] as $slice)
                                        <div class="sl-ca__legend-row">
                                            <span class="sl-ca__legend-count" style="color: {{ $slice['color'] }}">{{ $slice['value'] }}</span>
                                            <span>{{ $slice['label'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="piechart_3d{{ $index }}" class="sl-ca__pie" wire:ignore></div>
                            </div>
                        @endforeach

                        <script>
                            (function () {
                                if (typeof google === 'undefined' || ! google.charts) {
                                    return;
                                }

                                google.charts.load('current', { packages: ['corechart'] });
                                google.charts.setOnLoadCallback(function () {
                                    var charts = @json($formCharts);
                                    charts.forEach(function (chart, index) {
                                        var rows = [['Option', 'Responses']];
                                        chart.slices.forEach(function (slice) {
                                            rows.push([String(slice.label), Number(slice.value)]);
                                        });
                                        var data = google.visualization.arrayToDataTable(rows);
                                        var colors = chart.slices.map(function (s) { return s.color; });
                                        var pie = new google.visualization.PieChart(document.getElementById('piechart_3d' + index));
                                        pie.draw(data, {
                                            title: chart.title,
                                            is3D: true,
                                            legend: 'none',
                                            chartArea: { width: 200, height: 200 },
                                            width: 220,
                                            height: 200,
                                            sliceVisibilityThreshold: 0,
                                            colors: colors
                                        });
                                    });
                                });
                            })();
                        </script>
                    @endif

                    <div class="sl-ca__section-title" style="margin-top: 10px;">Scan Overview</div>
                    @if ($scanTotal === 0)
                        <p class="sl-ca__hint">No scan analytics rows for the selected profiles.</p>
                    @else
                        <div class="sl-ca__chart-grid">
                            <div class="sl-ca__chart-card">
                                <h4>Top Countries</h4>
                                @php $maxCountry = $this->maxBarValue($countryBars); @endphp
                                @forelse ($countryBars as $bar)
                                    <div class="sl-ca__bar-row">
                                        <div class="sl-ca__bar-label" title="{{ $bar['label'] }}">{{ $bar['label'] }}</div>
                                        <div class="sl-ca__bar-track">
                                            <div class="sl-ca__bar-fill" style="width: {{ round(($bar['value'] / $maxCountry) * 100) }}%"></div>
                                        </div>
                                        <div class="sl-ca__bar-value">{{ $bar['value'] }}</div>
                                    </div>
                                @empty
                                    <p class="sl-ca__hint">No country data.</p>
                                @endforelse
                            </div>
                            <div class="sl-ca__chart-card">
                                <h4>Top Platforms</h4>
                                @php $maxDevice = $this->maxBarValue($deviceBars); @endphp
                                @forelse ($deviceBars as $bar)
                                    <div class="sl-ca__bar-row">
                                        <div class="sl-ca__bar-label" title="{{ $bar['label'] }}">{{ $bar['label'] }}</div>
                                        <div class="sl-ca__bar-track">
                                            <div class="sl-ca__bar-fill" style="width: {{ round(($bar['value'] / $maxDevice) * 100) }}%"></div>
                                        </div>
                                        <div class="sl-ca__bar-value">{{ $bar['value'] }}</div>
                                    </div>
                                @empty
                                    <p class="sl-ca__hint">No platform data.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif
                @else
                    <div class="sl-ca__section-title">Location List</div>
                    @if ($locationRows === [])
                        <div class="sl-ca__empty">No location rows for the selected profiles.</div>
                    @else
                        @php
                            $pagedRows = $this->paginatedLocationRows();
                            $totalPages = $this->locationTotalPages();
                            $totalRows = count($locationRows);
                        @endphp
                        <div style="overflow-x:auto;">
                            <table class="listing-table">
                                <thead>
                                    <tr>
                                        <th>Profile</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Device</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pagedRows as $index => $row)
                                        <tr wire:key="ca-loc-{{ $locationPage }}-{{ $index }}">
                                            <td>{{ $row['profile_name'] }}</td>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['location'] }}</td>
                                            <td>{{ $row['device'] }}</td>
                                            <td>
                                                <span class="sl-ca__badge {{ ($row['scan_type'] ?? '') === 'GPS' ? 'sl-ca__badge--gps' : 'sl-ca__badge--ip' }}">
                                                    {{ $row['scan_type'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="sl-ca__pager">
                            <div>
                                Showing {{ $this->locationShowingFrom() }}–{{ $this->locationShowingTo() }}
                                of {{ $totalRows }}
                                (page {{ $locationPage }} of {{ $totalPages }})
                            </div>
                            <div class="sl-ca__pager-btns">
                                <button
                                    type="button"
                                    class="sl-ca__pager-btn {{ $locationPage <= 1 ? 'is-disabled' : '' }}"
                                    wire:click="previousLocationPage"
                                    @disabled($locationPage <= 1)
                                >Prev</button>

                                @for ($page = 1; $page <= $totalPages; $page++)
                                    @if ($totalPages <= 7 || abs($page - $locationPage) <= 2 || $page === 1 || $page === $totalPages)
                                        <button
                                            type="button"
                                            class="sl-ca__pager-btn {{ $page === $locationPage ? 'is-active' : '' }}"
                                            wire:click="goToLocationPage({{ $page }})"
                                        >{{ $page }}</button>
                                    @elseif (abs($page - $locationPage) === 3)
                                        <span style="padding: 0 4px;">…</span>
                                    @endif
                                @endfor

                                <button
                                    type="button"
                                    class="sl-ca__pager-btn {{ $locationPage >= $totalPages ? 'is-disabled' : '' }}"
                                    wire:click="nextLocationPage"
                                    @disabled($locationPage >= $totalPages)
                                >Next</button>
                            </div>
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
</x-filament-panels::page>
