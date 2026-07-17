<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Pages\CumulativeAnalytics;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\EquipmentType;
use Filament\Actions\Action;
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

    /**
     * Legacy innermenu order (people/customqr hidden, matching live portal).
     *
     * @var list<string>
     */
    protected static array $typeTabSlags = [
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

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => $this->typeTabs(),
            'activeTab' => $this->activeTab,
            'addCodeUrl' => $this->addNewCodeUrl(),
            'canAddCode' => $this->canAddCode(),
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
        return [
            Action::make('multipleCodeAnalytics')
                ->label('Multiple Code Analytics')
                ->color('success')
                ->url(CumulativeAnalytics::getUrl())
                ->openUrlInNewTab(false)
                ->extraAttributes(['class' => 'sl-legacy-btn sl-legacy-btn-analytics']),
            Action::make('renewSelectedCodes')
                ->label('Renew Selected Codes')
                ->color('success')
                ->url(\App\Filament\Portal\Pages\MultipleCodeRenewal::getUrl())
                ->visible(fn (): bool => $this->isPrimaryUser())
                ->extraAttributes(['class' => 'sl-legacy-btn']),
            Action::make('addNewCode')
                ->label('Add a New Code')
                ->color('success')
                ->url(fn (): string => $this->addNewCodeUrl())
                ->visible(fn (): bool => $this->canAddCode())
                ->extraAttributes(['class' => 'sl-legacy-btn sl-legacy-btn-add']),
        ];
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

    protected function addNewCodeUrl(): string
    {
        $url = ProfileResource::getUrl('create');
        $tab = $this->activeTab;

        if (filled($tab) && $tab !== 'all') {
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
        return EquipmentType::query()
            ->whereIn('slag', self::$typeTabSlags)
            ->get()
            ->sortBy(fn (EquipmentType $type): int => array_search($type->slag, self::$typeTabSlags, true) ?: 999)
            ->values();
    }

    protected function typeTabLabel(EquipmentType $type): string
    {
        return match ($type->slag) {
            'code' => 'URL Link',
            default => (string) $type->name,
        };
    }
}
