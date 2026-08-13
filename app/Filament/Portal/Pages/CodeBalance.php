<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use App\Support\LegacyEquipmentTypeLabels;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Legacy /profile/codeBalance — unused code slots with renew + activate-by-template.
 */
class CodeBalance extends Page
{
    use InteractsWithClientMembership;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = 'Code Balance';

    protected static ?string $title = 'Code Balance';

    protected static ?string $slug = 'code-balance';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.portal.pages.code-balance';

    public string $searchCodeNo = '';

    public string $appliedSearch = '';

    public int $page = 1;

    public int $perPage = 10;

    /** @var list<int|string> */
    public array $selected = [];

    public string $alertMessage = '';

    public bool $alertOpen = false;

    public function getHeading(): string|Htmlable|null
    {
        return '';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'My Account';
    }

    public function mount(): void
    {
        $this->page = max(1, (int) request()->query('page', 1));
    }

    public function availabilityBalance(): int
    {
        return $this->slotQuery()->count();
    }

    /**
     * @return Collection<int, \App\Models\EquipmentType>
     */
    public function templateTypes(): Collection
    {
        return LegacyEquipmentTypeLabels::navTypes();
    }

    /**
     * @return Collection<int, Profile>
     */
    public function paginatedSlots(): Collection
    {
        return $this->slotQuery()
            ->forPage($this->page, $this->perPage)
            ->get();
    }

    public function totalPages(): int
    {
        return max(1, (int) ceil($this->availabilityBalance() / $this->perPage));
    }

    public function search(): void
    {
        $this->appliedSearch = trim($this->searchCodeNo);
        $this->page = 1;
        $this->selected = [];
    }

    public function clearSearch(): void
    {
        $this->searchCodeNo = '';
        $this->appliedSearch = '';
        $this->page = 1;
        $this->selected = [];
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, min($page, $this->totalPages()));
    }

    public function nextPage(): void
    {
        $this->goToPage($this->page + 1);
    }

    public function previousPage(): void
    {
        $this->goToPage($this->page - 1);
    }

    public function renewSelected(): void
    {
        $ids = collect($this->selected)
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->showAlert('Please select the code to be renew.');

            return;
        }

        $client = $this->requireClient();

        $records = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->openSlot()
            ->whereIn('id', $ids->all())
            ->get();

        if ($records->isEmpty()) {
            $this->showAlert('Please select the code to be renew.');

            return;
        }

        // Legacy: renewSelected → code/renewCode summary (invoice), not instant extend.
        $url = RenewCodeSummary::stageRenew(
            $records,
            CodeBalance::getUrl(panel: 'portal'),
        );

        $this->selected = [];
        $this->redirect($url, navigate: false);
    }

    public function exitPage(): void
    {
        $this->redirect(ProfileResource::getUrl('index', panel: 'portal'), navigate: false);
    }

    public function activateUrl(int $profileId, string $typeSlag): string
    {
        return ProfileResource::getUrl('create', panel: 'portal')
            .'?type='.urlencode($typeSlag)
            .'&slot='.$profileId;
    }

    public function showAlert(string $message): void
    {
        $this->alertMessage = $message;
        $this->alertOpen = true;
    }

    public function closeAlert(): void
    {
        $this->alertOpen = false;
        $this->alertMessage = '';
    }

    protected function slotQuery(): Builder
    {
        $client = $this->currentClient();

        $query = Profile::query()
            ->where('client_id', $client?->id ?? 0)
            ->active()
            ->openSlot()
            ->orderBy('id');

        if ($this->appliedSearch !== '') {
            $query->where('id', 'like', $this->appliedSearch.'%');
        }

        return $query;
    }
}
