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

    public static function canAccess(): bool
    {
        return static::memberCanAccessAnalytics(static::portalMembership());
    }

    public function mount(AnalyticsApiService $analytics): void
    {
        $requestedProfile = request()->integer('profile');
        $firstProfileId = $requestedProfile ?: $this->clientProfileOptions()->keys()->first();

        if ($firstProfileId) {
            $this->loadAnalytics((int) $firstProfileId, $analytics);
            $this->form->fill(['selectedProfileId' => $firstProfileId]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => filled($this->scanList))
                ->action(fn (): StreamedResponse => $this->exportScanListCsv()),
        ];
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
                            ->afterStateUpdated(function (?string $state): void {
                                if ($state) {
                                    $this->loadAnalytics((int) $state, app(AnalyticsApiService::class));
                                }
                            }),
                    ]),
            ]);
    }

    public function exportScanListCsv(): StreamedResponse
    {
        $rows = $this->normalizedScanRows();
        $filename = 'scan-analytics-profile-'.($this->selectedProfileId ?? 'export').'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['#', 'Date', 'Location', 'Device', 'Details']);

            foreach ($rows as $index => $row) {
                fputcsv($handle, [
                    $index + 1,
                    $row['date'] ?? '',
                    $row['location'] ?? '',
                    $row['device'] ?? '',
                    $row['details'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * @return list<array{date?: string, location?: string, device?: string, details?: string}>
     */
    public function normalizedScanRows(): array
    {
        if (! is_array($this->scanList)) {
            return [];
        }

        $rows = [];

        foreach ($this->scanList as $key => $item) {
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

    public function summaryCounts(): array
    {
        $chart = is_array($this->chartData) ? $this->chartData : [];
        $map = is_array($this->mapData) ? $this->mapData : [];
        $scans = $this->normalizedScanRows();

        return [
            'total_scans' => count($scans) ?: (int) ($chart['total'] ?? $chart['count'] ?? 0),
            'chart_keys' => count($chart),
            'map_points' => is_array($map['points'] ?? null) ? count($map['points']) : count($map),
        ];
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
