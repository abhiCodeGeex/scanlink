<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\CodePurchases\CodePurchaseResource;
use App\Filament\Resources\FormBuilderOrders\FormBuilderOrderResource;
use App\Filament\Resources\Galleries\GalleryResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Profiles\ProfileResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
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
     * Quick-launch tiles for areas hidden from the sidebar or primary daily workflows.
     * Settings and Reports items live in the sidebar only.
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
                'label' => 'Manage Product',
                'url' => ProfileResource::getUrl('index'),
                'icon' => 'heroicon-o-squares-2x2',
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
            [
                'label' => 'Manage Testimonial',
                'url' => TestimonialResource::getUrl('index'),
                'icon' => 'heroicon-o-chat-bubble-left-right',
            ],
            [
                'label' => 'Manage Gallery',
                'url' => GalleryResource::getUrl('index'),
                'icon' => 'heroicon-o-photo',
            ],
        ];
    }
}
