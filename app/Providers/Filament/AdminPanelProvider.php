<?php

namespace App\Providers\Filament;

use App\Filament\Auth\ScanLinkAppAuthentication;
use App\Filament\Pages\AdminHome;
use Filament\Auth\Pages\EditProfile;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsIconAlias;
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
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->profile(isSimple: false)
            ->multiFactorAuthentication([
                ScanLinkAppAuthentication::make()
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
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->collapsedSidebarWidth('3.25rem')
            ->icons([
                PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON => Heroicon::OutlinedBars3,
                PanelsIconAlias::SIDEBAR_EXPAND_BUTTON => Heroicon::OutlinedBars3,
                PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL => Heroicon::OutlinedBars3,
                PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL => Heroicon::OutlinedBars3,
            ])
            ->globalSearch()
            ->globalSearchDebounce('400ms')
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                NavigationGroup::make('Client')->icon(Heroicon::OutlinedBuildingOffice2)->collapsible(),
                NavigationGroup::make('Reseller')->icon(Heroicon::OutlinedTicket)->collapsible(),
                NavigationGroup::make('Order')->icon(Heroicon::OutlinedShoppingCart)->collapsible(),
                NavigationGroup::make('Settings')->icon(Heroicon::OutlinedCog6Tooth)->collapsible(),
                NavigationGroup::make('Reports')->icon(Heroicon::OutlinedEnvelopeOpen)->collapsible(),
            ])
            ->navigationItems([
                // Replaces Settings → Change Password with Profile (same page as user menu).
                NavigationItem::make('Profile')
                    ->url(fn (): string => EditProfile::getUrl(panel: 'admin'))
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->group('Settings')
                    ->sort(4)
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.auth.profile')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                AdminHome::class,
            ])
            ->homeUrl(fn (): string => AdminHome::getUrl())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => view('filament.hooks.sidebar-sign-out')->render(),
            )
            ->renderHook(
                PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE,
                fn (): string => view('filament.hooks.admin-back-button')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.navigation-feedback')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.sidebar-position')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.modal-open-fix')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.client-users-scroll')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.table-filters')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.form-validation-scroll')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.mobile-photo-capture')->render(),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="'.asset('css/filament/scanlink-theme.css').'?v=81">',
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.hooks.mobile-topbar-fix')->render(),
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
