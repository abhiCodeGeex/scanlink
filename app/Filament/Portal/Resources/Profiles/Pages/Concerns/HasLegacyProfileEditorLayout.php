<?php

namespace App\Filament\Portal\Resources\Profiles\Pages\Concerns;

use App\Filament\Portal\Pages\FormBuilder;
use App\Filament\Portal\Pages\FormLibrary;
use App\Filament\Portal\Pages\ManageParticipants;
use App\Filament\Portal\Pages\OrderLabel;
use App\Filament\Portal\Pages\PurchaseFormBuilder;
use App\Filament\Portal\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Services\ProfileQrService;
use App\Support\LegacyEquipmentTypeLabels;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Collection;

trait HasLegacyProfileEditorLayout
{
    public string $qrDownloadFormat = '';

    /** Bumps phone-preview iframe cache key after save / live weblink sync. */
    public int $previewRefreshKey = 0;

    public bool $showFormBuilderOrderSuccess = false;

    public function closeFormBuilderOrderSuccess(): void
    {
        $this->showFormBuilderOrderSuccess = false;
    }

    public function startFormBuilderPurchase(): void
    {
        $profile = $this->legacyEditorRecord();

        if (! $profile?->exists || ! method_exists($this, 'isPrimaryUser') || ! $this->isPrimaryUser()) {
            \Filament\Notifications\Notification::make()
                ->title('Only the primary account user can purchase Form Builder activation.')
                ->warning()
                ->send();
            return;
        }

        if ((bool) $profile->form_active) {
            \Filament\Notifications\Notification::make()
                ->title('Form Builder is already activated for this profile.')
                ->warning()
                ->send();
            return;
        }

        // Legacy auto_save_* persisted the profile before entering checkout so no
        // edits were lost when the user left the editor.
        if (method_exists($this, 'save')) {
            $this->save(shouldRedirect: false);
        }

        $profile->refresh();

        $this->redirect(
            PurchaseFormBuilder::getUrl(['profile' => $profile->id], panel: 'portal'),
            navigate: false,
        );
    }

    protected function consumeFormBuilderOrderSuccess(Profile $profile): void
    {
        if (! (bool) $profile->pop_up_formbuilder) {
            return;
        }

        $this->showFormBuilderOrderSuccess = true;
        $profile->forceFill(['pop_up_formbuilder' => false])->save();
    }

    public function getView(): string
    {
        return 'filament.portal.profiles.legacy-profile-page';
    }

    public function getHeader(): ?ViewContract
    {
        $activeTab = $this->activeEditorTypeSlag();

        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => $this->editorTypeTabs(),
            'activeTab' => $activeTab,
            'addCodeUrl' => ProfileResource::getUrl('create').(filled($activeTab) ? '?type='.urlencode($activeTab) : ''),
            'canAddCode' => false,
            'hideActionBar' => true,
            'editorMode' => true,
            'hideLegend' => true,
        ]);
    }

    /**
     * Form only — the blade wraps left/right columns.
     */
    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function legacyPreviewData(): array
    {
        $record = $this->legacyEditorRecord();
        $previewUrl = null;
        $qrUrl = null;
        $qrImageUrl = null;
        $formBuilderUrl = null;
        $formBuilderEmbedUrl = null;
        $formBuilderPurchaseUrl = null;
        $orderLabelUrl = null;
        $participantsUrl = null;
        $formLibraryUrl = null;

        if ($record instanceof Profile && $record->exists) {
            $record->loadMissing(['client', 'qrImage', 'equipmentType']);
            $previewUrl = \App\Support\PortalProfilePreview::previewUrl($record, $this->previewRefreshKey);
            $qrUrl = app(ProfileQrService::class)->profileUrl($record);
            $formBuilderUrl = FormBuilder::getUrl(panel: 'portal').'?profile='.$record->id;
            $analytics = (int) ($record->enable_form_analytics ? 1 : 0);
            $embedNonce = property_exists($this, 'formBuilderEmbedNonce')
                ? (int) $this->formBuilderEmbedNonce
                : 0;
            $formBuilderEmbedUrl = url('/portal/legacy-form-builder')
                .'?profile_id='.$record->id
                .'&enable_form_analytics='.$analytics
                .'&_v=fluid-3'
                .'&_r='.$embedNonce;
            $formBuilderPurchaseUrl = PurchaseFormBuilder::getUrl(
                ['profile' => $record->id],
                panel: 'portal',
            );
            $orderLabelUrl = OrderLabel::getUrl(panel: 'portal').'?profile='.$record->id;
            $participantsUrl = ManageParticipants::getUrl(panel: 'portal').'?profile='.$record->id.'&embed=1';
            $formLibraryUrl = FormLibrary::getUrl(panel: 'portal');
        }

        if ($record instanceof Profile) {
            $record->loadMissing('equipmentType');
        }

        $typeSlag = $record?->equipmentType?->slag;
        $isUrlLinkCode = $typeSlag === 'code';
        $isSurvey = $typeSlag === 'survey';
        $isExhibit = $typeSlag === 'exhibit';
        $isVoc = $typeSlag === 'voc';
        $isMisc = $typeSlag === 'misc';
        $isAsset = $typeSlag === 'asset';
        $isCreateEditor = $this instanceof CreateProfile;
        // Legacy code/survey/exhibit/voc create pages leave preview QR blank until edit.
        $showPreviewQr = ! (($isUrlLinkCode || $isSurvey || $isExhibit || $isVoc) && $isCreateEditor);
        $showCodePreviewImage = $showPreviewQr;

        // Legacy parity: the create screens (location/index.php etc.) render an EMPTY
        // iphone-preview — no profile is previewed until the code is saved. Only the edit
        // screens show the live mobile preview. (Previously only survey/exhibit/voc create
        // were blanked, so location/plant/… create leaked the unsaved slot profile.)
        if ($isCreateEditor) {
            $previewUrl = null;
        }

        if (
            $showPreviewQr
            && $record instanceof Profile
            && $record->exists
        ) {
            if (! $record->qrImage) {
                try {
                    app(ProfileQrService::class)->generateFor($record);
                    $record->load('qrImage');
                } catch (\Throwable) {
                    // best effort — sidebar still shows URL block
                }
            }

            $qrImageUrl = $record->qrImage?->publicUrl();
        }

        return [
            'record' => $record,
            'previewUrl' => $previewUrl,
            'qrUrl' => $qrUrl,
            'qrImageUrl' => $qrImageUrl,
            'formBuilderUrl' => $formBuilderUrl,
            'formBuilderEmbedUrl' => $formBuilderEmbedUrl,
            'formBuilderPurchaseUrl' => $formBuilderPurchaseUrl,
            // Legacy: survey / voc get Form Builder free (no $5 gate); others need form_active.
            'formBuilderPurchased' => (bool) ($record?->formBuilderEntitled() ?? false),
            'canPurchaseFormBuilder' => method_exists($this, 'isPrimaryUser')
                ? $this->isPrimaryUser()
                : false,
            'orderLabelUrl' => $orderLabelUrl,
            'participantsUrl' => $participantsUrl,
            'formLibraryUrl' => $formLibraryUrl,
            'isUrlLinkCode' => $isUrlLinkCode,
            'isSurvey' => $isSurvey,
            'isExhibit' => $isExhibit,
            'isVoc' => $isVoc,
            'isMisc' => $isMisc,
            'isAsset' => $isAsset,
            'showCodePreviewImage' => $showCodePreviewImage,
            'isCreateEditor' => $isCreateEditor,
            'canAccessFormBuilder' => method_exists($this, 'canAccessFormBuilder')
                ? $this->canAccessFormBuilder()
                : static::memberCanAccessFormBuilder(static::portalMembership()),
            'canDownloadQr' => method_exists($this, 'canDownloadQr')
                ? $this->canDownloadQr()
                : true,
            'previewRefreshKey' => $this->previewRefreshKey,
        ];
    }

    /**
     * Force the iPhone iframe to reload (after Save or live draft push).
     */
    public function refreshPhonePreview(): void
    {
        $this->previewRefreshKey++;

        $record = $this->legacyEditorRecord();

        if ($record instanceof Profile && $record->exists) {
            $record->refresh();
        }

        $this->dispatch('refresh-phone-preview');
    }

    /**
     * Push current Filament form state into a session draft and reload the phone preview.
     * Does not persist to the database — Save still owns writes.
     * Calling this as a Livewire action also commits deferred wire:model values.
     */
    public function pushPhonePreviewDraft(): void
    {
        $record = $this->legacyEditorRecord();

        if (! $record instanceof Profile || ! $record->exists) {
            return;
        }

        $formData = property_exists($this, 'data') && is_array($this->data)
            ? $this->data
            : [];

        \App\Support\PortalProfilePreview::storeDraft((int) $record->id, $formData);
        $this->refreshPhonePreview();
    }

    /**
     * Any Livewire update under form $data (including nested weblinks) refreshes the phone preview.
     */
    public function updated(string $name, mixed $value = null): void
    {
        if (! str_starts_with($name, 'data.')) {
            return;
        }

        if (preg_match('/(?:logo|picture|document|video|upload|file|qr)/i', $name) === 1) {
            return;
        }

        // Nested Livewire "updated*" hooks already ran; debounce the iframe reload.
        $this->js(<<<'JS'
            clearTimeout(window.__slPhonePreviewTimer);
            window.__slPhonePreviewTimer = setTimeout(() => {
                $wire.pushPhonePreviewDraft();
            }, 350);
        JS);
    }

    public function downloadQrCode(): mixed
    {
        $record = $this->legacyEditorRecord();

        if (! $record instanceof Profile || ! $record->exists) {
            return null;
        }

        if (method_exists($this, 'canDownloadQr') && ! $this->canDownloadQr()) {
            \Filament\Notifications\Notification::make()
                ->title('You do not have permission to download codes')
                ->danger()
                ->send();

            return null;
        }

        $format = strtolower(trim($this->qrDownloadFormat));

        if ($format === '') {
            \Filament\Notifications\Notification::make()
                ->title('Please select a download format')
                ->warning()
                ->send();

            return null;
        }

        try {
            return app(ProfileQrService::class)->downloadAs($record, $format);
        } catch (\App\Exceptions\UnsupportedDownloadFormatException $e) {
            // e.g. TIFF/EPS without ImageMagick — tell the user which formats work.
            \Filament\Notifications\Notification::make()
                ->title($e->getMessage())
                ->warning()
                ->send();

            return null;
        } catch (\Throwable $e) {
            report($e);

            \Filament\Notifications\Notification::make()
                ->title('Could not download code')
                ->body(config('app.debug') ? $e->getMessage() : 'Please try again.')
                ->danger()
                ->send();

            return null;
        }
    }

    protected function legacyEditorRecord(): ?Profile
    {
        if (property_exists($this, 'record') && $this->record instanceof Profile) {
            return $this->record;
        }

        return null;
    }

    protected function activeEditorTypeSlag(): ?string
    {
        $record = $this->legacyEditorRecord();

        if ($record?->relationLoaded('equipmentType') && $record->equipmentType?->slag) {
            return $record->equipmentType->slag;
        }

        if ($record?->type_id) {
            return EquipmentType::query()->whereKey($record->type_id)->value('slag');
        }

        $type = request()->query('type');

        return filled($type) ? (string) $type : null;
    }

    /**
     * @return Collection<int, EquipmentType>
     */
    protected function editorTypeTabs(): Collection
    {
        return LegacyEquipmentTypeLabels::navTypes();
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
