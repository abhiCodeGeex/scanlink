<?php

namespace App\Filament\Portal\Resources\Profiles\Pages\Concerns;

use App\Filament\Portal\Pages\FormBuilder;
use App\Filament\Portal\Pages\OrderLabel;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Services\ProfileQrService;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Collection;

trait HasLegacyProfileEditorLayout
{
    public function getView(): string
    {
        return 'filament.portal.profiles.legacy-profile-page';
    }

    /**
     * @var list<string>
     */
    protected static array $editorTypeTabSlags = [
        'plant',
        'location',
        'asset',
        'product',
        'procedure',
        'misc',
        'code',
        'survey',
        'exhibit',
        'voc',
    ];

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
        $orderLabelUrl = null;

        if ($record instanceof Profile && $record->exists) {
            $record->loadMissing(['client', 'qrImage', 'equipmentType']);
            $previewUrl = \App\Support\PortalProfilePreview::previewUrl($record);
            $qrUrl = app(ProfileQrService::class)->profileUrl($record);
            $formBuilderUrl = FormBuilder::getUrl().'?profile='.$record->id;
            $orderLabelUrl = OrderLabel::getUrl().'?profile='.$record->id;

            if (! $record->qrImage && $record->exists) {
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
            'orderLabelUrl' => $orderLabelUrl,
            'canAccessFormBuilder' => method_exists($this, 'canAccessFormBuilder')
                ? $this->canAccessFormBuilder()
                : static::memberCanAccessFormBuilder(static::portalMembership()),
        ];
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
        return EquipmentType::query()
            ->whereIn('slag', self::$editorTypeTabSlags)
            ->get()
            ->sortBy(fn (EquipmentType $type): int => array_search($type->slag, self::$editorTypeTabSlags, true) ?: 999)
            ->values();
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
