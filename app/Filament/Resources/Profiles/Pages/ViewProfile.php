<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\Pages\Concerns\HasProfileQrActions;
use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProfile extends ViewRecord
{
    use HasProfileQrActions;

    protected static string $resource = ProfileResource::class;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->loadMissing([
            'equipmentType',
            'logos',
            'videos',
            'weblinks',
            'pictures',
            'documents',
            'contacts',
            'qrImage',
            'client',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->profileQrHeaderActions(),
        ];
    }
}
