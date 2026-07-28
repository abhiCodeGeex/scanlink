<?php

namespace App\Filament\Resources\ResellerCodes\Pages;

use App\Filament\Resources\ResellerCodes\ResellerCodeResource;
use Filament\Resources\Pages\ListRecords;

class ListResellerCodes extends ListRecords
{
    protected static string $resource = ResellerCodeResource::class;

    protected static ?string $title = 'Reseller Codes';
}
