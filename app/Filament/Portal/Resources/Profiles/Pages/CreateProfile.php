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
 * Implemented as EditRecord on an unused paid slot (still open until Save) so Form
 * Builder has profile_id without bouncing the browser to /profiles/{id}/edit.
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
        $requestedSlotId = (int) (request()->query('slot') ?? request()->input('slot') ?? 0);
        $slot = null;

        $drafts = app(ProfileDraftSlotService::class);

        if ($requestedSlotId > 0) {
            $slot = $drafts->claimSpecific((int) $client->id, $requestedSlotId, (string) $typeSlag, $member?->id);
        }

        // A specific slot was requested (from Code Balance): only claimSpecific may satisfy it —
        // never silently substitute a session draft or an arbitrary open slot.
        if (! $slot && $existingId > 0 && $requestedSlotId === 0) {
            $slot = \App\Models\Profile::query()
                ->whereKey($existingId)
                ->where('client_id', $client->id)
                ->where('deleted', false)
                ->first();

            // Reuse session draft only while it is still an unsaved open slot for this type.
            if ($slot && ! $slot->update_or_not) {
                $typeId = (int) EquipmentType::query()->where('slag', $typeSlag)->value('id');
                if ((int) $slot->type_id === $typeId) {
                    $slot = $drafts->scrubIfPollutedCreateDraft($slot);
                } else {
                    $slot = $drafts->claimSpecific((int) $client->id, (int) $slot->id, (string) $typeSlag, $member?->id);
                }
            } elseif ($slot && $slot->update_or_not) {
                // Already saved / activated — do not reuse for create.
                $slot = null;
            }
        }

        if (! $slot && $requestedSlotId === 0) {
            $slot = $drafts->claimForCreate((int) $client->id, (string) $typeSlag, $member?->id);
        }

        if (! $slot) {
            Notification::make()
                ->title($requestedSlotId > 0
                    ? 'That code slot is not available'
                    : 'No unused codes available')
                ->body($requestedSlotId > 0
                    ? null
                    : 'Purchase codes or activate a code from Code Balance first.')
                ->danger()
                ->send();

            $this->redirect(
                $requestedSlotId > 0
                    ? \App\Filament\Portal\Pages\CodeBalance::getUrl(panel: 'portal')
                    : ProfileResource::getUrl('index', panel: 'portal'),
                navigate: false,
            );

            return;
        }

        // Legacy parity: cannot activate/create on an expired code slot.
        if ($slot->isExpired()) {
            Notification::make()
                ->title('You can not perform this action on expired profile.')
                ->danger()
                ->send();

            $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);

            return;
        }

        session([$sessionKey => (int) $slot->getKey()]);

        // Create must open with an empty Form Builder canvas. Reused paid slots often
        // still have form_builder_question rows from a prior profile on the same id.
        app(\App\Services\FormBuilderService::class)->clearFormForCreate($slot);
        $slot = $slot->fresh(['equipmentType', 'client']) ?? $slot;

        parent::mount($slot->getKey());
        $this->bootLegacyCreateEditor();
        $this->formBuilderEmbedNonce++;
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

        // Create form always starts with Form Builder disabled / empty meta.
        $data['form_id'] = 0;
        $data['form_title'] = '';
        $data['form_email_tag'] = '';
        $data['form_is_enable'] = false;
        $data['form_active'] = false;
        $data['enable_form_analytics'] = false;

        // Legacy tiles_order: seed the exhibit/voc "Display Order" control (default order on create).
        $slag = $profile->equipmentType?->slag;
        if (in_array($slag, ['exhibit', 'voc'], true)) {
            $data['tile_order'] = \App\Models\Profile::tileOrderFormItems($slag, $profile->tiles_order);
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

        if (array_key_exists('tile_order', $data)) {
            $ids = \App\Models\Profile::tileOrderToString($data['tile_order']);

            if ($ids !== null) {
                $data['tiles_order'] = $ids;
            }

            unset($data['tile_order']);
        }

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

        if ($this->record?->exists) {
            \App\Support\PortalProfilePreview::clearDraft((int) $this->record->id);
            $this->refreshPhonePreview();
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
