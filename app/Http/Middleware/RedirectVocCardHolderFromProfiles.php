<?php

namespace App\Http\Middleware;

use App\Filament\Portal\Pages\VocDashboard;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectVocCardHolderFromProfiles
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->isVocCardHolder()) {
            return $next($request);
        }

        if (! $request->is('portal/profiles', 'portal/profiles/*')) {
            return $next($request);
        }

        return redirect()->to(VocDashboard::getUrl(panel: 'portal'));
    }
}
