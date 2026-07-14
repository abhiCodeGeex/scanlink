<x-filament-panels::page>
    {{ $this->content }}

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
