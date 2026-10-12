<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Who mints the short links a QR code carries
    |--------------------------------------------------------------------------
    |
    | "tinyurl" - ask api.tinyurl.com for one, which is what the app has always
    |             done and what every link printed so far depends on.
    |
    | "local"   - mint it ourselves and serve it from our own short domain, so a
    |             printed code depends on nothing outside this application.
    |
    | Left on tinyurl so that deploying this changes nothing until the domain is
    | in place and someone deliberately switches over. With "local" selected but
    | no domain configured it falls back to tinyurl rather than minting links
    | that point nowhere.
    |
    */

    'driver' => env('SHORT_LINK_DRIVER', 'tinyurl'),

    /*
    |--------------------------------------------------------------------------
    | The short domain
    |--------------------------------------------------------------------------
    |
    | Host only, no scheme or trailing slash: "scnlk.co". It must point at this
    | same application - the redirect route is registered against this host and
    | nowhere else, which is what keeps a bare /{code} from colliding with
    | /contact, /pricing and the rest of the marketing site.
    |
    | Keep it short. The whole benefit is a smaller URL, which makes a less
    | dense QR that scans better in poor light and at distance.
    |
    */

    'domain' => env('SHORT_LINK_DOMAIN'),

    /*
    | A printed code has to resolve for years, so these links should be https in
    | anything but local development.
    */

    'scheme' => env('SHORT_LINK_SCHEME', 'https'),

    /*
    | Uppercase digits and letters without I, L, O and U (32 characters), so 7
    | characters is about 34 billion codes. Random rather than sequential: a
    | counter would let anyone walk every client's codes.
    */

    'length' => (int) env('SHORT_LINK_LENGTH', 7),

];
