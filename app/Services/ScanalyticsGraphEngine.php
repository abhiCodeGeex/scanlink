<?php

namespace App\Services;

use App\Models\AnaItemAnalytics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Builds nested ddBarChart JSON matching legacy graphengine/*.php.
 */
class ScanalyticsGraphEngine
{
    /**
     * @return array{COLUMNS: list<string>, DATA: list<mixed>}
     */
    public function country(int $profileId): array
    {
        $rows = $this->rows($profileId);
        $data = [];

        foreach ($rows->groupBy(fn ($r) => (string) $r->country_name)->filter(fn ($g, $k) => $k !== '') as $country => $countryRows) {
            $stateData = [];

            foreach ($countryRows->groupBy(fn ($r) => (string) $r->region_name)->filter(fn ($g, $k) => $k !== '') as $region => $regionRows) {
                $cityData = [];

                foreach ($regionRows->groupBy(fn ($r) => (string) $r->city_name)->filter(fn ($g, $k) => $k !== '') as $city => $cityRows) {
                    $cityData[] = ['City Statistics', $city, $cityRows->count(), $city.' ^ '];
                }

                $stateData[] = [
                    'State Statistics',
                    $region,
                    $regionRows->count(),
                    $region.' ^ ',
                    ['COLUMNS' => ['CONTEXT', 'X_BAR_LABEL', 'X_BAR_VALUE', 'TOOL_TIP_TITLE'], 'DATA' => $cityData],
                ];
            }

            $data[] = [
                'Country Statistics',
                $country,
                $countryRows->count(),
                $country.' ^ ',
                ['COLUMNS' => ['CONTEXT', 'X_BAR_LABEL', 'X_BAR_VALUE', 'TOOL_TIP_TITLE', 'DRILL_DATA'], 'DATA' => $stateData],
            ];
        }

        return $this->envelope($data, withDrill: true);
    }

    /**
     * @return array{COLUMNS: list<string>, DATA: list<mixed>}
     */
    public function device(int $profileId): array
    {
        $rows = $this->rows($profileId);
        $platforms = $this->platformNames();
        $devices = $this->deviceNames();
        $data = [];

        foreach ($rows->groupBy(fn ($r) => (string) $r->id_platform)->filter(fn ($g, $k) => $k !== '') as $platformId => $platformRows) {
            $deviceData = [];

            foreach ($platformRows->groupBy(fn ($r) => (string) $r->id_device)->filter(fn ($g, $k) => $k !== '') as $deviceId => $deviceRows) {
                $browserData = [];

                foreach ($deviceRows->groupBy(fn ($r) => (string) $r->id_browser)->filter(fn ($g, $k) => $k !== '') as $browserId => $browserRows) {
                    $browserData[] = [
                        'Devics Browser Statistics',
                        $this->browserLabel($browserId),
                        $browserRows->count(),
                        $this->browserLabel($browserId).'  ^ ',
                    ];
                }

                $deviceLabel = $this->resolveName($deviceId, $devices, 'Device');
                $deviceData[] = [
                    'Platfrom Statistics',
                    $deviceLabel,
                    $deviceRows->count(),
                    $deviceLabel.' ^ ',
                    ['COLUMNS' => ['CONTEXT', 'X_BAR_LABEL', 'X_BAR_VALUE', 'TOOL_TIP_TITLE'], 'DATA' => $browserData],
                ];
            }

            $platformLabel = $this->resolveName($platformId, $platforms, 'Platform');
            $data[] = [
                'Devics Statistics',
                $platformLabel,
                $platformRows->count(),
                $platformLabel.' Platfrom ^ ',
                ['COLUMNS' => ['CONTEXT', 'X_BAR_LABEL', 'X_BAR_VALUE', 'TOOL_TIP_TITLE', 'DRILL_DATA'], 'DATA' => $deviceData],
            ];
        }

        return $this->envelope($data, withDrill: true);
    }

    /**
     * @return array{COLUMNS: list<string>, DATA: list<mixed>}
     */
    public function browser(int $profileId): array
    {
        $rows = $this->rows($profileId);
        $platforms = $this->platformNames();
        $data = [];

        foreach ($rows->groupBy(fn ($r) => (string) $r->id_browser)->filter(fn ($g, $k) => $k !== '') as $browserId => $browserRows) {
            $platformData = [];

            foreach ($browserRows->groupBy(fn ($r) => (string) $r->id_platform)->filter(fn ($g, $k) => $k !== '') as $platformId => $platformRows) {
                $versionData = [];

                foreach ($platformRows->groupBy(fn ($r) => (string) ($r->browser_version ?? ''))->filter(fn ($g, $k) => $k !== '') as $version => $versionRows) {
                    $label = trim($this->browserLabel($browserId).' '.$version);
                    $versionData[] = [' Browser Version Statistics', $label, $versionRows->count(), $label.'  ^ '];
                }

                $platformLabel = $this->resolveName($platformId, $platforms, 'Platform');
                $platformData[] = [
                    'Devics Statistics',
                    $platformLabel,
                    $platformRows->count(),
                    $platformLabel.' ^ ',
                    ['COLUMNS' => ['CONTEXT', 'X_BAR_LABEL', 'X_BAR_VALUE', 'TOOL_TIP_TITLE'], 'DATA' => $versionData],
                ];
            }

            $browserLabel = $this->browserLabel($browserId);
            $data[] = [
                'Browser Statistics',
                $browserLabel,
                $browserRows->count(),
                $browserLabel.' ^ ',
                ['COLUMNS' => ['CONTEXT', 'X_BAR_LABEL', 'X_BAR_VALUE', 'TOOL_TIP_TITLE', 'DRILL_DATA'], 'DATA' => $platformData],
            ];
        }

        return $this->envelope($data, withDrill: true);
    }

    /**
     * @return Collection<int, AnaItemAnalytics>
     */
    protected function rows(int $profileId): Collection
    {
        if (! Schema::hasTable('ana_item_analytics')) {
            return collect();
        }

        return AnaItemAnalytics::query()
            ->where('id_item', (string) $profileId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @param  list<mixed>  $data
     * @return array{COLUMNS: list<string>, DATA: list<mixed>}
     */
    protected function envelope(array $data, bool $withDrill): array
    {
        $columns = ['CONTEXT', 'X_BAR_LABEL', 'X_BAR_VALUE', 'TOOL_TIP_TITLE'];

        if ($withDrill) {
            $columns[] = 'DRILL_DATA';
        }

        return [
            'COLUMNS' => $columns,
            'DATA' => array_values($data),
        ];
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

    protected function resolveName(mixed $id, Collection $names, string $fallback): string
    {
        if ($id === null || $id === '') {
            return 'Unknown';
        }

        if (is_numeric($id)) {
            return (string) ($names[(int) $id] ?? ($fallback.' '.$id));
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
}
