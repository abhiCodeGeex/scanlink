<x-filament-panels::page>
    <div class="space-y-6">
        <div class="max-w-md">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Profile</label>
            <select
                wire:model.live="selectedProfileId"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-900"
            >
                <option value="">Select profile</option>
                @foreach ($this->clientProfileOptions() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        @if ($visitors->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Mobile</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($visitors as $visitor)
                            <tr wire:key="visitor-{{ $visitor->id }}">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $visitor->user_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $visitor->user_email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $visitor->user_mobile }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $visitor->entry_date?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif ($selectedProfileId)
            <p class="text-sm text-gray-500 dark:text-gray-400">No visitor contacts recorded yet.</p>
        @endif
    </div>
</x-filament-panels::page>
