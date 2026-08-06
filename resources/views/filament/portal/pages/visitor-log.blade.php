<x-filament-panels::page>
    {{-- Do NOT load public/styles/style.css here — it breaks Filament sidebar/main padding. --}}
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
            padding: 18px 22px 24px;
            box-sizing: border-box;
        }
        .sl-vlog__head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
        }
        .sl-vlog__title {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #555755;
            line-height: 1.25;
        }
        .sl-vlog__profile {
            margin-top: 6px;
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }
        .sl-vlog__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }
        .sl-vlog__btn {
            display: inline-block;
            background: #008901;
            color: #fff !important;
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            text-decoration: none !important;
            border: 1px solid #006201;
            border-radius: 5px;
            padding: 8px 16px;
            cursor: pointer;
            line-height: 1.2;
        }
        .sl-vlog__btn:hover { background: #00a001; }
        .sl-vlog table.listing-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            font-size: 13px;
        }
        .sl-vlog table.listing-table th {
            background: #e8e8e8;
            color: #333;
            font-weight: bold;
            padding: 10px 8px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        .sl-vlog table.listing-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .sl-vlog table.listing-table tr:nth-child(even) td { background: #f7faf7; }
        .sl-vlog__empty {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding: 28px 12px;
            color: #333;
        }
        html.dark .sl-vlog__panel { background: rgb(17 24 39) !important; border-color: rgb(55 65 81) !important; }
        html.dark .sl-vlog { color: rgb(229 231 235) !important; }
        html.dark .sl-vlog__title,
        html.dark .sl-vlog__empty { color: rgb(243 244 246) !important; }
        html.dark .sl-vlog table.listing-table th { background: rgb(31 41 55) !important; color: rgb(243 244 246) !important; border-color: rgb(55 65 81) !important; }
        html.dark .sl-vlog table.listing-table td { border-color: rgb(55 65 81) !important; color: rgb(229 231 235) !important; }
        html.dark .sl-vlog table.listing-table tr:nth-child(even) td { background: rgb(31 41 55) !important; }
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
                @if ($selectedProfileId)
                    <div class="sl-vlog__actions">
                        @unless ($profileExpired)
                            <button type="button" class="sl-vlog__btn" wire:click="exportXlsx">EXPORT</button>
                        @endunless
                        <a class="sl-vlog__btn" href="{{ $this->returnToListUrl() }}">RETURN TO LIST</a>
                    </div>
                @endif
            </div>

            @if ($profileExpired)
                <div class="sl-vlog__empty">You can not perform this action on expired profile.</div>
            @elseif ($selectedProfileId)
                <table class="listing-table" width="100%" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th width="20%" align="center">Date</th>
                            <th width="20%">Name</th>
                            <th width="20%">Surname</th>
                            <th width="20%">Mobile</th>
                            <th width="20%">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visitors as $contact)
                            <tr wire:key="visitor-{{ $contact->id }}">
                                <td align="center">
                                    {{ $contact->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td>{{ stripslashes((string) $contact->name) }}</td>
                                <td>{{ stripslashes((string) $contact->surname) }}</td>
                                <td>{{ stripslashes((string) $contact->mobile) }}</td>
                                <td>{{ stripslashes((string) $contact->email) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="sl-vlog__empty">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <div class="sl-vlog__empty">No records found</div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
