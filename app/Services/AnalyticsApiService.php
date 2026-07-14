<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyticsApiService
{
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
                return null;
            }

            $json = $response->json();

            return $json ?? $response->body();
        } catch (\Throwable $exception) {
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
}
