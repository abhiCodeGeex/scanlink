<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-3">
        <a href="{{ \App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl('index') }}"
           class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-primary-500 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Master code list</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">View and manage all code profiles.</p>
            <p class="mt-4 text-3xl font-bold text-primary-600">{{ $activeProfiles }}</p>
        </a>

        <a href="{{ \App\Filament\Portal\Pages\CodeBalance::getUrl() }}"
           class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-primary-500 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Code balance</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Unused paid code slots available.</p>
            <p class="mt-4 text-3xl font-bold text-primary-600">{{ $codeBalance }}</p>
        </a>

        <a href="{{ \App\Filament\Portal\Pages\EditAccount::getUrl() }}"
           class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-primary-500 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My account</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ $clientName ?: 'Update company and user details' }}
            </p>
        </a>
    </div>

    @if ($expiringSoon > 0)
        <div class="mt-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-100">
            {{ $expiringSoon }} code(s) expire within 30 days.
            <a class="font-semibold underline" href="{{ \App\Filament\Portal\Pages\MultipleCodeRenewal::getUrl() }}">
                Renew codes
            </a>
        </div>
    @endif
</x-filament-panels::page>
