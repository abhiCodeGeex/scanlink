<x-filament-panels::page>
    <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
        Select expired or expiring codes (within 30 days) and renew them in bulk.
    </p>

    {{-- Legacy legend: Expired / Expires within 3 days / Expires within 30 days --}}
    <div class="mb-4 flex flex-wrap items-center gap-4 text-xs text-gray-600 dark:text-gray-400">
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block h-3 w-3 rounded-sm bg-danger-500"></span> Expired
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block h-3 w-3 rounded-sm bg-gray-400"></span> Expires within 3 days
        </span>
        <span class="inline-flex items-center gap-1.5">
            <span class="inline-block h-3 w-3 rounded-sm bg-warning-500"></span> Expires within 30 days
        </span>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
