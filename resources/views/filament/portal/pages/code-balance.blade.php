<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchased codes</h3>
            <p class="mt-2 text-3xl font-bold text-primary-600">{{ $purchasedCodes }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Used profiles</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $usedProfiles }}</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Remaining codes</h3>
            <p class="mt-2 text-3xl font-bold text-primary-600">{{ $remainingCodes }}</p>
        </div>
    </div>
</x-filament-panels::page>
