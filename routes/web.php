<?php

use App\Http\Controllers\Portal\FormBuilderUploadController;
use App\Http\Controllers\Portal\LegacyFormBuilderController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\ExpressCodeController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MobileProfileController;
use App\Http\Controllers\PayPalNotifyController;
use App\Http\Controllers\PortalAuthController;
use App\Services\YouTubeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('marketing.home');
Route::post('/portal-login', [PortalAuthController::class, 'login'])->name('marketing.portal-login');
// Login is POST-only; a stale tab / expired session refresh lands here as a GET (405 error).
// Send the visitor back to the home page login instead of an error page.
Route::get('/portal-login', fn () => redirect()->route('marketing.home'));

Route::get('/contact', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::post('/contact', [MarketingController::class, 'submitContact'])->name('marketing.contact.submit');
Route::get('/captcha/default', CaptchaController::class)->name('marketing.captcha');
Route::get('/how-to', [MarketingController::class, 'howTo'])->name('marketing.how-to');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::post('/pricing/calculate', [MarketingController::class, 'calculatePricing'])->name('marketing.pricing.calculate');

// Express Code Generator (public home page) — parity with legacy qrhome.
Route::post('/express-code/preview', [ExpressCodeController::class, 'preview'])->name('marketing.express.preview');
Route::get('/express-code/download', [ExpressCodeController::class, 'download'])->name('marketing.express.download');
Route::get('/faq', [MarketingController::class, 'faq'])->name('marketing.faq');
Route::get('/privacy', [MarketingController::class, 'privacy'])->name('marketing.privacy');
Route::get('/terms', [MarketingController::class, 'terms'])->name('marketing.terms');

// Marketing solution / landing pages (parity with legacy marketing/workplace/formbuilderpage).
Route::get('/solutions/packaging-signage', [MarketingController::class, 'packaging'])->name('marketing.packaging');
Route::get('/solutions/for-you', [MarketingController::class, 'forYou'])->name('marketing.forYou');
Route::get('/solutions/workplace', [MarketingController::class, 'workplace'])->name('marketing.workplace');
Route::get('/solutions/forms', [MarketingController::class, 'forms'])->name('marketing.forms');
Route::get('/mobile-video', [MarketingController::class, 'mobileVideo'])->name('marketing.mobileVideo');
Route::get('/enquiry', [MarketingController::class, 'enquiry'])->name('marketing.enquiry');
Route::post('/enquiry', [MarketingController::class, 'submitEnquiry'])->name('marketing.enquiry.submit');

// Legacy URL aliases (Kohana paths from scanlink.com.au).
Route::redirect('/pricing/index', '/pricing', 301);
Route::redirect('/faq/index', '/faq', 301);
Route::redirect('/faq/index/', '/faq', 301);
Route::redirect('/termsnndcondition/index', '/terms', 301);
Route::redirect('/termsandcondition/index', '/terms', 301);
Route::redirect('/privacypolicy/index', '/privacy', 301);

Route::get('/voclogin', fn () => redirect()->to(route('marketing.home').'#login'));

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

// Async scan geolocation ping (GPS + IP-derived country/city) attached to the scan row.
Route::post('/{clientUrl}/{profileId}/scan-geo', [MobileProfileController::class, 'recordScanGeo'])
    ->whereNumber('profileId')
    ->name('scan.geo');

Route::post('/{clientUrl}/{profileId}/checklist/{itemId}/check', [MobileProfileController::class, 'checkChecklistItem'])
    ->whereNumber('profileId')
    ->whereNumber('itemId')
    ->name('scan.checklist.check');

Route::post('/{clientUrl}/{profileId}/checklist/{itemId}/uncheck', [MobileProfileController::class, 'uncheckChecklistItem'])
    ->whereNumber('profileId')
    ->whereNumber('itemId')
    ->name('scan.checklist.uncheck');

Route::middleware(['web', 'auth'])->group(function (): void {
    // VOC "Email Notification Settings" Preview — renders the document-expiry email
    // (30-day renewal reminder / expiry notice) from the saved profile.
    Route::get('/portal/profiles/{profile}/voc-email-preview/{kind}', [\App\Http\Controllers\VocEmailPreviewController::class, 'show'])
        ->whereNumber('profile')
        ->where('kind', 'reminder|expired')
        ->name('portal.voc.email-preview');

    Route::get('/portal/graphengine/country', [\App\Http\Controllers\Portal\ScanalyticsGraphController::class, 'country'])
        ->name('portal.graphengine.country');
    Route::get('/portal/graphengine/device', [\App\Http\Controllers\Portal\ScanalyticsGraphController::class, 'device'])
        ->name('portal.graphengine.device');
    Route::get('/portal/graphengine/browser', [\App\Http\Controllers\Portal\ScanalyticsGraphController::class, 'browser'])
        ->name('portal.graphengine.browser');
    // Isolated standalone charts page (legacy jQuery ddchart + jQuery-UI theme) embedded via iframe.
    Route::get('/portal/scanalytics-charts', [\App\Http\Controllers\Portal\ScanalyticsGraphController::class, 'charts'])
        ->name('portal.graphengine.charts');
    Route::get('/portal/scanalytics-export', [\App\Http\Controllers\Portal\ScanalyticsGraphController::class, 'export'])
        ->name('portal.graphengine.export');

    Route::get('/portal/legacy-form-builder', [LegacyFormBuilderController::class, 'index'])
        ->name('portal.legacy-form-builder');
    Route::match(['get', 'post'], '/portal/legacy-form-builder/render-element', [LegacyFormBuilderController::class, 'renderElement'])
        ->name('portal.legacy-form-builder.render');
    Route::match(['get', 'post'], '/portal/legacy-form-builder/remove-element', [LegacyFormBuilderController::class, 'removeElement'])
        ->name('portal.legacy-form-builder.remove');
    Route::match(['get', 'post'], '/portal/legacy-form-builder/reorder-element', [LegacyFormBuilderController::class, 'reorderElement'])
        ->name('portal.legacy-form-builder.reorder');
    Route::match(['get', 'post'], '/portal/legacy-form-builder/update-enable-form', [LegacyFormBuilderController::class, 'updateEnableForm'])
        ->name('portal.legacy-form-builder.enable');

    // Remove a video from the client's library (the existing-videos picker table).
    Route::post('/portal/videos/{video}/remove', function (\App\Models\Video $video) {
        $member = \App\Models\ClientUser::query()
            ->where('auth_user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('client_id', $video->client_id)
            ->active()
            ->first();

        abort_unless($member !== null, 403);

        $video->delete();

        return response()->json(['ok' => true]);
    })->name('portal.videos.remove');

    Route::get('/test/temp_image_upload', [FormBuilderUploadController::class, 'imageForm']);
    Route::get('/test/temp_doc_upload', [FormBuilderUploadController::class, 'docForm']);
    Route::get('/test/temp_doc_multi_upload', [FormBuilderUploadController::class, 'docMultiForm']);
    Route::post('/portal/legacy-form-builder/upload-image', [FormBuilderUploadController::class, 'storeImage'])
        ->name('portal.legacy-form-builder.upload-image');
    Route::post('/portal/legacy-form-builder/upload-doc', [FormBuilderUploadController::class, 'storeDoc'])
        ->name('portal.legacy-form-builder.upload-doc');

    Route::get('/portal/form-submissions/print/{sessionId}', function (Illuminate\Http\Request $request, string $sessionId) {
        return \App\Filament\Portal\Pages\FormSubmissions::downloadSessionHtml(
            $request->integer('profile'),
            $sessionId,
        );
    })->name('portal.form-submissions.print');
});
