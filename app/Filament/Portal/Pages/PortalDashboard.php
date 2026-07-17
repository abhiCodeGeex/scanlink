<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\Profile;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class PortalDashboard extends Page
{
    use InteractsWithClientMembership;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Client Dashboard';

    protected static ?string $slug = 'dashboard';

    /** Live "Dashboard" is Master Code List — keep this page off the sidebar. */
    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = -20;

    protected string $view = 'filament.portal.pages.portal-dashboard';

    public int $activeProfiles = 0;

    public int $codeBalance = 0;

    public int $expiringSoon = 0;

    public string $clientName = '';

    public function mount(): void
    {
        $client = $this->currentClient();

        if (! $client) {
            return;
        }

        $this->clientName = (string) $client->client_name;
        $this->activeProfiles = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->count();

        $purchased = (int) $client->codePurchases()->sum('no_of_codes');
        $this->codeBalance = max(0, $purchased - $this->activeProfiles);

        $this->expiringSoon = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->whereBetween('expired_at', [now(), now()->addDays(30)])
            ->count();
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }
}
