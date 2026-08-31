<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a VOCC card holder on their own card.
 *
 * Legacy fenced them in hard: every dashboard action in Controller_Dashboard begins by
 * checking voc_user_id and redirecting to dashboard/voc_user, so a card holder has no route
 * to the client's other codes, purchasing, team users or analytics. In the rebuilt portal a
 * VOCC login is a real auth identity that may enter the portal panel, and only the profiles
 * list was blocked — as a side effect of having no client membership, not as a rule.
 *
 * This restores the legacy rule as an allowlist: anything not on a card holder's own set of
 * pages sends them back to the VOC dashboard.
 */
class ConfineVocCardHolder
{
    /**
     * Portal paths a card holder is entitled to, relative to the panel path.
     *
     * @var list<string>
     */
    protected const ALLOWED = [
        'voc-dashboard',
        'edit-voc-profile',
        'force-password-change',
        'account',
        'logout',
        'password-reset',
        'password-reset/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || ! $user->isVocCardHolder()) {
            return $next($request);
        }

        $panel = trim((string) config('filament.panels.portal.path', 'portal'), '/') ?: 'portal';

        // Not a portal page at all (scan pages, downloads) — none of this middleware's business.
        if (! $request->is($panel, $panel.'/*')) {
            return $next($request);
        }

        foreach (self::ALLOWED as $path) {
            if ($request->is($panel.'/'.$path)) {
                return $next($request);
            }
        }

        // Livewire's own endpoint drives whatever page is already open; blocking it would
        // break the allowed pages themselves.
        if ($request->is('livewire/*') || $request->ajax()) {
            return $next($request);
        }

        return redirect()->to(\App\Filament\Portal\Pages\VocDashboard::getUrl(panel: 'portal'));
    }
}
