<?php

use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PortalAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('marketing.home');
Route::post('/portal-login', [PortalAuthController::class, 'login'])->name('marketing.portal-login');

Route::get('/contact', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::post('/contact', [MarketingController::class, 'submitContact'])->name('marketing.contact.submit');
Route::get('/captcha/default', CaptchaController::class)->name('marketing.captcha');
Route::get('/how-to', [MarketingController::class, 'howTo'])->name('marketing.how-to');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::post('/pricing/calculate', [MarketingController::class, 'calculatePricing'])->name('marketing.pricing.calculate');
Route::get('/faq', [MarketingController::class, 'faq'])->name('marketing.faq');
Route::get('/privacy', [MarketingController::class, 'privacy'])->name('marketing.privacy');
Route::get('/terms', [MarketingController::class, 'terms'])->name('marketing.terms');

// Legacy URL aliases (Kohana paths from scanlink.com.au).
Route::redirect('/pricing/index', '/pricing', 301);
Route::redirect('/faq/index', '/faq', 301);
Route::redirect('/faq/index/', '/faq', 301);
Route::redirect('/termsnndcondition/index', '/terms', 301);
Route::redirect('/termsandcondition/index', '/terms', 301);
Route::redirect('/privacypolicy/index', '/privacy', 301);

Route::get('/voclogin', fn () => redirect()->to(route('marketing.home').'#login'));
