<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyFormBuilderSidebar;
use App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyProfileEditorLayout;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Filament\Resources\Profiles\Pages\Concerns\HasProfileQrActions;
use App\Filament\Resources\Profiles\Pages\Concerns\SyncsProfileAssets;
use App\Models\EquipmentType;
use App\Services\ProfileDraftSlotService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * "Add a New Code" uses /portal/profiles/create?type=…
 * Implemented as EditRecord on a claimed draft so Form Builder has profile_id
 * without bouncing the browser to /profiles/{id}/edit.
 */
class CreateProfile extends EditRecord
{
    use HandlesDatabaseSaveFailures;
    use HasLegacyFormBuilderSidebar;
    use HasLegacyProfileEditorLayout;
    use HasProfileQrActions;
    use InteractsWithClientMembership;
    use SyncsProfileAssets;

    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Add a New Code';

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $typeSlag = request()->query('type')
            ?: $this->record?->equipmentType?->slag;

        if (filled($typeSlag)) {
            $name = EquipmentType::query()->where('slag', $typeSlag)->value('name')
                ?: $this->record?->equipmentType?->name;

            if ($name) {
                $label = $typeSlag === 'code' ? 'URL Link' : $name;

                return 'Add a New '.$label.' Code';
            }
        }

        return 'Add a New Code';
    }

    public function mount(int|string|null $record = null): void
    {
        // Livewire tests may pass an existing draft id; browser /create has none.
        if (filled($record)) {
            parent::mount($record);
            $this->bootLegacyCreateEditor();

            return;
        }

        $client = $this->currentClient();
        $member = $this->currentClientUser();
        $typeSlag = request()->query('type')
            ?? request()->input('type')
            ?? request()->route('type');

        if (! $client) {
            Notification::make()
                ->title('No active client account')
                ->danger()
                ->send();

            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        if (blank($typeSlag)) {
            Notification::make()
                ->title('Choose a code type first')
                ->body('Use Add a New Code from the Master Code List (e.g. Location).')
                ->warning()
                ->send();

            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        $sessionKey = 'portal_create_draft_'.$client->id.'_'.$typeSlag;
        $existingId = (int) session($sessionKey, 0);
        $slot = null;

        if ($existingId > 0) {
            $slot = \App\Models\Profile::query()
                ->whereKey($existingId)
                ->where('client_id', $client->id)
                ->where('deleted', false)
                ->where('update_or_not', false)
                ->first();
        }

        if (! $slot) {
            $slot = app(ProfileDraftSlotService::class)
                ->claimForCreate((int) $client->id, (string) $typeSlag, $member?->id);
        }

        if (! $slot) {
            Notification::make()
                ->title('Could not create profile draft')
                ->danger()
                ->send();

            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        session([$sessionKey => (int) $slot->getKey()]);

        parent::mount($slot->getKey());
        $this->bootLegacyCreateEditor();
    }

    protected function bootLegacyCreateEditor(): void
    {
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

    public function getView(): string
    {
        return 'filament.portal.profiles.legacy-profile-page';
    }

    protected function canDownloadQr(): bool
    {
        return $this->canDownload();
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->profileQrHeaderActions(),
            Action::make('backToList')
                ->label('Return to list')
                ->url(ProfileResource::getUrl('index', panel: 'portal'))
                ->color('gray'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['update_or_not'] = true;

        return $data;
    }

    protected function afterSave(): void
    {
        app(ProfileDraftSlotService::class)->finalize($this->record);
        $this->syncProfileAssets();
        $this->syncFormBuilderSidebarSettings();

        // Free the create-session draft so "Add a New Code" claims a fresh slot.
        $clientId = (int) ($this->record->client_id ?? 0);
        $typeSlag = $this->record->equipmentType?->slag
            ?? request()->query('type');

        if ($clientId > 0 && filled($typeSlag)) {
            session()->forget('portal_create_draft_'.$clientId.'_'.$typeSlag);
        }
    }

    protected function getRedirectUrl(): ?string
    {
        // After first save, open the edit URL (legacy code/index → edit flow).
        return ProfileResource::getUrl('edit', ['record' => $this->record], panel: 'portal');
    }

    /**
     * @return array<\Filament\Actions\Action>
     */
    protected function getFormActions(): array
    {
        // Survey Save is rendered under Form Builder in legacy-profile-page.
        if ($this->isSurveyEditor()) {
            return [];
        }

        $legacySaveOnly = $this->isUrlLinkCodeEditor();

        $actions = [
            $this->getSaveFormAction()->label(
                $legacySaveOnly ? 'SAVE' : 'Save changes'
            ),
        ];

        if (! $legacySaveOnly) {
            $actions[] = $this->getCancelFormAction();
        }

        return $actions;
    }

    private function isUrlLinkCodeEditor(): bool
    {
        $slag = request()->query('type')
            ?: $this->record?->equipmentType?->slag;

        return $slag === 'code';
    }

    private function isSurveyEditor(): bool
    {
        $slag = request()->query('type')
            ?: $this->record?->equipmentType?->slag;

        return $slag === 'survey';
    }
}
