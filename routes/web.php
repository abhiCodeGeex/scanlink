<?php

use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PortalAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('marketing.home');
Route::post('/portal-login', [PortalAuthController::class, 'login'])->name('marketing.portal-login');
Route::get('/captcha/default', CaptchaController::class)->name('marketing.captcha');
