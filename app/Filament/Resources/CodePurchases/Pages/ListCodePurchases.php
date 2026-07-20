<?php

namespace App\Filament\Resources\CodePurchases\Pages;

use App\Filament\Resources\CodePurchases\CodePurchaseResource;
use Filament\Resources\Pages\ListRecords;

class ListCodePurchases extends ListRecords
{
    protected static string $resource = CodePurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
