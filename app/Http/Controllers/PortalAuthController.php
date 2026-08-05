<?php

namespace App\Http\Controllers;

use App\Filament\Portal\Pages\EditAccount;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PortalAuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();
        $panel = Filament::getPanel('portal');

        if (! $user->canAccessPanel($panel)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This account cannot access the client portal.',
            ]);
        }

        Filament::setCurrentPanel($panel);

        // Always land in the portal. Ignore stale "intended" URLs from admin
        // (or other panels) — those cause 403 for portal-only users.
        $fallback = EditAccount::getUrl(panel: 'portal');
        $intended = $request->session()->pull('url.intended');

        if (is_string($intended) && $intended !== '') {
            $path = parse_url($intended, PHP_URL_PATH) ?: $intended;

            if (str_starts_with($path, '/portal')) {
                return redirect()->to($intended);
            }
        }

        return redirect()->to($fallback);
    }
}
