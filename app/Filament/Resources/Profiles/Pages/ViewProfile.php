<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\Pages\Concerns\HasProfileQrActions;
use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProfile extends ViewRecord
{
    use HasProfileQrActions;

    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->profileQrHeaderActions(),
            EditAction::make(),
        ];
    }
}
