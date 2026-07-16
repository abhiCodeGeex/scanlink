<x-filament-panels::page>
    <div class="scanlink-dashboard-grid">
        @foreach ($this->getDashboardTiles() as $tile)
            <a
                href="{{ $tile['url'] }}"
                aria-label="{{ $tile['label'] }}"
                class="scanlink-dashboard-tile"
            >
                <x-filament::icon :icon="$tile['icon']" class="scanlink-dashboard-tile-icon" aria-hidden="true" />
                <span class="scanlink-dashboard-tile-label">{{ $tile['label'] }}</span>
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
