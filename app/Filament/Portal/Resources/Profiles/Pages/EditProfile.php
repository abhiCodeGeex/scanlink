<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyProfileEditorLayout;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Filament\Resources\Profiles\Pages\Concerns\HasProfileQrActions;
use App\Filament\Resources\Profiles\Pages\Concerns\SyncsProfileAssets;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProfile extends EditRecord
{
    use HandlesDatabaseSaveFailures;
    use HasLegacyProfileEditorLayout;
    use HasProfileQrActions;
    use InteractsWithClientMembership;
    use SyncsProfileAssets;

    protected static string $resource = ProfileResource::class;

    public function getView(): string
    {
        return 'filament.portal.profiles.legacy-profile-page';
    }

    protected function canDownloadQr(): bool
    {
        return $this->canDownload();
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->loadMissing([
            'client',
            'equipmentType',
            'contacts',
            'qrImage',
            'weblinks',
        ]);
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $type = $this->record->equipmentType?->slag;
        $name = $this->record->equipmentType?->name;

        if ($type === 'code') {
            return 'Edit URL Link Code';
        }

        return $name ? 'Edit '.$name.' Code' : 'Edit Code Profile';
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->profileQrHeaderActions(),
            DeleteAction::make()
                ->label('Archive')
                ->modalHeading('Archive profile')
                ->visible(fn (): bool => ProfileResource::canDelete($this->record))
                ->action(fn () => $this->record->update(['deleted' => true])),
        ];
    }

    protected function afterSave(): void
    {
        $this->syncProfileAssets();
    }
}
