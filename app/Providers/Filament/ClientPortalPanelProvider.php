<?php

namespace App\Providers\Filament;

use App\Filament\Portal\Auth\Login;
use App\Filament\Portal\Auth\Register;
use App\Filament\Portal\Pages\EditAccount;
use App\Filament\Portal\Pages\PortalDashboard;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Http\Middleware\EnsurePortalPasswordChanged;
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
     * Legacy How to submenu (application/views/template/menu.php).
     *
     * @return list<array{label: string, url: string}>
     */
    protected function howToTutorials(): array
    {
        return [
            ['label' => 'Create a ScanLink account', 'url' => 'https://www.youtube.com/embed/9aTjweHyAWw?rel=0'],
            ['label' => 'Getting Started', 'url' => 'https://www.youtube.com/embed/o6NxTt0CmYI?rel=0'],
            ['label' => 'Register a new code', 'url' => 'https://www.youtube.com/embed/GZ12nXTO7_w?rel=0'],
            ['label' => 'Upload a logo', 'url' => 'https://www.youtube.com/embed/hGrBYsys2Oo?rel=0'],
            ['label' => 'Upload a video', 'url' => 'https://www.youtube.com/embed/H33caspIlcc?rel=0'],
            ['label' => 'Add text and phone numbers', 'url' => 'https://www.youtube.com/embed/CZx8xplEfoU?rel=0'],
            ['label' => 'Upload pictures', 'url' => 'https://www.youtube.com/embed/GshHCp9F0wU?rel=0'],
            ['label' => 'Upload documents', 'url' => 'https://www.youtube.com/embed/ujiEr65yg30?rel=0'],
            ['label' => 'Add web link buttons', 'url' => 'https://www.youtube.com/embed/id0I8j8RTuY?rel=0'],
            ['label' => 'Add social media and email share buttons', 'url' => 'https://www.youtube.com/embed/qOi6tSBsII4?rel=0'],
            ['label' => 'Create pop up messages to collect data', 'url' => 'https://www.youtube.com/embed/C_vH14MFtXA?rel=0'],
            ['label' => 'Select a code type - QR or Data matrix', 'url' => 'https://www.youtube.com/embed/jCeyQOfm7uc?rel=0'],
            ['label' => 'Feature code profile number on mobile display', 'url' => 'https://www.youtube.com/embed/eJtzHbZoCPw?rel=0'],
            ['label' => 'Password protect a code profile', 'url' => 'https://www.youtube.com/embed/KcXJnxuMVyc?rel=0'],
            ['label' => 'Link a code to a URL', 'url' => 'https://www.youtube.com/embed/uEDTnBPUk28?rel=0'],
            ['label' => 'Delete a code profile', 'url' => 'https://www.youtube.com/embed/Gu12cnKn16s?rel=0'],
            ['label' => 'View and download scan activity', 'url' => 'https://www.youtube.com/embed/Y0bVkzDA5Rc?rel=0'],
            ['label' => 'Create a form', 'url' => 'https://www.youtube.com/embed/cYQnzxkp528?rel=0'],
        ];
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
                // Demo: hide Dashboard menu (uncomment to restore).
                // NavigationItem::make('Dashboard')
                //     ->url(fn (): string => ProfileResource::getUrl('index'))
                //     ->icon(Heroicon::OutlinedHome)
                //     ->isActiveWhen(fn (): bool => request()->is('portal/profiles*'))
                //     ->sort(-30),
                ...$howToItems,
            ])
            ->discoverResources(in: app_path('Filament/Portal/Resources'), for: 'App\\Filament\\Portal\\Resources')
            ->discoverPages(in: app_path('Filament/Portal/Pages'), for: 'App\\Filament\\Portal\\Pages')
            ->pages([
                PortalDashboard::class,
                \App\Filament\Portal\Pages\FormSubmissions::class,
                \App\Filament\Portal\Pages\FormSubmissionView::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Portal/Widgets'), for: 'App\\Filament\\Portal\\Widgets')
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => view('filament.hooks.sidebar-sign-out')->render(),
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
                fn (): string => '<link rel="stylesheet" href="'.asset('css/filament/scanlink-theme.css').'?v=47">',
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
