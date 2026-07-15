<x-filament-panels::page>
    {{ $this->content }}

    <style>
        .sl-part-tools {
            margin-top: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            align-items: flex-end;
            border-radius: 12px;
            border: 1px solid rgb(229 231 235);
            background: #fff;
            padding: 1rem;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.06);
        }
        .dark .sl-part-tools { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .sl-part-field { min-width: 220px; flex: 1; }
        .sl-part-label { display: block; margin-bottom: .35rem; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: rgb(107 114 128); }
        .sl-part-file { display: block; width: 100%; font-size: .875rem; }
        .sl-part-hint { margin: .35rem 0 0; font-size: .75rem; color: rgb(107 114 128); }
        .sl-part-btn {
            display: inline-flex;
            align-items: center;
            border-radius: 8px;
            padding: .55rem .9rem;
            font-size: .8125rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }
        .sl-part-btn-primary { background: #008C00; color: #fff; }
        .sl-part-btn-secondary { background: rgb(243 244 246); color: rgb(55 65 81); border: 1px solid rgb(209 213 219); }
        .dark .sl-part-btn-secondary { background: rgb(55 65 81); color: #fff; border-color: rgb(75 85 99); }
        .sl-part-table-wrap {
            margin-top: 1.25rem;
            overflow: hidden;
            border-radius: 12px;
            border: 1px solid rgb(229 231 235);
            background: #fff;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.06);
        }
        .dark .sl-part-table-wrap { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .sl-part-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .sl-part-table th {
            padding: .75rem 1rem;
            text-align: left;
            font-size: .6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #008C00;
            background: rgb(0 140 0 / .08);
        }
        .dark .sl-part-table th { background: rgb(0 140 0 / .15); }
        .sl-part-table td { padding: .75rem 1rem; border-top: 1px solid rgb(229 231 235); }
        .dark .sl-part-table td { border-color: rgb(55 65 81); }
        .sl-part-danger { color: rgb(220 38 38); background: none; border: none; cursor: pointer; font-weight: 600; }
        .sl-part-empty { padding: 1.5rem; text-align: center; color: rgb(107 114 128); }
    </style>

    <div class="sl-part-tools">
        <div class="sl-part-field">
            <label class="sl-part-label">Import CSV</label>
            <input type="file" wire:model="csvImportFile" accept=".csv,text/csv" class="sl-part-file">
            <p class="sl-part-hint">Columns: name, employer, due_date (Y-m-d or d/m/Y)</p>
        </div>
        <button type="button" wire:click="importCsv" wire:loading.attr="disabled" class="sl-part-btn sl-part-btn-primary">
            Import CSV
        </button>
        <button type="button" wire:click="exportParticipantsCsv" class="sl-part-btn sl-part-btn-secondary">
            Export CSV
        </button>
    </div>

    <div class="sl-part-table-wrap">
        <table class="sl-part-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Employer</th>
                    <th>Due</th>
                    <th>Done</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($participants as $participant)
                    <tr wire:key="participant-{{ $participant->participant_id }}">
                        <td>{{ $participant->name }}</td>
                        <td>{{ $participant->employer_cmp }}</td>
                        <td>{{ optional($participant->due_date)->format('d/m/Y') }}</td>
                        <td>{{ $participant->is_participated ? 'Yes' : 'No' }}</td>
                        <td style="text-align:right;">
                            <button
                                type="button"
                                wire:click="deleteParticipant({{ $participant->participant_id }})"
                                class="sl-part-danger"
                            >
                                Remove
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="sl-part-empty">No participants yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
