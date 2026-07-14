<?php

use App\Models\Setting;
use App\Services\YouTubeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth/youtube/callback', function (Request $request, YouTubeService $youtube) {
    $code = $request->string('code')->toString();

    if ($code === '') {
        return response('Missing authorization code.', 400);
    }

    $redirectUri = url('/oauth/youtube/callback');

    try {
        $tokens = $youtube->exchangeAuthorizationCode($code, $redirectUri);
    } catch (\Throwable $exception) {
        return response('OAuth failed: '.$exception->getMessage(), 500);
    }

    if (empty($tokens['refresh_token'])) {
        return response('No refresh token returned. Revoke prior access in Google Account and authorize again.', 422);
    }

    Setting::setValue('youtube_refresh_token', $tokens['refresh_token']);

    return response('YouTube connected successfully. You can close this window and return to the admin panel.');
});
