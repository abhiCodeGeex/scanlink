<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Models\Profile;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CodeBalance extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = 'Code Balance';

    protected static ?string $title = 'Code Balance';

    protected static ?string $slug = 'code-balance';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.portal.pages.code-balance';

    public int $purchasedCodes = 0;

    public int $usedProfiles = 0;

    public int $remainingCodes = 0;

    public static function getNavigationGroup(): ?string
    {
        return 'My Account';
    }

    public function mount(): void
    {
        $client = $this->currentClient();

        if (! $client) {
            return;
        }

        $this->usedProfiles = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->count();

        $this->purchasedCodes = (int) $client->codePurchases()->sum('no_of_codes');
        $this->remainingCodes = max(0, $this->purchasedCodes - $this->usedProfiles);
    }
}
