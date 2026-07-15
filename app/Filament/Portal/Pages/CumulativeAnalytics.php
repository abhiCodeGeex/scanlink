<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\Profile;
use App\Services\AnalyticsApiService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema as DbSchema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CumulativeAnalytics extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static ?string $navigationLabel = 'Cumulative Analytics';

    protected static ?string $title = 'Cumulative Analytics';

    protected static ?string $slug = 'cumulative-analytics';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.portal.pages.cumulative-analytics';

    /** @var array<int, int> */
    public array $selectedProfileIds = [];

    /** @var list<array{profile_id: int, profile_name: string, date?: string, location?: string, device?: string, details?: string}> */
    public array $combinedScanRows = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Codes';
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessAnalytics(static::portalMembership());
    }

    public function mount(): void
    {
        $this->form->fill([
            'selectedProfileIds' => [],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => $this->combinedScanRows !== [])
                ->action(fn (): StreamedResponse => $this->exportCombinedCsv()),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profiles')
                    ->schema([
                        Select::make('selectedProfileIds')
                            ->label('Profiles')
                            ->options(fn (): array => $this->clientProfileOptions()->all())
                            ->multiple()
                            ->live()
                            ->afterStateUpdated(function (?array $state): void {
                                $this->selectedProfileIds = array_map('intval', $state ?? []);
                                $this->loadCombinedAnalytics(app(AnalyticsApiService::class));
                            }),
                    ]),
            ]);
    }

    public function exportCombinedCsv(): StreamedResponse
    {
        $filename = 'cumulative-analytics-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Profile', 'Profile ID', 'Date', 'Location', 'Device', 'Details']);

            foreach ($this->combinedScanRows as $row) {
                fputcsv($handle, [
                    $row['profile_name'] ?? '',
                    $row['profile_id'] ?? '',
                    $row['date'] ?? '',
                    $row['location'] ?? '',
                    $row['device'] ?? '',
                    $row['details'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function loadCombinedAnalytics(AnalyticsApiService $analytics): void
    {
        $this->combinedScanRows = [];

        if ($this->selectedProfileIds === []) {
            return;
        }

        $client = $this->requireClient();

        $profiles = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->whereIn('id', $this->selectedProfileIds)
            ->orderBy('name')
            ->get();

        $hasAnalyticKey = DbSchema::hasColumn('profiles', 'analytic_key');

        foreach ($profiles as $profile) {
            if (! $hasAnalyticKey) {
                continue;
            }

            $key = $profile->getAttribute('analytic_key');

            if (! filled($key)) {
                continue;
            }

            $scanList = $analytics->getScanList((string) $key);

            if (! is_array($scanList)) {
                continue;
            }

            foreach ($this->normalizeScanRows($scanList) as $row) {
                $this->combinedScanRows[] = [
                    'profile_id' => $profile->id,
                    'profile_name' => $profile->name,
                    ...$row,
                ];
            }
        }
    }

    /**
     * @param  array<int, mixed>  $scanList
     * @return list<array{date?: string, location?: string, device?: string, details?: string}>
     */
    protected function normalizeScanRows(array $scanList): array
    {
        $rows = [];

        foreach ($scanList as $key => $item) {
            if (is_array($item)) {
                $rows[] = [
                    'date' => (string) ($item['date'] ?? $item['datetime'] ?? $item['scanned_at'] ?? $key),
                    'location' => (string) ($item['location'] ?? $item['city'] ?? $item['country'] ?? ''),
                    'device' => (string) ($item['device'] ?? $item['platform'] ?? $item['browser'] ?? ''),
                    'details' => json_encode($item),
                ];

                continue;
            }

            $rows[] = [
                'date' => is_string($key) ? $key : '',
                'location' => '',
                'device' => '',
                'details' => is_scalar($item) ? (string) $item : json_encode($item),
            ];
        }

        return $rows;
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
