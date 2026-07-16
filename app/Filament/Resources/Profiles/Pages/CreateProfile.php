<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Profiles\Pages\Concerns\SyncsProfileAssets;
use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProfile extends CreateRecord
{
    use HandlesDatabaseSaveFailures;
    use SyncsProfileAssets;

    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Add Profile';

    protected function afterCreate(): void
    {
        $this->syncProfileAssets();
    }
}
