<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use App\Services\CumulativeAnalyticsBuilder;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CumulativeAnalytics extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static ?string $navigationLabel = 'Cumulative Analytics';

    protected static ?string $title = 'Cumulative Analytics';

    protected static ?string $slug = 'cumulative-analytics';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.portal.pages.cumulative-analytics';

    /** @var array<int, int> */
    public array $selectedProfileIds = [];

    /** charts|locations */
    public string $viewMode = 'charts';

    /** @var list<array{id: int, name: string}> */
    public array $selectedProfiles = [];

    public int $formSubmissionCount = 0;

    public int $scanTotal = 0;

    /** @var list<array{title: string, slices: list<array{label: string, value: int, color: string}>}> */
    public array $formCharts = [];

    /** @var list<array{label: string, value: int}> */
    public array $countryBars = [];

    /** @var list<array{label: string, value: int}> */
    public array $deviceBars = [];

    /** @var list<array{profile_id: int, profile_name: string, date: string, location: string, device: string, scan_type: string}> */
    public array $locationRows = [];

    public int $locationPage = 1;

    public int $locationPerPage = 25;

    /** @var list<string> */
    public array $analyticsStatusNotes = [];

    /** Legacy "EXPORT ANALYTICS" modal fields (Name + Description). */
    public string $exportName = '';

    public string $exportDescription = '';

    public static function getNavigationGroup(): ?string
    {
        return 'Codes';
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessAnalytics(static::portalMembership());
    }

    public function getTitle(): string|Htmlable
    {
        return 'Cumulative Analytics';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => \App\Support\LegacyEquipmentTypeLabels::navTypes(),
            'activeTab' => null,
            'addCodeUrl' => ProfileResource::getUrl('index', panel: 'portal'),
            'canAddCode' => false,
            'hideActionBar' => true,
            'hideLegend' => true,
            'readonlyNav' => true,
        ]);
    }

    public function mount(): void
    {
        $profileIds = request()->query('profiles');

        $ids = [];

        if (is_string($profileIds) && $profileIds !== '') {
            $ids = array_values(array_filter(array_map('intval', explode(',', $profileIds))));
        }

        $this->selectedProfileIds = $ids;

        $this->form->fill([
            'selectedProfileIds' => $ids,
        ]);

        if ($ids !== []) {
            $this->reloadAnalytics();
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profiles')
                    ->schema([
                        Select::make('selectedProfileIds')
                            ->label('Select profiles')
                            ->options(fn (): array => $this->clientProfileOptions()->all())
                            ->multiple()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (?array $state): void {
                                $this->selectedProfileIds = array_map('intval', $state ?? []);
                                $this->reloadAnalytics();
                            }),
                    ]),
            ]);
    }

    public function showCharts(): void
    {
        $this->viewMode = 'charts';
    }

    public function showLocations(): void
    {
        $this->viewMode = 'locations';
        $this->locationPage = 1;
    }

    public function goToLocationPage(int $page): void
    {
        $this->locationPage = max(1, min($page, $this->locationTotalPages()));
    }

    public function previousLocationPage(): void
    {
        $this->goToLocationPage($this->locationPage - 1);
    }

    public function nextLocationPage(): void
    {
        $this->goToLocationPage($this->locationPage + 1);
    }

    public function locationTotalPages(): int
    {
        $total = count($this->locationRows);

        if ($total === 0) {
            return 1;
        }

        return (int) max(1, (int) ceil($total / $this->locationPerPage));
    }

    /**
     * @return list<array{profile_id: int, profile_name: string, date: string, location: string, device: string, scan_type: string}>
     */
    public function paginatedLocationRows(): array
    {
        $offset = ($this->locationPage - 1) * $this->locationPerPage;

        return array_values(array_slice($this->locationRows, $offset, $this->locationPerPage));
    }

    public function locationShowingFrom(): int
    {
        if ($this->locationRows === []) {
            return 0;
        }

        return (($this->locationPage - 1) * $this->locationPerPage) + 1;
    }

    public function locationShowingTo(): int
    {
        return min(count($this->locationRows), $this->locationPage * $this->locationPerPage);
    }

    public function returnToListUrl(): string
    {
        return ProfileResource::getUrl('index', panel: 'portal');
    }

    public function exportAnalytics(): StreamedResponse
    {
        $filename = 'cumulative-analytics-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Section', 'Profile', 'Date', 'Location', 'Device', 'Scan Type', 'Question', 'Option', 'Count']);

            foreach ($this->formCharts as $chart) {
                foreach ($chart['slices'] as $slice) {
                    fputcsv($handle, [
                        'Form Analytics',
                        '',
                        '',
                        '',
                        '',
                        '',
                        $chart['title'],
                        $slice['label'],
                        $slice['value'],
                    ]);
                }
            }

            foreach ($this->locationRows as $row) {
                fputcsv($handle, [
                    'Scan Locations',
                    $row['profile_name'] ?? '',
                    $row['date'] ?? '',
                    $row['location'] ?? '',
                    $row['device'] ?? '',
                    $row['scan_type'] ?? '',
                    '',
                    '',
                    '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Legacy "EXPORT ANALYTICS": a named spreadsheet (Name + Description header, profile
     * list, then per-question option counts & percentages). Produced as an Excel-openable
     * HTML-table .xls (no spreadsheet dependency), matching legacy's .xls output.
     */
    public function exportSpreadsheet(): StreamedResponse
    {
        $name = trim($this->exportName) !== '' ? trim($this->exportName) : 'Cumulative Analytics';
        $description = trim($this->exportDescription);
        $profiles = $this->selectedProfiles;
        $charts = $this->formCharts;

        return response()->streamDownload(function () use ($name, $description, $profiles, $charts): void {
            $out = '<html><head><meta charset="UTF-8"></head><body><table border="1" cellspacing="0" cellpadding="4">';
            $out .= '<tr><td colspan="3" style="font-weight:bold;font-size:14px;">'.e($name).'</td></tr>';

            if ($description !== '') {
                $out .= '<tr><td colspan="3">'.e($description).'</td></tr>';
            }

            $out .= '<tr><td colspan="3">&nbsp;</td></tr>';

            foreach ($profiles as $profile) {
                $out .= '<tr><td colspan="3" style="font-weight:bold;">Profile '.(int) $profile['id'].'. '.e(ucwords((string) $profile['name'])).'</td></tr>';
            }

            $out .= '<tr><td colspan="3">&nbsp;</td></tr>';

            foreach ($charts as $chart) {
                $total = array_sum(array_column($chart['slices'], 'value'));
                $out .= '<tr><td colspan="3" style="font-weight:bold;">'.e((string) $chart['title']).'</td></tr>';
                $out .= '<tr><td style="font-weight:bold;">Option</td><td style="font-weight:bold;">Count</td><td style="font-weight:bold;">Percentage</td></tr>';

                foreach ($chart['slices'] as $slice) {
                    $value = (int) $slice['value'];
                    $pct = $total > 0 ? round($value * 100 / $total, 1).'%' : '0%';
                    $out .= '<tr><td>'.e((string) $slice['label']).'</td><td>'.$value.'</td><td>'.$pct.'</td></tr>';
                }

                $out .= '<tr><td colspan="3">&nbsp;</td></tr>';
            }

            $out .= '</table></body></html>';
            echo $out;
        }, 'cumulative_code_profile_analytics.xls', ['Content-Type' => 'application/vnd.ms-excel']);
    }

    /**
     * Legacy "Download PDF": a printable cumulative report. Charts are rendered as
     * option/count/percentage tables (legacy used server-side pie images; the data is
     * identical). Uses TCPDF (already a dependency).
     */
    public function downloadPdf(): StreamedResponse
    {
        $html = view('filament.portal.pages.cumulative-analytics-pdf', [
            'profiles' => $this->selectedProfiles,
            'charts' => $this->formCharts,
            'formSubmissionCount' => $this->formSubmissionCount,
            'scanTotal' => $this->scanTotal,
            'date' => now()->format('d/m/Y H:i:s'),
        ])->render();

        return response()->streamDownload(function () use ($html): void {
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
            $pdf->SetCreator('ScanLink');
            $pdf->SetTitle('Cumulative Analytics');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(12, 12, 12);
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');
            echo $pdf->Output('cumulative-analytics.pdf', 'S');
        }, 'cumulative-analytics.pdf', ['Content-Type' => 'application/pdf']);
    }

    protected function reloadAnalytics(): void
    {
        $this->selectedProfiles = [];
        $this->formSubmissionCount = 0;
        $this->scanTotal = 0;
        $this->formCharts = [];
        $this->countryBars = [];
        $this->deviceBars = [];
        $this->locationRows = [];
        $this->locationPage = 1;
        $this->analyticsStatusNotes = [];

        if ($this->selectedProfileIds === []) {
            return;
        }

        // Legacy getCumulativeProfile requires more than one profile.
        if (count($this->selectedProfileIds) < 2) {
            $this->analyticsStatusNotes[] = 'Please select more than one profile.';

            return;
        }

        $client = $this->requireClient();

        $profiles = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->whereIn('id', $this->selectedProfileIds)
            ->orderBy('name')
            ->get();

        if ($profiles->isEmpty()) {
            $this->analyticsStatusNotes[] = 'No matching profiles found for this account.';

            return;
        }

        // Legacy: expired profiles cannot be used for cumulative analytics.
        if ($profiles->contains(fn (Profile $p): bool => $p->isExpired())) {
            $this->analyticsStatusNotes[] = "Please don't select expired profile.";

            return;
        }

        // Legacy: all selected profiles must share an identical form structure.
        $compatibility = app(CumulativeAnalyticsBuilder::class)->validateCompatibility($profiles);

        if (! $compatibility['ok']) {
            $this->analyticsStatusNotes[] = (string) $compatibility['message'];

            return;
        }

        $built = app(CumulativeAnalyticsBuilder::class)->build($profiles);

        $this->selectedProfiles = $built['profiles'];
        $this->formSubmissionCount = $built['form_submission_count'];
        $this->scanTotal = $built['scan_total'];
        $this->formCharts = $built['form_charts'];
        $this->countryBars = $built['country_bars'];
        $this->deviceBars = $built['device_bars'];
        $this->locationRows = $built['location_rows'];

        if ($this->scanTotal === 0 && $this->formSubmissionCount === 0 && $this->formCharts === []) {
            $this->analyticsStatusNotes[] = 'No scan or form analytics data found for the selected profiles.';
        }
    }

    /**
     * @param  list<array{label: string, value: int}>  $bars
     */
    public function maxBarValue(array $bars): int
    {
        $max = 0;

        foreach ($bars as $bar) {
            $max = max($max, (int) ($bar['value'] ?? 0));
        }

        return max($max, 1);
    }
}
