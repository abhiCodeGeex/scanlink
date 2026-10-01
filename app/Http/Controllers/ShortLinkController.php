<?php

namespace App\Http\Controllers;

use App\Services\ProfileQrService;
use App\Services\ShortLinkService;
use Illuminate\Http\RedirectResponse;

/**
 * Resolves a scanned short code to the page it stands for.
 *
 * Registered against the short domain alone, which is what lets a bare /{code} exist at all:
 * on the main site it would collide with /contact, /pricing and the rest.
 */
class ShortLinkController extends Controller
{
    public function __invoke(string $code, ShortLinkService $shortLinks, ProfileQrService $qr): RedirectResponse
    {
        $profile = $shortLinks->resolve($code);

        if (! $profile) {
            // A printed label that 404s looks broken; one that lands on the home page looks
            // like an old code. Send them somewhere useful.
            return redirect()->away(rtrim((string) config('scanlink.portal_url'), '/').'/');
        }

        // 302, not 301: a permanent redirect is cached by browsers more or less forever, so a
        // profile that later moves would never be re-asked about. The scan page records the
        // hit, so this stays a plain redirect and nothing is counted twice.
        return redirect()
            ->away($shortLinks->destinationFor($profile, $qr), 302)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
