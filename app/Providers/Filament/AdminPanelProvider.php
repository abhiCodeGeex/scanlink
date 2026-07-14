<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AdminHome;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->login()
            // Self-registration disabled — legacy siteadmin had no open signup.
            ->passwordReset()
            ->profile(isSimple: false)
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable()
                    ->brandName('ScanLink'),
            ])
            ->colors([
                // Legacy ScanLink portal green (nav bar from brand screenshot).
                'primary' => Color::hex('#008C00'),
            ])
            ->brandName('ScanLink Admin')
            ->brandLogo(asset('images/scanlink-logo.png'))
            ->darkModeBrandLogo(asset('images/scanlink-logo.png'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('images/scanlink-logo.png'))
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                NavigationGroup::make('Client')->collapsible(),
                NavigationGroup::make('Product')->collapsible(),
                NavigationGroup::make('Order')->collapsible(),
                NavigationGroup::make('Settings')->collapsible(),
                NavigationGroup::make('Testimonial')->collapsible(),
                NavigationGroup::make('Gallery')->collapsible(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                AdminHome::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.navigation-feedback')->render(),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="'.asset('css/filament/scanlink-theme.css').'?v=5">',
            )
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
