{{-- Back button on all admin pages except Admin Home / auth. --}}
@php
    use App\Filament\Pages\AdminHome;

    $routeName = optional(request()->route())->getName() ?? '';
    $isAuth = str_contains($routeName, '.auth.') || str_contains($routeName, '.login') || str_contains($routeName, '.password-reset');
    $isHome = $routeName === 'filament.admin.pages.admin-home'
        || str_ends_with($routeName, '.pages.dashboard');

    $path = trim(request()->path(), '/');
    $segments = $path === '' ? [] : explode('/', $path);

    $backUrl = AdminHome::getUrl();

    // /admin/{resource}/{id}/edit|view|users → resource index
    // /admin/{resource}/create → resource index
    if (count($segments) >= 3 && ($segments[0] ?? '') === 'admin') {
        $resource = $segments[1] ?? null;
        $last = $segments[array_key_last($segments)] ?? null;

        if (in_array($last, ['edit', 'view', 'users'], true) || $last === 'create') {
            $backUrl = url('/admin/'.$resource);
        } elseif (count($segments) === 2 && $resource !== 'admin-home') {
            // Resource index / standalone page → Admin Home
            $backUrl = AdminHome::getUrl();
        } elseif (count($segments) === 3 && is_numeric($segments[2] ?? null)) {
            // /admin/{resource}/{id} view-style routes
            $backUrl = url('/admin/'.$resource);
        }
    }
@endphp

@unless ($isAuth || $isHome)
    <a
        href="{{ $backUrl }}"
        wire:navigate
        class="fi-btn fi-color fi-color-gray fi-btn-color-gray fi-size-md fi-ac-btn-action"
        style="margin-inline-end: .5rem;"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="fi-icon fi-size-md" style="width:1.25rem;height:1.25rem;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        <span class="fi-btn-label">Back</span>
    </a>
@endunless
