<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v3 (contact forms)
    |--------------------------------------------------------------------------
    |
    | v3 has nothing to click: the page asks Google for a token when the form is
    | sent, and Google scores the visitor from 0.0 (almost certainly a bot) to
    | 1.0 (almost certainly a person). We check that score server side.
    |
    | Both keys have to be set. With either missing the contact forms fall back
    | to the built-in picture captcha rather than going out with no challenge.
    |
    */

    'recaptcha' => [

        'site_key' => env('GOOGLE_CAPTCHA_SITE_KEY'),

        'secret_key' => env('GOOGLE_CAPTCHA_SECRET_KEY'),

        // Google's own suggested starting point. Raise it if spam still gets
        // through, lower it if real enquiries are being turned away.
        'min_score' => (float) env('GOOGLE_CAPTCHA_MIN_SCORE', 0.5),

        'verify_url' => env(
            'GOOGLE_CAPTCHA_VERIFY_URL',
            'https://www.google.com/recaptcha/api/siteverify',
        ),

        // The action name the page passes to grecaptcha.execute(). Google echoes
        // it back, and checking it stops a token minted on some other page being
        // replayed against the contact form.
        'action' => 'contact',

    ],

];
