<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AdminHome extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Admin Home';

    protected static ?string $title = 'Admin Control Panel';

    protected static ?int $navigationSort = -10;

    protected string $view = 'filament.pages.admin-home';

    public static function getNavigationGroup(): ?string
    {
        return null;
    }
}
