<?php

namespace App\Providers\Filament;

use App\Filament\Portal\Auth\Register;
use App\Filament\Portal\Pages\PortalDashboard;
use App\Http\Middleware\EnsurePortalPasswordChanged;
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

class ClientPortalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('portal')
            ->path('portal')
            ->spa()
            ->login()
            ->registration(Register::class)
            ->passwordReset()
            ->profile(isSimple: false)
            ->colors([
                'primary' => Color::hex('#008C00'),
            ])
            ->brandName('ScanLink')
            ->brandLogo(asset('images/scanlink-logo.png'))
            ->darkModeBrandLogo(asset('images/scanlink-logo.png'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('images/scanlink-logo.png'))
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                NavigationGroup::make('Codes')->collapsible(),
                NavigationGroup::make('Account')->collapsible(),
                NavigationGroup::make('Orders')->collapsible(),
                NavigationGroup::make('Forms')->collapsible(),
                NavigationGroup::make('VOC')->collapsible(),
            ])
            ->discoverResources(in: app_path('Filament/Portal/Resources'), for: 'App\\Filament\\Portal\\Resources')
            ->discoverPages(in: app_path('Filament/Portal/Pages'), for: 'App\\Filament\\Portal\\Pages')
            ->pages([
                PortalDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Portal/Widgets'), for: 'App\\Filament\\Portal\\Widgets')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="'.asset('css/filament/scanlink-theme.css').'?v=3">',
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
                EnsurePortalPasswordChanged::class,
            ]);
    }
}
