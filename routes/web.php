<?php

use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MobileProfileController;
use App\Http\Controllers\PayPalNotifyController;
use App\Services\YouTubeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('marketing.home');

Route::get('/contact', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::post('/contact', [MarketingController::class, 'submitContact'])->name('marketing.contact.submit');
Route::get('/how-to', [MarketingController::class, 'howTo'])->name('marketing.how-to');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::get('/faq', [MarketingController::class, 'faq'])->name('marketing.faq');
Route::get('/privacy', [MarketingController::class, 'privacy'])->name('marketing.privacy');
Route::get('/terms', [MarketingController::class, 'terms'])->name('marketing.terms');

Route::get('/voclogin', fn () => redirect('/portal/login'));

Route::post('/notify/paypal', PayPalNotifyController::class);

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

    \App\Models\Setting::setValue('youtube_refresh_token', $tokens['refresh_token']);

    return response('YouTube connected successfully. You can close this window and return to the admin panel.');
});

Route::get('/{clientUrl}/{profileId}', [MobileProfileController::class, 'show'])
    ->whereNumber('profileId')
    ->name('scan.show');

Route::post('/{clientUrl}/{profileId}/unlock', [MobileProfileController::class, 'unlock'])
    ->whereNumber('profileId')
    ->name('scan.unlock');

Route::post('/{clientUrl}/{profileId}/visitor', [MobileProfileController::class, 'storeVisitor'])
    ->whereNumber('profileId')
    ->name('scan.visitor');

Route::post('/{clientUrl}/{profileId}/form', [MobileProfileController::class, 'storeFormAnswer'])
    ->whereNumber('profileId')
    ->name('scan.form');

Route::post('/{clientUrl}/{profileId}/checklist/{itemId}/check', [MobileProfileController::class, 'checkChecklistItem'])
    ->whereNumber('profileId')
    ->whereNumber('itemId')
    ->name('scan.checklist.check');

Route::post('/{clientUrl}/{profileId}/checklist/{itemId}/uncheck', [MobileProfileController::class, 'uncheckChecklistItem'])
    ->whereNumber('profileId')
    ->whereNumber('itemId')
    ->name('scan.checklist.uncheck');

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/portal/form-submissions/print/{sessionId}', function (Illuminate\Http\Request $request, string $sessionId) {
        return \App\Filament\Portal\Pages\FormSubmissions::downloadSessionHtml(
            $request->integer('profile'),
            $sessionId,
        );
    })->name('portal.form-submissions.print');
});
