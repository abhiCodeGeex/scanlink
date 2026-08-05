<?php

namespace App\Filament\Resources\ResellerCodes\Widgets;

use App\Models\Client;
use App\Models\CodePurchase;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Admin: at-a-glance usage totals for a single reseller code (owned by a Client).
 * Rendered inline on the ResellerCodeHistory page with the owning client passed in.
 */
class ResellerCodeStats extends StatsOverviewWidget
{
    public ?Client $record = null;

    public function mount(?Client $record = null): void
    {
        $this->record = $record;
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $base = CodePurchase::query()->where('reseller_client_id', $this->record?->getKey());

        $uses = (clone $base)->count();
        $clients = (clone $base)->distinct('client_id')->count('client_id');
        $codes = (int) (clone $base)->sum('no_of_codes');
        $amount = (float) (clone $base)->sum('total_amount');

        return [
            Stat::make('Times used', number_format($uses))
                ->description('Purchases made with this code')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('primary'),

            Stat::make('Clients', number_format($clients))
                ->description('Distinct purchasing clients')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Codes purchased', number_format($codes))
                ->description('Total codes bought')
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('warning'),

            Stat::make('Total amount', '$'.number_format($amount, 2))
                ->description('Gross revenue generated')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
