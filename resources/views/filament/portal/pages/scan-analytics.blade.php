<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @if ($selectedProfileId && ! $chartData && ! $mapData && ! $scanList)
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No analytics key configured for this profile, or the Galatech API returned no data.
            </p>
        @endif

        @if ($chartData)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Chart data</h3>
                <pre class="overflow-auto rounded-lg bg-gray-50 p-4 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ json_encode($chartData, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

        @if ($mapData)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Map data</h3>
                <pre class="overflow-auto rounded-lg bg-gray-50 p-4 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ json_encode($mapData, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

        @if ($scanList)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Scan list</h3>
                <pre class="overflow-auto rounded-lg bg-gray-50 p-4 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ json_encode($scanList, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>
</x-filament-panels::page>
