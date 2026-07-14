<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Resources\Profiles\ProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfiles extends ListRecords
{
    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Master Code List';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add profile')
                ->visible(fn (): bool => ProfileResource::canCreate()),
        ];
    }
}
