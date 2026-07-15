<x-filament-panels::page>
    <div class="space-y-6">
        @php $summary = $this->orderSummary(); @endphp
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Order summary</h3>
            <dl class="grid gap-2 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Label size</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ ucfirst($summary['size']) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Quantity</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $summary['quantity'] }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Unit price</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">${{ number_format($summary['unit_price'], 2) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Subtotal</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">${{ number_format($summary['subtotal'], 2) }}</dd>
                </div>
            </dl>
            <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">{{ $summary['postage_note'] }}</p>
        </div>

        {{ $this->content }}
    </div>
</x-filament-panels::page>
