@if (filament()->auth()->check())
    @php
        $isSidebarCollapsible = filament()->isSidebarCollapsibleOnDesktop();
        $logoutFormId = 'scanlink-sidebar-logout-' . filament()->getId();
    @endphp

    <div class="scanlink-sidebar-sign-out">
        <form
            id="{{ $logoutFormId }}"
            action="{{ filament()->getLogoutUrl() }}"
            method="post"
            hidden
        >
            @csrf
        </form>

        <ul class="fi-sidebar-nav-groups">
            <li class="fi-sidebar-group">
                <ul class="fi-sidebar-group-items">
                    <li class="fi-sidebar-item fi-sidebar-item-has-url">
                        <a
                            href="#"
                            class="fi-sidebar-item-btn"
                            onclick="event.preventDefault(); document.getElementById(@js($logoutFormId)).submit();"
                        >
                            {{ \Filament\Support\generate_icon_html(
                                \Filament\Support\Icons\Heroicon::OutlinedArrowLeftOnRectangle,
                                attributes: (new \Illuminate\View\ComponentAttributeBag)->class(['fi-sidebar-item-icon']),
                                size: \Filament\Support\Enums\IconSize::Large,
                            ) }}

                            <span
                                class="fi-sidebar-item-label"
                                @if ($isSidebarCollapsible)
                                    x-show="$store.sidebar.isOpen"
                                    x-transition:enter="fi-transition-enter"
                                @endif
                            >
                                {{ __('filament-panels::layout.actions.logout.label') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
@endif
