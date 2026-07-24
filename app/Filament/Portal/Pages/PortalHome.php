<?php

namespace App\Filament\Portal\Pages;

use App\Enums\UserType;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class PortalHome extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Home';

    protected static ?string $title = 'Home';

    protected static ?string $slug = 'home';

    protected static ?int $navigationSort = -20;

    protected string $view = 'filament.portal.pages.home';

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
        $member = $this->currentClientUser();
        $name = trim(implode(' ', array_filter([
            $member?->first_name,
            $member?->last_name,
        ])));

        return $name !== '' ? "Welcome, {$name}" : 'Welcome';
    }
}
