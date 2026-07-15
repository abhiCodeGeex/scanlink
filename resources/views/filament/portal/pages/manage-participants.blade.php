<x-filament-panels::page>
    {{ $this->content }}

    <div class="mt-6 flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
        <div class="min-w-[220px] flex-1">
            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Import CSV</label>
            <input type="file" wire:model="csvImportFile" accept=".csv,text/csv" class="block w-full text-sm">
            <p class="mt-1 text-xs text-gray-500">Columns: name, employer, due_date (Y-m-d or d/m/Y)</p>
        </div>
        <button type="button" wire:click="importCsv" wire:loading.attr="disabled" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white">
            Import CSV
        </button>
        <button type="button" wire:click="exportParticipantsCsv" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold dark:border-gray-600">
            Export CSV
        </button>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Employer</th>
                    <th class="px-4 py-2 text-left">Due</th>
                    <th class="px-4 py-2 text-left">Done</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($participants as $participant)
                    <tr>
                        <td class="px-4 py-2">{{ $participant->name }}</td>
                        <td class="px-4 py-2">{{ $participant->employer_cmp }}</td>
                        <td class="px-4 py-2">{{ optional($participant->due_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $participant->is_participated ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-2 text-right">
                            <button
                                type="button"
                                wire:click="deleteParticipant({{ $participant->participant_id }})"
                                class="text-danger-600 hover:underline"
                            >
                                Remove
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No participants yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
