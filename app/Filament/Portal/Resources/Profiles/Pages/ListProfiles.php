<?php

namespace App\Filament\Portal\Resources\Profiles\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Pages\CumulativeAnalytics;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\EquipmentType;
use App\Models\Profile;
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

    /** @var 'expired'|'expiring'|'active'|null */
    public ?string $expiryStatusFilter = null;

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

    public function setExpiryStatusFilter(?string $status): void
    {
        $allowed = ['expired', 'expiring', 'active'];

        // Clicking the active legend item again clears the filter.
        if ($status === $this->expiryStatusFilter || ! in_array($status, $allowed, true)) {
            $this->expiryStatusFilter = null;
        } else {
            $this->expiryStatusFilter = $status;
        }

        $this->resetPage();
    }

    public function clearExpiryStatusFilter(): void
    {
        $this->expiryStatusFilter = null;
        $this->resetPage();
    }

    protected function modifyQueryWithActiveTab(Builder $query, bool $isResolvingRecord = false): Builder
    {
        $query = parent::modifyQueryWithActiveTab($query, $isResolvingRecord);

        if (filled($this->expiryStatusFilter)) {
            $query->whereExpiryStatus((string) $this->expiryStatusFilter);
        }

        return $query;
    }

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => $this->typeTabs(),
            'activeTab' => $this->activeTab,
            'addCodeUrl' => $this->addNewCodeUrl(),
            'canAddCode' => $this->canAddCode() && $this->hasSelectedTemplateTab(),
            'canRenewCodes' => $this->isPrimaryUser(),
            'hasProfiles' => $this->getAllTableRecordsCount() > 0,
            'bindToolbarActions' => true,
            // The working "Multiple Code Analytics" / "Renew Selected Codes" live in the
            // Filament table bulk-action bar; hide the duplicate (non-working) toolbar copies.
            'hideBulkActions' => true,
            'canFilterByExpiry' => true,
            'expiryStatusFilter' => $this->expiryStatusFilter,
        ]);
    }

    /**
     * Legacy parity: Renew Selected Codes → order summary (invoice), then confirm.
     */
    public function toolbarRenewSelectedCodes(): void
    {
        $records = $this->getSelectedTableRecords(shouldFetchSelectedRecords: true);

        if ($records->isEmpty()) {
            $this->dispatch('sl-toolbar-alert', message: 'Please select the code to be renew.');

            return;
        }

        $renewable = $records->filter(fn (Profile $profile): bool => ! (bool) $profile->free_code);

        if ($renewable->isEmpty()) {
            $this->dispatch('sl-toolbar-alert', message: 'Please select the code to be renew.');

            return;
        }

        $this->redirect(
            \App\Filament\Portal\Pages\RenewCodeSummary::stageRenew(
                $renewable,
                ProfileResource::getUrl('index', panel: 'portal'),
            ),
            navigate: false,
        );
    }

    /**
     * Legacy parity: Multiple Code Analytics requires a non-empty, non-expired selection.
     */
    public function toolbarMultipleCodeAnalytics(): void
    {
        $records = $this->getSelectedTableRecords(shouldFetchSelectedRecords: true);

        if ($records->isEmpty()) {
            $this->dispatch('sl-toolbar-alert', message: 'No code profiles have been selected');

            return;
        }

        $hasExpired = $records->contains(
            fn (Profile $profile): bool => $profile->expiryStatusClass() === 'sl-row-expired'
        );

        if ($hasExpired) {
            $this->dispatch('sl-toolbar-alert', message: "Please don't select expired profile");

            return;
        }

        $this->redirect(
            CumulativeAnalytics::getUrl(['profiles' => $records->pluck('id')->implode(',')]),
            navigate: false,
        );
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
