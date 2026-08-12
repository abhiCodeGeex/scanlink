<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Filament\Resources\Profiles\Pages\Concerns\HasProfileQrActions;
use Filament\Resources\Pages\ViewRecord;

class ViewProfile extends ViewRecord
{
    use HasProfileQrActions;
    use InteractsWithClientMembership;

    protected static string $resource = ProfileResource::class;

    protected function canDownloadQr(): bool
    {
        return $this->canDownload();
    }

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
