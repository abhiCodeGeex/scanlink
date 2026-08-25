<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public portal base URL used in profile QR codes (legacy: site base + client url)
    |--------------------------------------------------------------------------
    */
    'portal_url' => env('SCANLINK_PORTAL_URL', env('APP_URL', 'http://localhost')),

    /*
    |--------------------------------------------------------------------------
    | Optional short-URL API token (legacy Galatech / custom shortenUrl)
    | When empty, QR codes encode the full profile URL.
    |--------------------------------------------------------------------------
    */
    'short_url_api_token' => env('SCANLINK_SHORT_URL_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Storage paths (relative to the public disk) matching legacy folders
    |--------------------------------------------------------------------------
    */
    'qr_path' => 'qrcode',
    'dm_path' => 'dmcode',

    /*
    |--------------------------------------------------------------------------
    | Galatech / legacy analytics API (public scan ping + charts)
    |--------------------------------------------------------------------------
    */
    'analytics_api_url' => env('SCANLINK_ANALYTICS_API_URL', 'https://www.scanlink.com.au/api/web/api.php/'),
    'analytics_api_fallback_urls' => array_values(array_filter([
        env('SCANLINK_ANALYTICS_API_FALLBACK_URL'),
        // Legacy endpoint kept as fallback for outage scenarios.
        'http://api.galatech.com.au/web/api.php/',
    ])),
    // When remote analytics API is down, still assign a local key so profile setup
    // never blocks and key-dependent screens can proceed.
    'analytics_local_key_fallback' => (bool) env('SCANLINK_ANALYTICS_LOCAL_KEY_FALLBACK', true),

    // Visitor IP -> country/region/city for scan analytics (legacy used ipgeolocation.io).
    'ip_geolocation_enabled' => (bool) env('SCANLINK_IP_GEOLOCATION_ENABLED', true),
    'ip_geolocation_url' => env('SCANLINK_IP_GEOLOCATION_URL', 'http://ip-api.com/json'),

    // Google Maps JS API key for the scan-analytics location map (legacy used Google Maps).
    // Leave empty to fall back to the OpenStreetMap/Leaflet map.
    'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Notification addresses / support details (legacy hardcoded values)
    |--------------------------------------------------------------------------
    */
    'admin_email' => env('SCANLINK_ADMIN_EMAIL', 'admin@scanlink.net.au'),
    'support_email' => env('SCANLINK_SUPPORT_EMAIL', 'support@scanlink.com.au'),
    'support_phone' => env('SCANLINK_SUPPORT_PHONE', '1300 566 696'),

    /*
    |--------------------------------------------------------------------------
    | Testing: only deliver outbound mail to this domain (e.g. yopmail.com).
    | Set SCANLINK_MAIL_RESTRICT_ENABLED=false to allow real recipients again.
    |--------------------------------------------------------------------------
    */
    'mail_restrict_enabled' => (bool) env('SCANLINK_MAIL_RESTRICT_ENABLED', true),
    'mail_restrict_domain' => env('SCANLINK_MAIL_RESTRICT_DOMAIN', 'yopmail.com'),

    /*
    |--------------------------------------------------------------------------
    | Live (production) DB source for `php artisan scanlink:refresh-from-live`.
    | Credentials live in the machine's gitignored .env as LIVE_DB_* — never
    | hardcoded here. Read via config so it survives `config:cache`.
    |--------------------------------------------------------------------------
    */
    'live_db' => [
        'host' => env('LIVE_DB_HOST'),
        'port' => env('LIVE_DB_PORT', '3306'),
        'database' => env('LIVE_DB_DATABASE', 'scanlink_development'),
        'username' => env('LIVE_DB_USERNAME'),
        'password' => env('LIVE_DB_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Physical QR label prices (AUD) — legacy dashboard constants
    |--------------------------------------------------------------------------
    */
    'label_price_small' => (float) env('SCANLINK_LABEL_PRICE_SMALL', 3),
    'label_price_large' => (float) env('SCANLINK_LABEL_PRICE_LARGE', 5),

    /*
    |--------------------------------------------------------------------------
    | Covid check-in Location Description/Type options (legacy LOCATION_DESCRIPTION)
    |--------------------------------------------------------------------------
    */
    'covid_location_descriptions' => [
        'Outdoor Seating',
        'Indoor Seating',
        'Construction site',
        'Showroom/Retail',
        'Office',
        'Vehicle',
        'Cinema/Theatre',
        'Gym/Swimming Pool',
        'Other',
    ],
];
