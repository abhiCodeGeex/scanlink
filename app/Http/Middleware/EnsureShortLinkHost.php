<?php

namespace App\Http\Middleware;

use App\Services\ShortLinkService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets /{code} exist only on the short domain.
 *
 * The route has to be declared on the main router - Route::domain() fixes the host when routes
 * are built, which made the route's existence depend on the environment at boot. Checking the
 * host here instead means the same config drives it at request time, which is both easier to
 * reason about and testable without rebooting the application.
 *
 * It is registered last in web.php, so every real route - /contact, /pricing, /how-to and the
 * rest - is matched first and can never be swallowed by a code-shaped path.
 */
class EnsureShortLinkHost
{
    public function __construct(protected ShortLinkService $shortLinks) {}

    public function handle(Request $request, Closure $next): Response
    {
        $domain = $this->shortLinks->domain();

        if (! $this->shortLinks->enabled() || ! is_string($domain)) {
            abort(404);
        }

        if (strcasecmp($request->getHost(), $domain) !== 0) {
            abort(404);
        }

        return $next($request);
    }
}
