<?php

namespace App\Filament\Resources\FormBuilderOrders\Pages;

use App\Filament\Resources\FormBuilderOrders\FormBuilderOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListFormBuilderOrders extends ListRecords
{
    protected static string $resource = FormBuilderOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
