<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Resolves a visitor IP to country / region / city (legacy did this via ipgeolocation.io).
 * Best-effort and cached per IP — never throws, so it can't break a public scan.
 */
class IpGeolocationService
{
    /**
     * @return array{country_code: ?string, country_name: ?string, region_name: ?string, city_name: ?string, zipcode: ?string, timezone: ?string, latitude: ?string, longitude: ?string}
     */
    public function locate(string $ip): array
    {
        $empty = [
            'country_code' => null, 'country_name' => null, 'region_name' => null,
            'city_name' => null, 'zipcode' => null, 'timezone' => null,
            'latitude' => null, 'longitude' => null,
        ];

        $ip = trim($ip);

        if (! (bool) config('scanlink.ip_geolocation_enabled', true)) {
            return $empty;
        }

        // Skip empty / private / reserved addresses — no useful geo, and avoids wasted calls.
        if ($ip === '' || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $empty;
        }

        return Cache::remember('ipgeo.'.$ip, now()->addDays(7), function () use ($ip, $empty): array {
            try {
                $endpoint = rtrim((string) config('scanlink.ip_geolocation_url', 'http://ip-api.com/json'), '/').'/'.$ip;

                $response = Http::timeout(3)->get($endpoint, [
                    'fields' => 'status,countryCode,country,regionName,city,zip,timezone,lat,lon',
                ]);

                if ($response->ok() && $response->json('status') === 'success') {
                    return [
                        'country_code' => $this->str($response->json('countryCode')),
                        'country_name' => $this->str($response->json('country')),
                        'region_name' => $this->str($response->json('regionName')),
                        'city_name' => $this->str($response->json('city')),
                        'zipcode' => $this->str($response->json('zip')),
                        'timezone' => $this->str($response->json('timezone')),
                        'latitude' => $this->str($response->json('lat')),
                        'longitude' => $this->str($response->json('lon')),
                    ];
                }
            } catch (\Throwable) {
                // best effort — fall through to empty
            }

            return $empty;
        });
    }

    private function str(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }
}
