<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\Profile;
use App\Services\AnalyticsApiService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema as DbSchema;

class ScanAnalytics extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Scan Analytics';

    protected static ?string $title = 'Scan Analytics';

    protected static ?string $slug = 'scan-analytics';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.portal.pages.scan-analytics';

    public ?int $selectedProfileId = null;

    /** @var array<string, mixed>|null */
    public ?array $chartData = null;

    /** @var array<string, mixed>|null */
    public ?array $mapData = null;

    /** @var array<int, mixed>|null */
    public ?array $scanList = null;

    public static function getNavigationGroup(): ?string
    {
        return 'Codes';
    }

    public function mount(AnalyticsApiService $analytics): void
    {
        $firstProfileId = $this->clientProfileOptions()->keys()->first();

        if ($firstProfileId) {
            $this->loadAnalytics((int) $firstProfileId, $analytics);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->schema([
                        Select::make('selectedProfileId')
                            ->label('Profile')
                            ->options(fn (): array => $this->clientProfileOptions()->all())
                            ->live()
                            ->afterStateUpdated(function (?string $state) use ($schema): void {
                                if ($state) {
                                    $this->loadAnalytics((int) $state, app(AnalyticsApiService::class));
                                }
                            }),
                    ]),
            ]);
    }

    protected function loadAnalytics(int $profileId, AnalyticsApiService $analytics): void
    {
        $client = $this->requireClient();

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;
        $this->chartData = null;
        $this->mapData = null;
        $this->scanList = null;

        $key = DbSchema::hasColumn('profiles', 'analytic_key')
            ? ($profile->getAttribute('analytic_key') ?: null)
            : null;

        if (! filled($key)) {
            return;
        }

        $this->chartData = $analytics->getChartData((string) $key);
        $this->mapData = $analytics->getMapData((string) $key);
        $this->scanList = $analytics->getScanList((string) $key);
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
