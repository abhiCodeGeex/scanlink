<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\Profile;
use App\Models\VisitorContact;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class VisitorLog extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Visitor Log';

    protected static ?string $title = 'Visitor Log';

    protected static ?string $slug = 'visitor-log';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.portal.pages.visitor-log';

    public ?int $selectedProfileId = null;

    /** @var Collection<int, VisitorContact> */
    public Collection $visitors;

    public static function getNavigationGroup(): ?string
    {
        return 'Codes';
    }

    public function mount(): void
    {
        $this->visitors = collect();
        $firstProfileId = $this->clientProfileOptions()->keys()->first();

        if ($firstProfileId) {
            $this->loadVisitors((int) $firstProfileId);
        }
    }

    public function updatedSelectedProfileId(?int $profileId): void
    {
        if ($profileId) {
            $this->loadVisitors($profileId);
        }
    }

    protected function loadVisitors(int $profileId): void
    {
        $client = $this->requireClient();

        Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;
        $this->visitors = VisitorContact::query()
            ->where('profile_id', $profileId)
            ->orderByDesc('entry_date')
            ->limit(200)
            ->get();
    }

    /**
     * @return Collection<int|string, string>
     */
    public function clientProfileOptions(): Collection
    {
        $client = $this->currentClient();

        if (! $client) {
            return collect();
        }

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
