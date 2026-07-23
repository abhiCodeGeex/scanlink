<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyFormBuilderSidebar;
use App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyProfileEditorLayout;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Filament\Resources\Profiles\Pages\Concerns\HasProfileQrActions;
use App\Filament\Resources\Profiles\Pages\Concerns\SyncsProfileAssets;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProfile extends EditRecord
{
    use HandlesDatabaseSaveFailures;
    use HasLegacyFormBuilderSidebar;
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
            'logos',
            'pictures',
            'documents',
            'videos',
        ]);

        $this->loadFormBuilderSidebarState($this->record);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $profile = $this->record->loadMissing(['logos', 'logosExtra', 'videos', 'equipmentType']);

        $logo = $profile->logos->first();
        $data['logo_upload'] = $logo?->logo_name;

        $logoExtra = $profile->logosExtra->first();
        $data['logo_extra_upload'] = $logoExtra?->logo_name;

        $data['video_titles'] = $profile->videos
            ->where('is_extra', false)
            ->map(fn ($video): array => [
                'title' => (string) ($video->title ?? ''),
                'video_name' => (string) ($video->video_name ?? ''),
            ])
            ->values()
            ->all();

        $data['video_extra_titles'] = $profile->videos
            ->where('is_extra', true)
            ->map(fn ($video): array => [
                'title' => (string) ($video->title ?? ''),
                'video_name' => (string) ($video->video_name ?? ''),
            ])
            ->values()
            ->all();

        // Legacy code/index.php defaults destination_url to "http://".
        if (($profile->equipmentType?->slag === 'code') && blank($data['url'] ?? null)) {
            $data['url'] = 'http://';
        }

        return $data;
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
        $this->syncFormBuilderSidebarSettings();
    }

    /**
     * @return array<\Filament\Actions\Action>
     */
    protected function getFormActions(): array
    {
        $slag = $this->record?->equipmentType?->slag;

        // Survey Save is rendered under Form Builder in legacy-profile-page.
        if ($slag === 'survey') {
            return [];
        }

        if ($slag === 'code') {
            return [
                $this->getSaveFormAction()->label('SAVE'),
            ];
        }

        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}
