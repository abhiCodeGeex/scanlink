<x-filament-panels::page>
    {{-- Code owner only — code name + status live in the page heading --}}
    <x-filament::section>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1.25rem;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:0.875rem;min-width:0;">
                <div class="sl-rch-avatar" style="display:flex;height:2.75rem;width:2.75rem;flex-shrink:0;align-items:center;justify-content:center;border-radius:0.5rem;background:#ecfdf5;color:#059669;">
                    <x-filament::icon icon="heroicon-o-building-office-2" class="h-5 w-5" />
                </div>
                <div style="min-width:0;">
                    <div class="sl-rch-muted" style="font-size:0.75rem;font-weight:500;letter-spacing:0.04em;text-transform:uppercase;color:#6b7280;">Code owner</div>
                    <div class="sl-rch-name" style="margin-top:0.125rem;font-size:1rem;font-weight:600;color:#111827;">{{ $this->record->client_name }}</div>
                    @if ($this->record->email)
                        <div class="sl-rch-muted" style="margin-top:0.125rem;font-size:0.875rem;color:#6b7280;">{{ $this->record->email }}</div>
                    @endif
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- KPI stat widgets --}}
    @livewire(\App\Filament\Resources\ResellerCodes\Widgets\ResellerCodeStats::class, ['record' => $this->record])

    {{-- Per-client usage table --}}
    {{ $this->table }}
</x-filament-panels::page>
