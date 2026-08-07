<?php

namespace App\Providers\Filament;

use App\Filament\Portal\Auth\Login;
use App\Filament\Portal\Auth\Register;
use App\Filament\Portal\Pages\EditAccount;
use App\Filament\Portal\Pages\FormSubmissions;
use App\Filament\Portal\Pages\FormSubmissionView;
use App\Filament\Portal\Pages\PortalDashboard;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Http\Middleware\EnsurePortalPasswordChanged;
use App\Models\HowToTutorial;
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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ClientPortalPanelProvider extends PanelProvider
{
    /**
     * How to submenu — DB catalog with legacy defaults fallback.
     *
     * @return list<array{label: string, url: string}>
     */
    protected function howToTutorials(): array
    {
        return array_map(
            fn (array $item): array => [
                'label' => $item['title'],
                'url' => $item['url'],
            ],
            HowToTutorial::catalog(),
        );
    }

    public function panel(Panel $panel): Panel
    {
        $howToItems = [];

        foreach ($this->howToTutorials() as $index => $tutorial) {
            $howToItems[] = NavigationItem::make($tutorial['label'])
                ->url($tutorial['url'], shouldOpenInNewTab: true)
                ->icon(Heroicon::OutlinedPlayCircle)
                ->group('How to')
                ->sort(100 + $index);
        }

        return $panel
            ->id('portal')
            ->path('portal')
            ->spa()
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->profile(isSimple: false)
            // Demo: land on Edit user profile (was Master Code List).
            ->homeUrl(fn (): string => EditAccount::getUrl())
            // ->homeUrl(fn (): string => ProfileResource::getUrl('index'))
            ->colors([
                'primary' => Color::hex('#008C00'),
            ])
            ->brandName('ScanLink')
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
            ->collapsibleNavigationGroups()
            // Live logged-in top nav: Contact us | Dashboard | My Account | How to
            ->navigationGroups([
                NavigationGroup::make('My Account')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->collapsible(),
                NavigationGroup::make('How to')
                    ->icon(Heroicon::OutlinedQuestionMarkCircle)
                    ->collapsible(),
            ])
            ->navigationItems([
                NavigationItem::make('Dashboard')
                    ->url(fn (): string => ProfileResource::getUrl('index'))
                    ->icon(Heroicon::OutlinedHome)
                    ->isActiveWhen(fn (): bool => request()->is('portal/profiles*'))
                    ->sort(-30),
                ...$howToItems,
            ])
            ->discoverResources(in: app_path('Filament/Portal/Resources'), for: 'App\\Filament\\Portal\\Resources')
            ->discoverPages(in: app_path('Filament/Portal/Pages'), for: 'App\\Filament\\Portal\\Pages')
            ->pages([
                PortalDashboard::class,
                FormSubmissions::class,
                FormSubmissionView::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Portal/Widgets'), for: 'App\\Filament\\Portal\\Widgets')
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => view('filament.hooks.sidebar-sign-out')->render(),
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
                fn (): string => view('filament.hooks.table-filters')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.hooks.form-validation-scroll')->render(),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="'.asset('css/filament/scanlink-theme.css').'?v=72">'
                    .'<link rel="stylesheet" href="'.asset('css/filament/portal-dark.css').'?v=4">',
            )
            ->widgets([])
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
