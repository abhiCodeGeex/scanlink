{{-- Back button on internal admin pages only (edit / view / create sub-pages, etc.). --}}
@php
    $routeName = optional(request()->route())->getName() ?? '';
    $isAuth = str_contains($routeName, '.auth.') || str_contains($routeName, '.login') || str_contains($routeName, '.password-reset');
    $isHome = $routeName === 'filament.admin.pages.dashboard'
        || $routeName === 'filament.admin.pages.admin-home';

    $path = trim(request()->path(), '/');
    $segments = $path === '' ? [] : explode('/', $path);

    $showBack = false;
    $resourceSlug = null;
    $fallbackUrl = url('/admin');

    if (! $isAuth && ! $isHome && count($segments) >= 2 && ($segments[0] ?? '') === 'admin') {
        $resourceSlug = $segments[1] ?? null;
        $depth = count($segments) - 1;

        if ($depth === 2) {
            $second = $segments[2] ?? null;

            if ($second === 'create') {
                // "Add Client" is a sidebar destination — no back button there.
                $showBack = $resourceSlug !== 'clients';
            } elseif (is_numeric($second)) {
                $showBack = true;
            }
        } elseif ($depth >= 3) {
            $showBack = true;
        }

        if ($showBack && $resourceSlug) {
            $fallbackUrl = url('/admin/'.$resourceSlug);
        }
    }
@endphp

@if ($showBack)
    <button
        type="button"
        class="fi-btn fi-color fi-color-primary fi-btn-color-primary fi-size-md fi-ac-btn-action"
        style="margin-inline-end: .5rem;"
        data-fallback-url="{{ $fallbackUrl }}"
        data-resource="{{ $resourceSlug }}"
        onclick="window.scanlinkAdminBack(this)"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="fi-icon fi-size-md" style="width:1.25rem;height:1.25rem;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        <span class="fi-btn-label">Back</span>
    </button>
@endif
