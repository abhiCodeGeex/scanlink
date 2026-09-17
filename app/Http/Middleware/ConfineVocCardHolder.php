<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\LoginEntrance;
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
 *
 * Who is fenced follows legacy too. Legacy keyed on the `voc_user_id` session value, which only
 * voclogin ever set, so whoever came through that door saw the cut-down site — even if the
 * same email also had a client account. Deriving it from the account instead let a dual-identity
 * holder in through the VOCC door and straight out into the full portal, so the entrance is now
 * recorded at sign-in (SESSION_KEY) and checked here.
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
        'logout',
        'password-reset',
        'password-reset/*',
    ];

    /**
     * Session flag recording that this session signed in at the VOCC address.
     *
     * The legacy equivalent of `voc_user_id`: set by PortalAuthController::vocLogin(), cleared
     * when a client signs in, and gone with the session on logout.
     */
    public const SESSION_KEY = 'voc_card_holder_login';

    /** Is the current session confined to the card holder's own pages? */
    public static function fences(?User $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        // Decided by LoginEntrance so the recorded door and the fence can never disagree. It
        // still honours SESSION_KEY and still fences a pure card holder with no door on record.
        return LoginEntrance::current($user) === LoginEntrance::VOC;
    }

    /**
     * May a fenced session see this path? Shared with the login controller so the landing page
     * and this middleware cannot disagree about what is reachable.
     */
    public static function allows(string $path): bool
    {
        $path = '/'.ltrim($path, '/');

        foreach (self::ALLOWED as $allowed) {
            if (fnmatch('/'.self::panelPath().'/'.$allowed, $path)) {
                return true;
            }
        }

        return false;
    }

    protected static function panelPath(): string
    {
        return trim((string) config('filament.panels.portal.path', 'portal'), '/') ?: 'portal';
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! self::fences($user)) {
            return $next($request);
        }

        $panel = self::panelPath();

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
