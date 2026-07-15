<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use App\Filament\Portal\Pages\ForcePasswordChange;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalPasswordChanged
{
    /**
     * @var list<string>
     */
    protected array $except = [
        'filament.portal.auth.login',
        'filament.portal.auth.logout',
        'filament.portal.auth.password-reset.request',
        'filament.portal.auth.password-reset.reset',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || $user->user_type !== UserType::Portal) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        $member = $user->clientMemberships()
            ->where('status', true)
            ->orderByDesc('role')
            ->first();

        if ($member?->is_password_change) {
            return redirect()->to(ForcePasswordChange::getUrl());
        }

        return $next($request);
    }

    protected function shouldBypass(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if ($routeName !== null && in_array($routeName, $this->except, true)) {
            return true;
        }

        $path = trim($request->path(), '/');

        if (str_starts_with($path, 'portal/login')
            || str_starts_with($path, 'portal/logout')
            || str_starts_with($path, 'portal/password-reset')
            || str_starts_with($path, 'portal/force-password-change')) {
            return true;
        }

        return false;
    }
}
