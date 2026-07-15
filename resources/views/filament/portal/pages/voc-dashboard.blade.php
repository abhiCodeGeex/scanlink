<x-filament-panels::page>
    <div class="space-y-6">
        @if ($isVocUser)
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Profiles linked to your VOC account.
            </p>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-300">
                VOC and survey profiles for your organisation.
            </p>
        @endif

        @if ($profiles->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Profile</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Links</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($profiles as $profile)
                            <tr wire:key="voc-profile-{{ $profile->id }}">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                    #{{ $profile->id }} — {{ $profile->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $profile->equipmentType?->name ?? 'Profile' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($viewUrl = $this->profileViewUrl($profile))
                                        <a href="{{ $viewUrl }}" class="text-primary-600 hover:underline">Portal view</a>
                                        <span class="mx-1 text-gray-400">|</span>
                                    @endif
                                    <a href="{{ $this->profileScanUrl($profile) }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline">Public scan</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">No VOC profiles are linked to your account.</p>
        @endif

        @if ($documents->isNotEmpty())
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">VOC documents</h3>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($documents as $document)
                        <li class="flex items-center justify-between py-3 text-sm">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $document->name ?: 'Document' }}</p>
                                <p class="text-gray-500 dark:text-gray-400">
                                    Profile #{{ $document->profile_id }}
                                    @if ($document->expiry_date)
                                        — expires {{ $document->expiry_date->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                            @if ($document->file_name)
                                <a href="{{ asset('storage/'.ltrim(str_replace(['storage/', 'public/'], '', $document->file_name), '/')) }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline">Download</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-filament-panels::page>
