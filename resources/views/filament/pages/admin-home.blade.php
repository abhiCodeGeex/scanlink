<x-filament-panels::page>
    <div class="scanlink-home-grid">
        @foreach ([
            ['Add Client', route('filament.admin.resources.clients.create'), 'heroicon-o-user-plus'],
            ['Manage Client', route('filament.admin.resources.clients.index'), 'heroicon-o-building-office-2'],
            ['Sub Divide Client', \App\Filament\Pages\SubdivideClient::getUrl(), 'heroicon-o-arrows-right-left'],
            ['Manage Product', route('filament.admin.resources.profiles.index'), 'heroicon-o-qr-code'],
            ['Manage Order', route('filament.admin.resources.orders.index'), 'heroicon-o-truck'],
            ['Manage Code Order', route('filament.admin.resources.code-purchases.index'), 'heroicon-o-shopping-cart'],
            ['Manage Form Builder Order', route('filament.admin.resources.form-builder-orders.index'), 'heroicon-o-document-text'],
            ['Global Settings', \App\Filament\Pages\GlobalSettings::getUrl(), 'heroicon-o-cog-6-tooth'],
            ['Code Pricing', \App\Filament\Pages\CodePricing::getUrl(), 'heroicon-o-currency-dollar'],
            ['Manage Testimonial', route('filament.admin.resources.testimonials.index'), 'heroicon-o-chat-bubble-left-right'],
            ['Manage Gallery', route('filament.admin.resources.galleries.index'), 'heroicon-o-photo'],
            ['Change Password', \App\Filament\Pages\ChangePassword::getUrl(), 'heroicon-o-key'],
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
