<x-filament-panels::page>
    @php
        $visitors = $this->paginatedVisitors();
        $totalPages = $this->totalPages();
        $total = $visitors?->total() ?? 0;
    @endphp

    {{-- Do NOT load public/styles/style.css here — it breaks Filament sidebar/main padding.
         Inline styles mirror legacy listing-table + form-submissions toolbar. --}}
    <style>
        .sl-vlog {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0 0 1.5rem;
            box-sizing: border-box;
        }
        .sl-vlog__panel {
            background: #fff;
            border: 1px solid #e5ebe5;
            border-radius: 4px;
            padding: 16px 18px 20px;
            box-sizing: border-box;
        }
        .sl-vlog__head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }
        .sl-vlog__title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #555755;
            line-height: 1.2;
        }
        .sl-vlog__profile {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 700;
            color: #333;
            line-height: 1.3;
        }
        .sl-vlog__toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 10px 12px;
            margin: 0 0 14px;
            padding: 0 0 12px;
            border-bottom: 1px solid #e5ebe5;
        }
        .sl-vlog__field {
            display: inline-flex;
            flex-direction: column;
            gap: 3px;
        }
        .sl-vlog__field label {
            font-size: 12px;
            font-weight: 700;
            color: #5f5f5f;
            line-height: 1.2;
        }
        .sl-vlog__field input.sl-vlog-range {
            width: 240px;
            height: 32px;
            padding: 4px 32px 4px 10px;
            border: 1px solid #cfcfcf;
            border-radius: 4px;
            font-size: 13px;
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23666666' stroke-width='2'%3E%3Crect x='3' y='4' width='18' height='18' rx='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E") no-repeat right 10px center;
            box-sizing: border-box;
            box-shadow: inset 2px 2px 3px rgba(0, 0, 0, .06);
            cursor: pointer;
        }
        .sl-vlog__field input.sl-vlog-range:focus {
            outline: none;
            border-color: #008901;
            box-shadow: 0 0 0 2px rgba(0, 137, 1, .15);
        }

        /* Flatpickr — clean range calendar (no clipping, light header) */
        .flatpickr-calendar {
            z-index: 10050 !important;
            width: 328px !important;
            max-width: calc(100vw - 24px) !important;
            font-family: Arial, Helvetica, sans-serif !important;
            border: 1px solid #d0d0d0 !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .18) !important;
            padding: 10px 12px 12px !important;
            overflow: visible !important;
            box-sizing: border-box !important;
        }
        .flatpickr-calendar.animate.open {
            overflow: visible !important;
        }
        .flatpickr-innerContainer,
        .flatpickr-rContainer,
        .flatpickr-days {
            width: 100% !important;
            overflow: visible !important;
        }
        .flatpickr-days {
            width: 304px !important;
        }
        .dayContainer {
            width: 304px !important;
            min-width: 304px !important;
            max-width: 304px !important;
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: flex-start !important;
            opacity: 1 !important;
        }
        .flatpickr-months {
            align-items: center;
            margin-bottom: 6px;
        }
        .flatpickr-months .flatpickr-month {
            background: transparent !important;
            color: #222 !important;
            height: 34px;
            border-radius: 0;
        }
        .flatpickr-current-month {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 4px;
            padding-top: 0 !important;
            height: 34px !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            color: #222 !important;
            font-weight: 700;
            font-size: 14px !important;
            background: transparent !important;
            border: 0 !important;
        }
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            fill: #444 !important;
            color: #444 !important;
            padding: 6px 8px;
            border-radius: 6px;
            top: 4px !important;
        }
        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            background: #f0f0f0 !important;
        }
        .flatpickr-months .flatpickr-prev-month:hover svg,
        .flatpickr-months .flatpickr-next-month:hover svg {
            fill: #008901 !important;
        }
        .flatpickr-weekdays {
            background: transparent !important;
            height: 28px;
        }
        .flatpickr-weekdaycontainer,
        span.flatpickr-weekday {
            color: #777 !important;
            font-weight: 700;
            font-size: 11px;
            max-width: none !important;
        }
        .flatpickr-day {
            border-radius: 8px !important;
            border: 0 !important;
            width: 14.2857% !important;
            max-width: 43.4px !important;
            height: 36px !important;
            line-height: 36px !important;
            margin: 1px 0 !important;
            color: #333;
            flex-basis: 14.2857% !important;
        }
        .flatpickr-day:hover,
        .flatpickr-day:focus {
            background: #e8f7e8 !important;
            border-color: transparent !important;
        }
        .flatpickr-day.today {
            border: 1px solid #008901 !important;
            font-weight: 700;
        }
        .flatpickr-day.today:hover {
            background: #e8f7e8 !important;
            color: #008901 !important;
        }
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover {
            background: #008901 !important;
            border-color: #008901 !important;
            color: #fff !important;
            box-shadow: none !important;
        }
        .flatpickr-day.inRange,
        .flatpickr-day.inRange:hover {
            background: #d9f0d9 !important;
            border-color: transparent !important;
            box-shadow: -5px 0 0 #d9f0d9, 5px 0 0 #d9f0d9 !important;
            color: #1a5c1a !important;
            border-radius: 0 !important;
        }
        .flatpickr-day.startRange {
            border-radius: 8px 0 0 8px !important;
        }
        .flatpickr-day.endRange {
            border-radius: 0 8px 8px 0 !important;
        }
        .flatpickr-day.startRange.endRange {
            border-radius: 8px !important;
        }
        .flatpickr-day.disabled,
        .flatpickr-day.flatpickr-disabled {
            color: #ccc !important;
        }
        .sl-vlog__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }
        .sl-vlog__btn {
            display: inline-block;
            background: linear-gradient(to bottom, #008901 0%, #007a01 100%);
            color: #fff !important;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            text-decoration: none !important;
            border: 1px solid #006201;
            border-radius: 4px;
            padding: 7px 14px;
            cursor: pointer;
            line-height: 1.2;
            height: 30px;
            box-sizing: border-box;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .12);
        }
        .sl-vlog__btn:hover { background: linear-gradient(to bottom, #00a001 0%, #008901 100%); }
        .sl-vlog__btn--ghost {
            background: #fff;
            color: #008901 !important;
            border-color: #008901;
            box-shadow: none;
        }
        .sl-vlog__btn--ghost:hover { background: #f3fff3; }
        .sl-vlog__btn:disabled,
        .sl-vlog__btn[disabled] {
            opacity: 0.45;
            cursor: default;
            pointer-events: none;
        }

        /* Legacy .listing-table — equal columns, matching header/cell alignment */
        .sl-vlog__table-wrap {
            width: 100%;
            overflow-x: auto;
            background: #fff;
        }
        .sl-vlog table.listing-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border-left: 1px solid #e5ebe5;
            border-top: 1px solid #e5ebe5;
            background: #fff;
            font-size: 13px;
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
        }
        .sl-vlog table.listing-table th,
        .sl-vlog table.listing-table td {
            border-right: 1px solid #e5ebe5;
            border-bottom: 1px solid #e5ebe5;
            padding: 10px 8px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
        .sl-vlog table.listing-table th {
            background: linear-gradient(to bottom, #ffffff 0%, #f4f4f4 100%);
            font-size: 13px;
            font-weight: 700;
            color: #222;
            padding: 12px 8px;
        }
        .sl-vlog table.listing-table th.sl-vlog__col-date,
        .sl-vlog table.listing-table td.sl-vlog__col-date {
            width: 20%;
            text-align: center !important;
            white-space: nowrap;
        }
        .sl-vlog table.listing-table th.sl-vlog__col-name,
        .sl-vlog table.listing-table td.sl-vlog__col-name,
        .sl-vlog table.listing-table th.sl-vlog__col-surname,
        .sl-vlog table.listing-table td.sl-vlog__col-surname,
        .sl-vlog table.listing-table th.sl-vlog__col-mobile,
        .sl-vlog table.listing-table td.sl-vlog__col-mobile {
            width: 18%;
            text-align: left !important;
        }
        .sl-vlog table.listing-table th.sl-vlog__col-email,
        .sl-vlog table.listing-table td.sl-vlog__col-email {
            width: 26%;
            text-align: left !important;
        }
        .sl-vlog__empty {
            text-align: center !important;
            font-size: 14px;
            font-weight: 700;
            padding: 28px 12px !important;
            color: #333;
            background: #fff !important;
        }

        /* Legacy-style pager (matches Code Balance) */
        .sl-vlog__pager {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 14px;
            font-size: 13px;
            color: #333;
        }
        .sl-vlog__pager select {
            border: 1px solid #aaa;
            padding: 2px 6px;
            min-width: 48px;
            background: #fff;
            font-size: 13px;
        }
        .sl-vlog__pager-arrow {
            border: 0;
            background: transparent;
            padding: 0 4px;
            cursor: pointer;
            font-size: 14px;
            color: #333;
            line-height: 1;
        }
        .sl-vlog__pager-arrow:disabled {
            opacity: 0.35;
            cursor: default;
        }
        .sl-vlog__meta {
            margin-top: 8px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        html.dark .sl-vlog__panel { background: rgb(17 24 39) !important; border-color: rgb(55 65 81) !important; }
        html.dark .sl-vlog { color: rgb(229 231 235) !important; }
        html.dark .sl-vlog__title,
        html.dark .sl-vlog__profile,
        html.dark .sl-vlog__empty,
        html.dark .sl-vlog__pager,
        html.dark .sl-vlog__meta { color: rgb(243 244 246) !important; }
        html.dark .sl-vlog__toolbar { border-color: rgb(55 65 81) !important; }
        html.dark .sl-vlog__field label { color: rgb(209 213 219) !important; }
        html.dark .sl-vlog__field input.sl-vlog-range,
        html.dark .sl-vlog__pager select {
            background-color: rgb(31 41 55) !important;
            border-color: rgb(75 85 99) !important;
            color: rgb(243 244 246) !important;
        }
        html.dark .sl-vlog table.listing-table {
            border-color: rgb(55 65 81) !important;
            background: rgb(17 24 39) !important;
        }
        html.dark .sl-vlog table.listing-table th,
        html.dark .sl-vlog table.listing-table td {
            border-color: rgb(55 65 81) !important;
            color: rgb(229 231 235) !important;
        }
        html.dark .sl-vlog table.listing-table th {
            background: rgb(31 41 55) !important;
            color: rgb(243 244 246) !important;
        }
        html.dark .sl-vlog__empty { background: transparent !important; }
        html.dark .sl-vlog__pager-arrow { color: rgb(229 231 235) !important; }
    </style>

    <div class="sl-vlog">
        <div class="sl-vlog__panel">
            <div class="sl-vlog__head">
                <div>
                    <h3 class="sl-vlog__title">Visitor Log</h3>
                    @if ($selectedProfileId)
                        <div class="sl-vlog__profile">
                            Profile {{ $selectedProfileId }}.
                            @if (filled($profileName)) {{ ucwords($profileName) }} @endif
                        </div>
                    @endif
                </div>
            </div>

            @if ($profileExpired)
                <div class="sl-vlog__empty">You can not perform this action on expired profile.</div>
            @elseif ($selectedProfileId)
                <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
                <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>

                <div class="sl-vlog__toolbar">
                    <div
                        class="sl-vlog__field"
                        wire:ignore
                        x-data="{ fp: null }"
                        x-init="
                            const pad = (n) => (n < 10 ? '0' + n : '' + n);
                            const fmt = (d) => pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear();
                            const from = @js($fromDate);
                            const to = @js($toDate);
                            const defaults = (from && to) ? [from, to] : [];

                            fp = flatpickr($refs.range, {
                                mode: 'range',
                                dateFormat: 'd/m/Y',
                                conjunction: ' — ',
                                allowInput: false,
                                clickOpens: true,
                                showMonths: 1,
                                appendTo: document.body,
                                defaultDate: defaults,
                                onChange(dates) {
                                    if (dates.length < 2) {
                                        return;
                                    }
                                    let a = dates[0];
                                    let b = dates[1];
                                    if (a.getTime() > b.getTime()) {
                                        const tmp = a; a = b; b = tmp;
                                    }
                                    $wire.applyDateRange(fmt(a), fmt(b));
                                }
                            });
                            window.__slVlogFp = fp;
                        "
                    >
                        <label for="sl-vlog-range">Date range</label>
                        <input
                            id="sl-vlog-range"
                            x-ref="range"
                            type="text"
                            class="sl-vlog-range"
                            placeholder="dd/mm/yyyy — dd/mm/yyyy"
                            autocomplete="off"
                            readonly
                            aria-label="Date range"
                        >
                    </div>
                    <button
                        type="button"
                        class="sl-vlog__btn sl-vlog__btn--ghost"
                        wire:click="clearDates"
                        @disabled($fromDate === '' && $toDate === '')
                        x-on:click="if (window.__slVlogFp) { window.__slVlogFp.clear(); }"
                    >Clear</button>

                    <div class="sl-vlog__actions">
                        <button type="button" class="sl-vlog__btn" wire:click="exportXlsx">Export</button>
                        <a class="sl-vlog__btn" href="{{ $this->returnToListUrl() }}">Return to list</a>
                    </div>
                </div>

                <div class="sl-vlog__table-wrap">
                    <table class="listing-table" width="100%" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="sl-vlog__col-date left-round"><strong>Date</strong></th>
                                <th class="sl-vlog__col-name"><strong>Name</strong></th>
                                <th class="sl-vlog__col-surname"><strong>Surname</strong></th>
                                <th class="sl-vlog__col-mobile"><strong>Mobile</strong></th>
                                <th class="sl-vlog__col-email right-round"><strong>Email</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($visitors && $visitors->count() > 0)
                                @foreach ($visitors as $contact)
                                    <tr wire:key="visitor-{{ $contact->id }}">
                                        <td class="sl-vlog__col-date">
                                            {{ $contact->created_at?->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="sl-vlog__col-name">{{ stripslashes((string) $contact->name) }}</td>
                                        <td class="sl-vlog__col-surname">{{ stripslashes((string) $contact->surname) }}</td>
                                        <td class="sl-vlog__col-mobile">{{ stripslashes((string) $contact->mobile) }}</td>
                                        <td class="sl-vlog__col-email">{{ stripslashes((string) $contact->email) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="sl-vlog__empty">No records found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($total > 0)
                    <div class="sl-vlog__pager">
                        <button
                            type="button"
                            class="sl-vlog__pager-arrow"
                            wire:click="goToPage(1)"
                            @disabled($page <= 1)
                            title="First page"
                        >&lt;&lt;</button>
                        <button
                            type="button"
                            class="sl-vlog__pager-arrow"
                            wire:click="previousPage"
                            @disabled($page <= 1)
                            title="Previous page"
                        >&lt;</button>
                        <span>Page</span>
                        <select wire:change="goToPage(parseInt($event.target.value))">
                            @for ($p = 1; $p <= $totalPages; $p++)
                                <option value="{{ $p }}" @selected($p === $page)>{{ $p }}</option>
                            @endfor
                        </select>
                        <span>of {{ $totalPages }}</span>
                        <button
                            type="button"
                            class="sl-vlog__pager-arrow"
                            wire:click="nextPage"
                            @disabled($page >= $totalPages)
                            title="Next page"
                        >&gt;</button>
                        <button
                            type="button"
                            class="sl-vlog__pager-arrow"
                            wire:click="goToPage({{ $totalPages }})"
                            @disabled($page >= $totalPages)
                            title="Last page"
                        >&gt;&gt;</button>
                    </div>
                    <div class="sl-vlog__meta">
                        Showing {{ $visitors->firstItem() }}–{{ $visitors->lastItem() }} of {{ $total }}
                    </div>
                @endif
            @else
                <div class="sl-vlog__empty">No records found</div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
