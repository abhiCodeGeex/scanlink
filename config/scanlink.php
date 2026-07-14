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

    /*
    |--------------------------------------------------------------------------
    | Physical QR label prices (AUD) — legacy dashboard constants
    |--------------------------------------------------------------------------
    */
    'label_price_small' => (float) env('SCANLINK_LABEL_PRICE_SMALL', 3),
    'label_price_large' => (float) env('SCANLINK_LABEL_PRICE_LARGE', 5),
];