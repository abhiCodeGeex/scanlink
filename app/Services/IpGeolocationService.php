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

    /**
     * Reverse-geocode precise browser GPS coordinates to country / state / city. This is
     * accurate to the visitor's real location, unlike IP geolocation which only resolves to
     * the ISP / mobile-carrier regional gateway (e.g. Gurugram for a Punjab user). Uses
     * BigDataCloud's free, no-key reverse endpoint by default. Cached and never throws.
     *
     * @return array{country_code: ?string, country_name: ?string, region_name: ?string, city_name: ?string, zipcode: ?string, timezone: ?string, latitude: ?string, longitude: ?string}
     */
    public function reverseLocate(string $lat, string $lng): array
    {
        $empty = [
            'country_code' => null, 'country_name' => null, 'region_name' => null,
            'city_name' => null, 'zipcode' => null, 'timezone' => null,
            'latitude' => null, 'longitude' => null,
        ];

        $lat = trim($lat);
        $lng = trim($lng);

        if (! (bool) config('scanlink.ip_geolocation_enabled', true)) {
            return $empty;
        }

        if ($lat === '' || $lng === '' || ! is_numeric($lat) || ! is_numeric($lng)) {
            return $empty;
        }

        // Cache to ~100m precision so repeat scans from the same spot don't re-call.
        $key = 'revgeo.'.number_format((float) $lat, 3, '.', '').','.number_format((float) $lng, 3, '.', '');

        return Cache::remember($key, now()->addDays(7), function () use ($lat, $lng, $empty): array {
            try {
                $url = (string) config(
                    'scanlink.reverse_geolocation_url',
                    'https://api.bigdatacloud.net/data/reverse-geocode-client',
                );

                $response = Http::timeout(3)->get($url, [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'localityLanguage' => 'en',
                ]);

                if ($response->ok()) {
                    $city = $this->str($response->json('city'))
                        ?: $this->str($response->json('locality'));

                    return [
                        'country_code' => $this->str($response->json('countryCode')),
                        'country_name' => $this->str($response->json('countryName')),
                        'region_name' => $this->str($response->json('principalSubdivision')),
                        'city_name' => $city,
                        'zipcode' => $this->str($response->json('postcode')),
                        'timezone' => null,
                        'latitude' => $lat,
                        'longitude' => $lng,
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
