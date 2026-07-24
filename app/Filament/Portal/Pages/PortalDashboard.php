<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
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

    public function mount(): void
    {
        // Demo: land on Edit user profile (was Master Code List).
        $this->redirect(EditAccount::getUrl(panel: 'portal'), navigate: false);
        // $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }
}
