<?php

namespace App\Filament\Resources\Profiles\Pages;

use App\Filament\Resources\Profiles\Pages\Concerns\HasProfileQrActions;
use App\Filament\Resources\Profiles\Pages\Concerns\SyncsProfileAssets;
use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProfile extends EditRecord
{
    use HasProfileQrActions;
    use SyncsProfileAssets;

    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...$this->profileQrHeaderActions(),
            DeleteAction::make()
                ->label('Archive')
                ->modalHeading('Archive profile')
                ->action(fn () => $this->record->update(['deleted' => true])),
        ];
    }

    protected function afterSave(): void
    {
        $this->syncProfileAssets();
    }
}
