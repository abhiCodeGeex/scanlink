<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @if ($selectedProfileIds !== [] && $combinedScanRows === [])
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No analytics keys configured for the selected profiles, or the Galatech API returned no data.
            </p>
        @endif

        @if ($combinedScanRows !== [])
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">
                    Combined scan list ({{ count($combinedScanRows) }} rows)
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Profile</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Location</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Device</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($combinedScanRows as $index => $row)
                                <tr wire:key="cumulative-row-{{ $index }}">
                                    <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $row['profile_name'] ?? '' }}</td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $row['date'] ?? '' }}</td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $row['location'] ?? '' }}</td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $row['device'] ?? '' }}</td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $row['details'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
