@php
    $isActive = $record->isResellerCodeActive();
    $code = $record->reseller_code ?: 'Reseller code';
@endphp

<header class="fi-header fi-header-has-breadcrumbs">
    <div>
        @if ($breadcrumbs)
            <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
        @endif

        <h1 class="fi-header-heading" style="display:inline-flex;align-items:center;gap:0.625rem;flex-wrap:wrap;">
            <span>Usage history — {{ $code }}</span>
            <span style="display:inline-flex;align-items:center;border-radius:0.375rem;padding:0.15rem 0.55rem;font-size:0.75rem;font-weight:600;line-height:1.25;letter-spacing:0.01em;
                @if ($isActive)
                    background:#dcfce7;color:#15803d;
                @else
                    background:#fee2e2;color:#b91c1c;
                @endif
            ">
                {{ $isActive ? 'Active' : 'Inactive' }}
            </span>
        </h1>
    </div>

    <div class="fi-header-actions-ctn">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_BEFORE, scopes: $scopes) }}
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER, scopes: $scopes) }}
    </div>
</header>
