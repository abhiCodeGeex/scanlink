<?php

namespace App\Support;

/**
 * Resolve the browser admin URL path during Filament page renders.
 *
 * Livewire actions POST to /livewire/update, so request()->path() is wrong while
 * header hooks re-render. Prefer the Livewire snapshot memo path or Referer.
 */
final class AdminRequestPath
{
    public static function current(): string
    {
        $path = trim((string) request()->path(), '/');

        if ($path !== '' && ! str_starts_with($path, 'livewire')) {
            return $path;
        }

        foreach (self::candidatePaths() as $candidate) {
            $candidate = trim($candidate, '/');

            if ($candidate !== '' && str_starts_with($candidate, 'admin')) {
                return $candidate;
            }
        }

        return $path;
    }

    /**
     * @return list<string>
     */
    private static function candidatePaths(): array
    {
        $candidates = [];

        $snapshot = data_get(request()->all(), 'components.0.snapshot');

        if (is_string($snapshot) && $snapshot !== '') {
            $decoded = json_decode($snapshot, true);
            $memoPath = data_get($decoded, 'memo.path');

            if (is_string($memoPath) && $memoPath !== '') {
                $candidates[] = $memoPath;
            }
        }

        foreach ([
            request()->header('X-Livewire-Original-Url'),
            request()->header('Referer'),
            url()->previous(),
        ] as $url) {
            if (! is_string($url) || $url === '') {
                continue;
            }

            $parsed = parse_url($url, PHP_URL_PATH);

            if (is_string($parsed) && $parsed !== '') {
                $candidates[] = $parsed;
            }
        }

        return $candidates;
    }

    /**
     * Whether the admin Back button should appear for this path.
     *
     * @return array{show: bool, resource: ?string, fallback: string}
     */
    public static function backButtonState(?string $path = null, ?string $routeName = null): array
    {
        $path ??= self::current();
        $routeName ??= optional(request()->route())->getName() ?? '';

        $isAuth = str_contains($routeName, '.auth.')
            || str_contains($routeName, '.login')
            || str_contains($routeName, '.password-reset')
            || str_contains($path, 'admin/login')
            || str_contains($path, 'admin/password-reset');

        $isHome = $routeName === 'filament.admin.pages.dashboard'
            || $routeName === 'filament.admin.pages.admin-home'
            || $path === 'admin'
            || $path === 'admin/admin-home'
            || $path === 'admin/dashboard';

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

        return [
            'show' => $showBack,
            'resource' => $resourceSlug,
            'fallback' => $fallbackUrl,
        ];
    }
}
