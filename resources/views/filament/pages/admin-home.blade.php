<x-filament-panels::page>
    <div class="scanlink-home-grid">
        @foreach ([
            ['Add Client', route('filament.admin.resources.clients.create'), 'heroicon-o-user-plus'],
            ['Manage Client', route('filament.admin.resources.clients.index'), 'heroicon-o-building-office-2'],
            ['Sub Divide Client', \App\Filament\Pages\SubdivideClient::getUrl(), 'heroicon-o-arrows-right-left'],
        ] as [$label, $url, $icon])
            <a
                href="{{ $url }}"
                aria-label="{{ $label }}"
                class="scanlink-home-tile"
            >
                <x-filament::icon :icon="$icon" class="scanlink-home-tile-icon" aria-hidden="true" />
                <span class="scanlink-home-tile-label">{{ $label }}</span>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
