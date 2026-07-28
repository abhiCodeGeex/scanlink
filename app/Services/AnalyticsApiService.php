<?php

namespace App\Services;

use App\Models\AnaItemAnalytics;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AnalyticsApiService
{
    /** @var bool|null null = unknown, false = last call failed, true = last call ok */
    protected static ?bool $remoteAvailable = null;

    protected function baseUrl(): string
    {
        return rtrim((string) config('scanlink.analytics_api_url'), '/').'/';
    }

    protected function get(string $path, array $query = []): mixed
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get($this->baseUrl().ltrim($path, '/'), $query);

            if (! $response->successful()) {
                static::$remoteAvailable = false;

                return null;
            }

            $json = $response->json();
            static::$remoteAvailable = true;

            return $json ?? $response->body();
        } catch (\Throwable $exception) {
            static::$remoteAvailable = false;

            Log::warning('Analytics API request failed', [
                'path' => $path,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public function registerUrl(string $url): ?object
    {
        $data = $this->get('item/addurl', ['url' => $url]);

        if (is_array($data)) {
            return (object) $data;
        }

        if (is_string($data) && $data !== '') {
            $decoded = json_decode($data);

            return is_object($decoded) ? $decoded : null;
        }

        return null;
    }

    public function clearItem(string $key): bool
    {
        $data = $this->get('item/clearItem', ['key' => $key]);

        return $data !== null;
    }

    public function getChartData(string $key): ?array
    {
        $data = $this->get('data/getdata', ['key' => $key]);

        return is_array($data) ? $data : null;
    }

    public function getMapData(string $key): ?array
    {
        $data = $this->get('data/map', ['key' => $key]);

        return is_array($data) ? $data : null;
    }

    public function getScanList(string $key): ?array
    {
        $data = $this->get('data/scanalytics', ['key' => $key]);

        return is_array($data) ? $data : null;
    }

    /**
     * Prefer local ana_item_analytics (same source as portal Scanalytics), then Galatech API.
     * Local-first avoids N remote round-trips when the Galatech endpoint is down.
     *
     * @return array{rows: list<array<string, mixed>>, source: 'api'|'local'|'none'}
     */
    public function getScanListForProfile(int $profileId, ?string $analyticKey = null, int $limit = 500): array
    {
        $local = $this->getLocalScanList($profileId, $limit);

        if ($local !== []) {
            return [
                'rows' => $local,
                'source' => 'local',
            ];
        }

        if (filled($analyticKey) && static::$remoteAvailable !== false) {
            $remote = $this->getScanList((string) $analyticKey);

            if (is_array($remote) && $remote !== []) {
                return [
                    'rows' => array_values($remote),
                    'source' => 'api',
                ];
            }
        }

        return [
            'rows' => [],
            'source' => 'none',
        ];
    }

    /**
     * @return list<array{date: string, location: string, device: string, details: string, datetime?: string, city?: string, country?: string, platform?: string, browser?: string}>
     */
    public function getLocalScanList(int $profileId, int $limit = 500): array
    {
        if ($profileId <= 0 || ! Schema::hasTable('ana_item_analytics')) {
            return [];
        }

        $platforms = Schema::hasTable('ana_platforms')
            ? DB::table('ana_platforms')->pluck('platform_name', 'id')
            : collect();
        $devices = Schema::hasTable('ana_devices')
            ? DB::table('ana_devices')->pluck('device_name', 'id')
            : collect();
        $browsers = Schema::hasTable('ana_browsers')
            ? DB::table('ana_browsers')->pluck('browser_name', 'id')
            : collect();

        return AnaItemAnalytics::query()
            ->where('id_item', (string) $profileId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function (AnaItemAnalytics $row) use ($platforms, $devices, $browsers): array {
                $location = trim(implode(', ', array_filter([
                    (string) $row->city_name,
                    (string) $row->region_name,
                    (string) $row->country_name,
                ])));

                $platform = $this->resolveLookupLabel($row->id_platform, $platforms, 'Platform');
                $device = $this->resolveLookupLabel($row->id_device, $devices, 'Device');
                $browser = $this->resolveLookupLabel($row->id_browser, $browsers, 'Browser');
                $deviceLabel = trim(implode(' / ', array_filter([$platform, $device, $browser])));

                $date = $row->created_at
                    ? $row->created_at->format('Y-m-d H:i:s')
                    : '';

                return [
                    'date' => $date,
                    'datetime' => $date,
                    'location' => $location,
                    'city' => (string) $row->city_name,
                    'country' => (string) $row->country_name,
                    'device' => $deviceLabel !== '' ? $deviceLabel : 'Unknown',
                    'platform' => $platform,
                    'browser' => $browser,
                    'details' => trim(implode(' | ', array_filter([
                        strtolower((string) ($row->scan_type ?? '')) === 'gps' ? 'GPS' : 'IP',
                        (string) ($row->ip_add ?? ''),
                        (string) ($row->screen_size ?? ''),
                    ]))),
                ];
            })
            ->all();
    }

    /**
     * @param  \Illuminate\Support\Collection<int|string, mixed>  $lookup
     */
    protected function resolveLookupLabel(mixed $id, $lookup, string $fallbackPrefix): string
    {
        if ($id === null || $id === '') {
            return '';
        }

        if (is_numeric($id)) {
            $name = $lookup[(int) $id] ?? null;

            return filled($name) ? (string) $name : $fallbackPrefix.' '.$id;
        }

        return (string) $id;
    }
}
