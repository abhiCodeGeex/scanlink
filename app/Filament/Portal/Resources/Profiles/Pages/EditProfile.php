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

    /** Legacy edit.php shows a message-only page (no form) for expired codes. */
    public bool $profileIsExpired = false;

    public function getView(): string
    {
        if ($this->profileIsExpired) {
            return 'filament.portal.profiles.expired-profile';
        }

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

        // Legacy parity: expired codes are not editable (message-only page).
        $this->profileIsExpired = $this->record->isExpired();

        if ($this->profileIsExpired) {
            return;
        }

        $this->consumeFormBuilderOrderSuccess($this->record);
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
        $data['logo_extra_url'] = $logoExtra?->logo_url;

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

        // Dirty legacy rows can have form_is_enable=1 without form_active. Never surface
        // Enable as ON until the profile is entitled (purchase or survey/voc free).
        if (! $profile->formBuilderEntitled()) {
            $data['form_is_enable'] = false;

            if ((bool) $profile->form_is_enable) {
                // Persist just the corrected flag with a targeted column update. A full
                // save here is unsafe: the model's `retrieved` hook nulls the sentinel
                // date columns (voc_dob, activation_start_date, activation_end_date) in
                // memory, and saveQuietly() skips the `saving` guard that restores the
                // sentinel — so those NOT NULL columns would be written as NULL and the
                // page would 500 on load.
                $profile->newQuery()->whereKey($profile->getKey())->update(['form_is_enable' => 0]);
                $profile->setAttribute('form_is_enable', false);
                $profile->syncOriginalAttribute('form_is_enable');
            }
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
        if ($this->profileIsExpired) {
            return [];
        }

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

        if ($this->record?->exists) {
            // Re-arm the password gate after save so preview/real URL show login again
            // (same browser session may still hold a prior unlock).
            session()->forget('scan_unlock_'.$this->record->id);

            \App\Support\PortalProfilePreview::clearDraft((int) $this->record->id);
            // Ensure weblink-only saves still remount the iframe.
            $this->record->touch();
            $this->record->refresh();
            $this->refreshPhonePreview();
        }
    }

    /**
     * @return array<\Filament\Actions\Action>
     */
    protected function getFormActions(): array
    {
        if ($this->profileIsExpired) {
            return [];
        }

        $slag = $this->activeEditorTypeSlag();

        // Survey Save is rendered under Form Builder in legacy-profile-page (no Cancel).
        if ($slag === 'survey') {
            return [];
        }

        if ($slag === 'code') {
            return [
                $this->getSaveFormAction()->label('SAVE'),
            ];
        }

        return [
            $this->getSaveFormAction()->label('Save changes'),
        ];
    }
}
