<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\CodePurchases\CodePurchaseResource;
use App\Filament\Resources\FormBuilderOrders\FormBuilderOrderResource;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AdminHome extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Admin Control Panel';

    protected static ?string $slug = 'dashboard';

    protected static ?int $navigationSort = -10;

    protected string $view = 'filament.pages.admin-home';

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    /**
     * Quick-launch tiles matching items available in the admin sidebar.
     *
     * @return list<array{label: string, url: string, icon: string}>
     */
    public function getDashboardTiles(): array
    {
        return [
            [
                'label' => 'Add Client',
                'url' => ClientResource::getUrl('create'),
                'icon' => 'heroicon-o-user-plus',
            ],
            [
                'label' => 'Manage Client',
                'url' => ClientResource::getUrl('index'),
                'icon' => 'heroicon-o-building-office-2',
            ],
            [
                'label' => 'Sub Divide Client',
                'url' => SubdivideClient::getUrl(),
                'icon' => 'heroicon-o-arrows-right-left',
            ],
            [
                'label' => 'Manage Order',
                'url' => OrderResource::getUrl('index'),
                'icon' => 'heroicon-o-truck',
            ],
            [
                'label' => 'Manage Code Order',
                'url' => CodePurchaseResource::getUrl('index'),
                'icon' => 'heroicon-o-shopping-cart',
            ],
            [
                'label' => 'Manage Form Builder Order',
                'url' => FormBuilderOrderResource::getUrl('index'),
                'icon' => 'heroicon-o-document-text',
            ],
        ];
    }
}
