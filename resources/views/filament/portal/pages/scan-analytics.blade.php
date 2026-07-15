<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @if ($selectedProfileId && ! $chartData && ! $mapData && ! $scanList)
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No analytics key configured for this profile, or the Galatech API returned no data.
            </p>
        @endif

        @php $summary = $this->summaryCounts(); @endphp
        @if ($chartData || $mapData || $scanList)
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total scans</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $summary['total_scans'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Chart data keys</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $summary['chart_keys'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Map points</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $summary['map_points'] }}</p>
                </div>
            </div>
        @endif

        @if ($chartData)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Chart data</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Key</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($chartData as $key => $value)
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $key }}</td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                        {{ is_scalar($value) ? $value : json_encode($value) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($mapData)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Map data preview</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Key</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($mapData as $key => $value)
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $key }}</td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                        {{ is_scalar($value) ? $value : json_encode($value) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($scanList)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Scan list</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">#</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Location</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Device</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($this->normalizedScanRows() as $index => $row)
                                <tr>
                                    <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $index + 1 }}</td>
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
