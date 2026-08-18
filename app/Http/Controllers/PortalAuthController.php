<?php

namespace App\Http\Controllers;

use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\User;
use Filament\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
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
            // Accounts migrated from the legacy site whose password could not be carried
            // over (stored as one-way MD5) have a BLANK password marker. Rather than a
            // generic error, email them the same reset link as "forgot password" and show
            // an "expired password" notice so they aren't left stuck at the new login.
            $email = strtolower(trim((string) $request->input('email')));
            $user = User::query()->where('email', $email)->first();

            if ($user && blank($user->getRawOriginal('password'))) {
                $this->sendExpiredPasswordReset($user);

                return back()->with('password_expired_email', $user->email);
            }

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

        // Always land in the portal on the Master Code List (profiles). Ignore stale
        // "intended" URLs from admin (or other panels) — those cause 403 for portal-only
        // users — and treat a bare "/portal" as "no intent" so it also lands on profiles.
        $fallback = ProfileResource::getUrl('index', panel: 'portal');
        $intended = $request->session()->pull('url.intended');

        if (is_string($intended) && $intended !== '') {
            $path = parse_url($intended, PHP_URL_PATH) ?: $intended;

            if (str_starts_with($path, '/portal/')) {
                return redirect()->to($intended);
            }
        }

        return redirect()->to($fallback);
    }

    /**
     * Email the standard portal password-reset link (identical to "forgot password"),
     * with the reset URL pointing at the portal reset page.
     */
    private function sendExpiredPasswordReset(User $user): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
            ['email' => $user->email],
            function (CanResetPassword $notifiable, string $token): void {
                $notification = app(ResetPasswordNotification::class, ['token' => $token]);
                $notification->url = Filament::getResetPasswordUrl($token, $notifiable);
                $notifiable->notify($notification);
            },
        );
    }
}
