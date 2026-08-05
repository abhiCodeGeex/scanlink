<?php

namespace App\Filament\Portal\Pages;

use App\Enums\UserType;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Legacy parity with scanlink-laravel PortalDashboard: do not show a standalone
 * "signed in" home screen — send users to Edit user profile instead.
 */
class PortalHome extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Home';

    protected static ?string $title = 'Home';

    protected static ?string $slug = 'home';

    /** Hide from sidebar — Home is not a real destination (matches Laravel). */
    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = -20;

    protected string $view = 'filament.portal.pages.home';

    public function mount(): void
    {
        $this->redirect(EditAccount::getUrl(panel: 'portal'), navigate: false);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if (! in_array($user->user_type, [UserType::Portal, UserType::Voc], true)) {
            return false;
        }

        return $user->clientMemberships()->active()->exists();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Home';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }
}
