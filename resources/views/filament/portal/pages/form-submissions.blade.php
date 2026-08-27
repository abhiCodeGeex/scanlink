<x-filament-panels::page>
    @php
        $sessions = $this->paginatedSessions();
    @endphp

    {{-- Do NOT load public/styles/style.css here — it breaks Filament sidebar/main padding. --}}
    <style>
        .sl-fslog {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0 0 1.5rem;
            box-sizing: border-box;
        }
        .sl-fslog__panel {
            background: #fff;
            border: 1px solid #e5ebe5;
            border-radius: 4px;
            padding: 18px 22px 24px;
            box-sizing: border-box;
        }
        .sl-fslog__head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }
        .sl-fslog__title {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #555755;
            line-height: 1.25;
        }
        .sl-fslog__profile {
            margin-top: 4px;
            font-size: 14px;
            color: #444;
        }
        .sl-fslog__toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            gap: 10px 14px;
            margin: 0 0 16px;
            padding: 4px 0 12px;
            border-bottom: 1px solid #eee;
        }
        .sl-fslog__toolbar label {
            font-size: 13px;
            font-weight: bold;
            color: #5f5f5f;
            margin-right: 4px;
        }
        .sl-fslog__toolbar input[type="text"] {
            width: 100px;
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 13px;
            background: #fff;
        }
        .sl-fslog__btn {
            display: inline-block;
            background: #008901;
            color: #fff !important;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            text-decoration: none !important;
            border: 1px solid #006201;
            border-radius: 5px;
            padding: 8px 14px;
            cursor: pointer;
            line-height: 1.2;
        }
        .sl-fslog__btn:hover { background: #00a001; }
        .sl-fslog__btn--wide { min-width: 115px; text-align: center; }
        /* Wide logs scroll horizontally inside the card instead of pushing the page sideways. */
        .sl-fslog__table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
        }
        .sl-fslog table.listing-table {
            width: max-content;
            min-width: 100%;
            border-collapse: collapse;
            background: #fff;
            font-size: 13px;
        }
        /* Neutralise the legacy width="8%"-style attributes: percentages of a max-content
           table blow single columns up to thousands of pixels of empty space. */
        .sl-fslog table.listing-table th { white-space: nowrap; width: auto !important; }
        .sl-fslog table.listing-table td { max-width: 240px; min-width: 70px; }
        .sl-fslog table.listing-table th {
            background: #e8e8e8;
            color: #333;
            font-weight: bold;
            padding: 10px 8px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        .sl-fslog table.listing-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .sl-fslog table.listing-table tr:nth-child(even) td { background: #f7faf7; }
        .sl-fslog__empty {
            text-align: center;
            color: #5286BE;
            font-size: 16px;
            font-weight: bold;
            padding: 28px 12px;
        }
        .sl-fslog__paging {
            text-align: center;
            padding: 14px 0 0;
            font-size: 13px;
        }
        .sl-fslog__paging button {
            margin: 0 4px;
            background: #008901;
            color: #fff;
            border: 0;
            border-radius: 4px;
            padding: 4px 10px;
            cursor: pointer;
            font-weight: bold;
        }
        .sl-fslog .form-submission-table { width: auto; margin: 0 auto; }
        .sl-fslog .form-submission-table td {
            border: 0 !important;
            padding: 0 6px !important;
            background: transparent !important;
        }
        .sl-fslog .form-submission-table a {
            background: url("{{ asset('images/icons_submissions.png') }}") no-repeat left top;
            width: 22px;
            height: 24px;
            display: block;
            text-indent: -9999px;
            overflow: hidden;
        }
        .sl-fslog .form-submission-table a.view { background-position: -11px -24px; }
        .sl-fslog .form-submission-table a.view:hover { background-position: -11px 0; }
        .sl-fslog .form-submission-table a.download { background-position: -53px -24px; }
        .sl-fslog .form-submission-table a.download:hover { background-position: -53px 0; }
        .sl-fslog .form-submission-table a.delete { background-position: -104px -25px; }
        .sl-fslog .form-submission-table a.delete:hover { background-position: -104px 0; }
        html.dark .sl-fslog__panel { background: rgb(17 24 39) !important; border-color: rgb(55 65 81) !important; }
        html.dark .sl-fslog,
        html.dark .sl-fslog__title { color: rgb(243 244 246) !important; }
        html.dark .sl-fslog input,
        html.dark .sl-fslog select { background: rgb(31 41 55) !important; border-color: rgb(75 85 99) !important; color: rgb(243 244 246) !important; }
        html.dark .sl-fslog table th { background: rgb(31 41 55) !important; color: rgb(243 244 246) !important; }
        html.dark .sl-fslog table td { border-color: rgb(55 65 81) !important; color: rgb(229 231 235) !important; }
    </style>

    <div class="sl-fslog">
        <div class="sl-fslog__panel">
            <div class="sl-fslog__head">
                <div>
                    <h3 class="sl-fslog__title">Form Submission Log</h3>
                    @if ($selectedProfileId)
                        <div class="sl-fslog__profile">
                            Profile {{ $selectedProfileId }}.
                            @if ($profileName) {{ $profileName }} @endif
                        </div>
                    @endif
                </div>
            </div>

            @if ($selectedProfileId && $profileExpired)
                <div class="sl-fslog__empty">You can not perform this action on expired profile.</div>
            @elseif ($selectedProfileId)
                <div class="sl-fslog__toolbar">
                    <button type="button" class="sl-fslog__btn sl-fslog__btn--wide" wire:click="downloadAll">DOWNLOAD ALL</button>
                    <button type="button" class="sl-fslog__btn" wire:click="exportXlsx">EXPORT</button>

                    <span wire:ignore x-data="slFsLogDatePicker({ property: 'fromDate', initial: @js($fromDate) })">
                        <label for="from_date">From Date</label>
                        <input id="from_date" x-ref="input" type="text" placeholder="dd/mm/yyyy" autocomplete="off">
                    </span>
                    <span wire:ignore x-data="slFsLogDatePicker({ property: 'toDate', initial: @js($toDate) })">
                        <label for="to_date">To Date</label>
                        <input id="to_date" x-ref="input" type="text" placeholder="dd/mm/yyyy" autocomplete="off">
                    </span>

                    <button type="button" class="sl-fslog__btn" wire:click="search">SEARCH</button>
                    <a class="sl-fslog__btn" href="{{ $this->returnToListUrl() }}">RETURN TO LIST</a>
                </div>

                {{-- Wide logs (many columns) scroll inside this container instead of
                     overflowing the card / scrolling the whole page sideways. --}}
                <div class="sl-fslog__table-scroll">
                <table class="listing-table" width="100%" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th width="8%" align="center">No.</th>
                            <th width="14%">Date / Time</th>
                            @foreach ($logQuestions as $logQ)
                                @if ((int) $logQ->question_type_id === 25)
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Venue Address</th>
                                    <th>Location Description/Type</th>
                                    <th>Vehicle Reg No</th>
                                @else
                                    <th>{{ $logQ->log_columntitle ?: \Illuminate\Support\Str::limit(strip_tags((string) $logQ->question_text), 40) }}</th>
                                @endif
                            @endforeach
                            <th width="16%" style="text-align:center;">Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($sessions && $sessions->count() > 0)
                            @foreach ($sessions as $index => $session)
                                @php
                                    $rowNum = ($sessions->currentPage() - 1) * $sessions->perPage() + $index + 1;
                                @endphp
                                <tr wire:key="fs-{{ $session->session_id }}">
                                    <td align="center">{{ $rowNum }}</td>
                                    <td>
                                        {{ \Illuminate\Support\Carbon::parse($session->submitted_at)->format('d/m/Y H:i') }}
                                    </td>
                                    @foreach ($logQuestions as $logQ)
                                        @php $raw = $this->answerForSession($session, (int) $logQ->question_id); @endphp
                                        @if ((int) $logQ->question_type_id === 25)
                                            @foreach ($this->covidCells($raw) as $cell)
                                                <td align="center">{!! nl2br(e($cell)) !!}</td>
                                            @endforeach
                                        @else
                                            <td align="center">{{ $raw }}</td>
                                        @endif
                                    @endforeach
                                    <td>
                                        <table class="form-submission-table" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>
                                                    <a class="view" href="{{ $this->viewUrl($session->session_id) }}" title="View Submission Response">View</a>
                                                </td>
                                                <td>
                                                    <a class="download" href="#" wire:click.prevent="downloadSessionPdf('{{ $session->session_id }}')" title="Download Submission Response">Download</a>
                                                </td>
                                                @if ($this->canDeleteCode())
                                                    <td>
                                                        <a
                                                            class="delete"
                                                            href="#"
                                                            title="Delete Response"
                                                            x-on:click.prevent="window.slConfirm('Are you sure to delete this?').then(ok => ok && $wire.deleteSession('{{ $session->session_id }}'))"
                                                        >Delete</a>
                                                    </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="{{ 3 + $logQuestions->sum(fn ($q) => (int) $q->question_type_id === 25 ? 5 : 1) }}" class="sl-fslog__empty">
                                    No Results Found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                </div>

                @if ($sessions && $sessions->hasPages())
                    <div class="sl-fslog__paging">
                        <span>Page {{ $sessions->currentPage() }} of {{ $sessions->lastPage() }}</span>
                        @if (! $sessions->onFirstPage())
                            <button type="button" wire:click="goToPage({{ $sessions->currentPage() - 1 }})">Prev</button>
                        @endif
                        @if ($sessions->hasMorePages())
                            <button type="button" wire:click="goToPage({{ $sessions->currentPage() + 1 }})">Next</button>
                        @endif
                    </div>
                @endif
            @else
                <div class="sl-fslog__empty">No Form Submissions Found!</div>
            @endif
        </div>
    </div>

    {{-- Date-picker assets + slFsLogDatePicker are loaded panel-wide via the
         filament.hooks.flatpickr-datepicker HEAD_END hook (SPA navigation skips
         page-level <script> tags, which is why they lived here before and only
         worked after a hard refresh). --}}
</x-filament-panels::page>
