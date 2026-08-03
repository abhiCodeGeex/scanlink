<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\AnaItemAnalytics;
use App\Models\FormBuilderAnswer;
use App\Models\Profile;
use App\Support\LegacyEquipmentTypeLabels;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScanAnalytics extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Scanalytics';

    protected static ?string $title = 'Scanalytics';

    protected static ?string $slug = 'scan-analytics';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.portal.pages.scan-analytics';

    public ?int $selectedProfileId = null;

    public ?string $profileName = null;

    public ?string $typeName = null;

    /** charts|map|locations */
    public string $viewMode = 'charts';

    public int $analyticsCount = 0;

    public int $formSubmissionCount = 0;

    public bool $formAnalyticsEnabled = false;

    /** @var list<array{title: string, slices: list<array{label: string, value: int, color: string}>}> */
    public array $formCharts = [];

    /** city_name|created_at */
    public string $locationSort = 'created_at';

    public ?int $focusRowId = null;

    /** @var Collection<int, object> */
    public Collection $locationRows;

    /** @var list<array{id: int, lat: float, lng: float, scan_type: string, label: string}> */
    public array $mapPoints = [];

    public ?string $loadError = null;

    public static function getNavigationGroup(): ?string
    {
        return 'Codes';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Scanalytics';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => LegacyEquipmentTypeLabels::navTypes(),
            'activeTab' => null,
            'addCodeUrl' => ProfileResource::getUrl('index'),
            'canAddCode' => false,
            'hideActionBar' => true,
            'hideLegend' => true,
            'readonlyNav' => true,
        ]);
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessAnalytics(static::portalMembership());
    }

    public function mount(): void
    {
        $this->locationRows = collect();
        $this->viewMode = match ((string) request()->query('view', 'charts')) {
            'map', 'locations' => (string) request()->query('view'),
            default => 'charts',
        };

        $sort = (string) request()->query('sort', 'created_at');
        $this->locationSort = in_array($sort, ['city_name', 'created_at'], true) ? $sort : 'created_at';
        $this->focusRowId = request()->integer('row') ?: null;

        $requestedProfile = request()->integer('profile');

        if ($requestedProfile > 0) {
            $this->loadProfileAnalytics($requestedProfile);
        }

        if ($this->viewMode === 'map') {
            $this->dispatchMapBoot();
        }
    }

    public function showCharts(): void
    {
        $this->viewMode = 'charts';
        $this->focusRowId = null;
    }

    public function showMap(?int $rowId = null): void
    {
        $this->viewMode = 'map';
        $this->focusRowId = $rowId;
        $this->rebuildMapPoints();
        $this->dispatchMapBoot();
    }

    public function showLocations(): void
    {
        $this->viewMode = 'locations';
        $this->focusRowId = null;
        $this->reloadLocationRows();
    }

    public function setLocationSort(string $sort): void
    {
        $this->locationSort = in_array($sort, ['city_name', 'created_at'], true) ? $sort : 'created_at';
        $this->reloadLocationRows();
    }

    public function returnToListUrl(): string
    {
        return ProfileResource::getUrl('index');
    }

    public function countryChartUrl(): string
    {
        return route('portal.graphengine.country', ['pid' => $this->selectedProfileId]);
    }

    public function deviceChartUrl(): string
    {
        return route('portal.graphengine.device', ['pid' => $this->selectedProfileId]);
    }

    public function browserChartUrl(): string
    {
        return route('portal.graphengine.browser', ['pid' => $this->selectedProfileId]);
    }

    /**
     * @return array{COLUMNS: list<string>, DATA: list<mixed>}
     */
    public function countryChartData(): array
    {
        return app(\App\Services\ScanalyticsGraphEngine::class)->country((int) $this->selectedProfileId);
    }

    /**
     * @return array{COLUMNS: list<string>, DATA: list<mixed>}
     */
    public function deviceChartData(): array
    {
        return app(\App\Services\ScanalyticsGraphEngine::class)->device((int) $this->selectedProfileId);
    }

    /**
     * @return array{COLUMNS: list<string>, DATA: list<mixed>}
     */
    public function browserChartData(): array
    {
        return app(\App\Services\ScanalyticsGraphEngine::class)->browser((int) $this->selectedProfileId);
    }

    public function exportAnalytics(): StreamedResponse
    {
        $profileId = (int) $this->selectedProfileId;
        $filename = 'excelreport-'.$profileId.'.csv';
        $rows = $this->analyticsQuery($profileId)->orderByDesc('id')->get();
        $platforms = $this->platformNames();
        $devices = $this->deviceNames();

        return response()->streamDownload(function () use ($rows, $platforms, $devices): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                '#', 'Date Time', 'IP', 'Location', 'Latitude', 'Longitude',
                'Device', 'Platform', 'Screen Size', 'Browser', 'Browser Version', 'Scan Type',
            ]);

            foreach ($rows as $index => $row) {
                fputcsv($handle, [
                    $index + 1,
                    $row->created_at?->format('Y-m-d H:i:s') ?? '',
                    (string) $row->ip_add,
                    trim(implode(', ', array_filter([
                        (string) $row->city_name,
                        (string) $row->region_name,
                        (string) $row->country_name,
                    ]))),
                    (string) $row->latitude,
                    (string) $row->longitude,
                    $this->resolvePlatformLabel($row->id_platform, $platforms),
                    $this->resolveDeviceLabel($row->id_device, $devices),
                    (string) ($row->screen_size ?? ''),
                    $this->browserLabel((string) $row->id_browser),
                    (string) ($row->browser_version ?? ''),
                    strtolower((string) ($row->scan_type ?? '')) === 'gps' ? 'GPS' : 'IP',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Legacy exported scan analytics as .xls — produce a real .xlsx of the scan rows.
     */
    public function exportXlsx(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $profileId = (int) $this->selectedProfileId;
        $rows = $this->analyticsQuery($profileId)->orderByDesc('id')->get();
        $platforms = $this->platformNames();
        $devices = $this->deviceNames();

        $tmp = tempnam(sys_get_temp_dir(), 'sanl_').'.xlsx';
        $writer = new \OpenSpout\Writer\XLSX\Writer;
        $writer->openToFile($tmp);
        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
            '#', 'Date Time', 'IP', 'Location', 'Latitude', 'Longitude',
            'Device', 'Platform', 'Screen Size', 'Browser', 'Browser Version', 'Scan Type',
        ]));

        foreach ($rows as $index => $row) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                (string) ($index + 1),
                $row->created_at?->format('Y-m-d H:i:s') ?? '',
                (string) $row->ip_add,
                trim(implode(', ', array_filter([
                    (string) $row->city_name,
                    (string) $row->region_name,
                    (string) $row->country_name,
                ]))),
                (string) $row->latitude,
                (string) $row->longitude,
                $this->resolvePlatformLabel($row->id_platform, $platforms),
                $this->resolveDeviceLabel($row->id_device, $devices),
                (string) ($row->screen_size ?? ''),
                $this->browserLabel((string) $row->id_browser),
                (string) ($row->browser_version ?? ''),
                strtolower((string) ($row->scan_type ?? '')) === 'gps' ? 'GPS' : 'IP',
            ]));
        }

        $writer->close();

        return response()
            ->download($tmp, 'scan-analytics-'.$profileId.'.xlsx')
            ->deleteFileAfterSend();
    }

    /**
     * Legacy form-analytics "submission rate" = form submissions ÷ scans.
     */
    public function submissionRatePercent(): int
    {
        if ($this->analyticsCount <= 0) {
            return 0;
        }

        return (int) round($this->formSubmissionCount * 100 / $this->analyticsCount);
    }

    /**
     * Top-level scan breakdowns (country / platform / browser) for the PDF bar charts.
     *
     * @return array{country: list<array{label: string, value: int}>, platform: list<array{label: string, value: int}>, browser: list<array{label: string, value: int}>}
     */
    protected function chartBreakdowns(int $profileId): array
    {
        $rows = $this->analyticsQuery($profileId)->get();
        $platforms = $this->platformNames();

        $group = function (\Illuminate\Support\Collection $rows, callable $keyer, callable $labeler): array {
            $out = [];
            foreach ($rows->groupBy($keyer) as $key => $group) {
                $out[] = ['label' => $labeler((string) $key), 'value' => $group->count()];
            }
            usort($out, fn ($a, $b): int => $b['value'] <=> $a['value']);

            return array_slice($out, 0, 12);
        };

        return [
            'country' => $group(
                $rows,
                fn ($r): string => trim((string) $r->country_name) !== '' ? (string) $r->country_name : 'Unknown',
                fn (string $k): string => $k,
            ),
            'platform' => $group(
                $rows,
                fn ($r): string => (string) $r->id_platform,
                fn (string $k): string => $this->resolvePlatformLabel($k, $platforms),
            ),
            'browser' => $group(
                $rows,
                fn ($r): string => (string) $r->id_browser,
                fn (string $k): string => $this->browserLabel($k),
            ),
        ];
    }

    /**
     * Legacy "Download PDF": a real server-generated PDF with rendered chart images
     * (GD bar/pie charts — no browser print, no external services).
     */
    public function downloadPdf(): StreamedResponse
    {
        $profileId = (int) $this->selectedProfileId;
        $bar = app(\App\Services\BarChartRenderer::class);
        $pie = app(\App\Services\PieChartRenderer::class);

        $breakdowns = $this->chartBreakdowns($profileId);

        $barCharts = [
            'Scans by Country' => $bar->renderDataUri($breakdowns['country'], 540, '#185FA5'),
            'Scans by Platform' => $bar->renderDataUri($breakdowns['platform'], 540, '#639922'),
            'Scans by Browser' => $bar->renderDataUri($breakdowns['browser'], 540, '#BA7517'),
        ];

        $formPies = array_map(
            fn (array $chart): array => [
                'title' => $chart['title'],
                'image' => $pie->renderDataUri($chart['slices']),
            ],
            $this->formCharts,
        );

        $html = view('filament.portal.pages.scan-analytics-pdf', [
            'profileName' => $this->profileName,
            'profileId' => $profileId,
            'analyticsCount' => $this->analyticsCount,
            'formSubmissionCount' => $this->formSubmissionCount,
            'submissionRate' => $this->submissionRatePercent(),
            'barCharts' => $barCharts,
            'formPies' => $formPies,
            'locationRows' => $this->locationRows,
            'date' => now()->format('d/m/Y H:i:s'),
        ])->render();

        return response()->streamDownload(function () use ($html): void {
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');
            $pdf->SetCreator('ScanLink');
            $pdf->SetTitle('Scan Analytics');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(12, 12, 12);
            $pdf->SetAutoPageBreak(true, 12);
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');
            echo $pdf->Output('scan-analytics.pdf', 'S');
        }, 'scan-analytics-'.$profileId.'.pdf', ['Content-Type' => 'application/pdf']);
    }

    protected function loadProfileAnalytics(int $profileId): void
    {
        $client = $this->requireClient();

        $profile = Profile::query()
            ->with('equipmentType')
            ->where('client_id', $client->id)
            ->active()
            ->find($profileId);

        if (! $profile) {
            $this->selectedProfileId = null;
            $this->profileName = null;
            $this->typeName = null;
            $this->analyticsCount = 0;
            $this->formSubmissionCount = 0;
            $this->locationRows = collect();
            $this->mapPoints = [];
            $this->loadError = 'This profile was not found for your account, or you do not have access to its Scanalytics.';

            return;
        }

        $this->loadError = null;
        $this->selectedProfileId = $profileId;
        $this->profileName = filled(trim((string) $profile->code_profile_name))
            ? (string) $profile->code_profile_name
            : (string) ($profile->name ?? '');
        $this->typeName = (string) ($profile->equipmentType?->name ?: 'Scanalytics');

        if (! Schema::hasTable('ana_item_analytics')) {
            $this->analyticsCount = 0;
            $this->locationRows = collect();
            $this->mapPoints = [];
        } else {
            $this->analyticsCount = $this->analyticsQuery($profileId)->count();
            $this->reloadLocationRows();
            $this->rebuildMapPoints();
        }

        $this->formSubmissionCount = $this->countFormSubmissions($profileId);

        // Legacy: Form Analytics pies show when enable_form_analytics=1 && form_id!=0.
        $this->formAnalyticsEnabled = (bool) $profile->enable_form_analytics && (int) ($profile->form_id ?? 0) !== 0;
        $this->formCharts = $this->formAnalyticsEnabled
            ? app(\App\Services\CumulativeAnalyticsBuilder::class)->build(collect([$profile]))['form_charts']
            : [];
    }

    protected function analyticsQuery(int $profileId)
    {
        return AnaItemAnalytics::query()->where('id_item', (string) $profileId);
    }

    protected function reloadLocationRows(): void
    {
        $profileId = (int) $this->selectedProfileId;

        if ($profileId <= 0 || ! Schema::hasTable('ana_item_analytics')) {
            $this->locationRows = collect();

            return;
        }

        $query = $this->analyticsQuery($profileId);

        if ($this->locationSort === 'city_name') {
            $query->orderBy('city_name')->orderByDesc('id');
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $platforms = $this->platformNames();
        $devices = $this->deviceNames();

        $this->locationRows = $query->limit(500)->get()->map(function (AnaItemAnalytics $row) use ($platforms, $devices) {
            return (object) [
                'id' => $row->id,
                'created_at' => $row->created_at,
                'ip_add' => (string) $row->ip_add,
                'location_label' => trim(implode(', ', array_filter([
                    (string) $row->city_name,
                    (string) $row->region_name,
                    (string) $row->country_name,
                ]))),
                'latitude' => (string) $row->latitude,
                'longitude' => (string) $row->longitude,
                'device_name' => $this->resolvePlatformLabel($row->id_platform, $platforms),
                'platform_name' => $this->resolveDeviceLabel($row->id_device, $devices),
                'screen_size' => (string) ($row->screen_size ?? ''),
                'browser_name' => $this->browserLabel((string) $row->id_browser),
                'browser_version' => (string) ($row->browser_version ?? ''),
                'scan_type' => strtolower((string) ($row->scan_type ?? '')) === 'gps' ? 'GPS' : 'IP',
            ];
        });
    }

    protected function rebuildMapPoints(): void
    {
        $profileId = (int) $this->selectedProfileId;
        $this->mapPoints = [];

        if ($profileId <= 0 || ! Schema::hasTable('ana_item_analytics')) {
            return;
        }

        $rows = $this->analyticsQuery($profileId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->orderByDesc('id')
            ->limit(500)
            ->get();

        foreach ($rows as $row) {
            $lat = (float) $row->latitude;
            $lng = (float) $row->longitude;

            if ($lat == 0.0 && $lng == 0.0) {
                continue;
            }

            $this->mapPoints[] = [
                'id' => (int) $row->id,
                'lat' => $lat,
                'lng' => $lng,
                'scan_type' => strtolower((string) ($row->scan_type ?? 'ip')),
                'label' => trim(implode(', ', array_filter([
                    (string) $row->city_name,
                    (string) $row->region_name,
                    (string) $row->country_name,
                ]))) ?: ('Scan #'.$row->id),
            ];
        }
    }

    /**
     * Livewire morphs do not re-run inline <script> tags, so Location Map /
     * "Show on map" must boot Leaflet via a client event after each update.
     */
    protected function dispatchMapBoot(): void
    {
        $payload = [
            'points' => $this->mapPoints,
            'focus' => $this->focusRowId,
        ];

        $this->js(
            'window.dispatchEvent(new CustomEvent("sl-sa-boot-map", { detail: '
            .json_encode($payload, JSON_THROW_ON_ERROR)
            .' }))'
        );
    }

    /**
     * @return Collection<int|string, string>
     */
    protected function platformNames(): Collection
    {
        return Schema::hasTable('ana_platforms')
            ? DB::table('ana_platforms')->pluck('platform_name', 'id')
            : collect();
    }

    /**
     * @return Collection<int|string, string>
     */
    protected function deviceNames(): Collection
    {
        return Schema::hasTable('ana_devices')
            ? DB::table('ana_devices')->pluck('device_name', 'id')
            : collect();
    }

    protected function resolvePlatformLabel(mixed $id, Collection $platforms): string
    {
        if ($id === null || $id === '') {
            return 'Unknown';
        }

        if (is_numeric($id)) {
            return (string) ($platforms[(int) $id] ?? ('Platform '.$id));
        }

        return (string) $id;
    }

    protected function resolveDeviceLabel(mixed $id, Collection $devices): string
    {
        if ($id === null || $id === '') {
            return 'Unknown';
        }

        if (is_numeric($id)) {
            return (string) ($devices[(int) $id] ?? ('Device '.$id));
        }

        return (string) $id;
    }

    protected function browserLabel(string $raw): string
    {
        if ($raw === '') {
            return 'Unknown';
        }

        if (! is_numeric($raw)) {
            return $raw;
        }

        if (! Schema::hasTable('ana_browsers')) {
            return 'Browser '.$raw;
        }

        $name = DB::table('ana_browsers')->where('id', (int) $raw)->value('browser_name');

        return filled($name) ? (string) $name : 'Browser '.$raw;
    }

    protected function countFormSubmissions(int $profileId): int
    {
        if (! Schema::hasTable('form_builder_answers')) {
            return 0;
        }

        return (int) FormBuilderAnswer::query()
            ->where('profile_id', $profileId)
            ->whereNotNull('session_id')
            ->where('session_id', '!=', '')
            ->selectRaw('COUNT(DISTINCT session_id) as aggregate')
            ->value('aggregate');
    }
}
