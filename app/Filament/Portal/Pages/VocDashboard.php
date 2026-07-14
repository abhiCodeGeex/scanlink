<?php

namespace App\Filament\Portal\Pages;

use App\Enums\UserType;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class VocDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $navigationLabel = 'VOC Dashboard';

    protected static ?string $title = 'VOC Dashboard';

    protected static ?string $slug = 'voc-dashboard';

    protected static ?int $navigationSort = -10;

    protected string $view = 'filament.portal.pages.voc-dashboard';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->user_type === UserType::Voc;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'VOC';
    }
}
