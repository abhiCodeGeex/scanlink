<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\EquipmentType;
use App\Support\LegacyEquipmentTypeLabels;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class ListProfiles extends ListRecords
{
    use InteractsWithClientMembership;

    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Master Code List';

    public function mount(): void
    {
        parent::mount();

        $tab = request()->query('tab');

        if (! filled($tab)) {
            return;
        }

        $tab = (string) $tab;

        if (array_key_exists($tab, $this->getTabs())) {
            $this->activeTab = $tab;
        }
    }

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => $this->typeTabs(),
            'activeTab' => $this->activeTab,
            'addCodeUrl' => $this->addNewCodeUrl(),
            'canAddCode' => $this->canAddCode() && $this->hasSelectedTemplateTab(),
            'canRenewCodes' => $this->isPrimaryUser(),
        ]);
    }

    public function getHeading(): string|Htmlable
    {
        $label = $this->activeTabHeading();

        return new HtmlString(
            '<a href="#" class="sl-mastercode-heading" wire:click.prevent="$set(\'activeTab\', \'all\')">'.e($label).'</a>'
        );
    }

    protected function getHeaderActions(): array
    {
        // CTAs live in mastercode-toolbar next to the status legend.
        return [];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        foreach ($this->typeTabs() as $type) {
            $tabs[$type->slag] = Tab::make($this->typeTabLabel($type))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('type_id', $type->id));
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }

    protected function hasSelectedTemplateTab(): bool
    {
        $tab = $this->activeTab;

        return filled($tab) && $tab !== 'all';
    }

    protected function addNewCodeUrl(): string
    {
        $url = ProfileResource::getUrl('create');
        $tab = $this->activeTab;

        if ($this->hasSelectedTemplateTab()) {
            $url .= '?type='.urlencode((string) $tab);
        }

        return $url;
    }

    protected function activeTabHeading(): string
    {
        if (blank($this->activeTab) || $this->activeTab === 'all') {
            return 'Master Code List';
        }

        $type = $this->typeTabs()->firstWhere('slag', $this->activeTab);

        return $type ? $this->typeTabLabel($type).' Codes' : 'Master Code List';
    }

    /**
     * @return Collection<int, EquipmentType>
     */
    protected function typeTabs(): Collection
    {
        return LegacyEquipmentTypeLabels::navTypes();
    }

    protected function typeTabLabel(EquipmentType $type): string
    {
        return LegacyEquipmentTypeLabels::labelFor($type);
    }
}
