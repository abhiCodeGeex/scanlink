<?php

namespace App\Http\Controllers;

use App\Filament\Portal\Pages\VocDashboard;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Http\Middleware\ConfineVocCardHolder;
use App\Models\User;
use App\Support\LoginEntrance;
use App\Support\VocCardLogin;
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
    /**
     * Client / sub-client login (POST /portal-login) - the home page modal.
     *
     * Requires standing in a client. Legacy authenticated this door against `client_users`, so
     * an "Additional User Access Login" could not use it at all; an email present in both
     * tables could use either door, and that still holds.
     */
    public function login(Request $request): RedirectResponse
    {
        return $this->authenticate($request, cardHolderEntrance: false);
    }

    /**
     * VOCC card holder login (POST /voclogin).
     *
     * The address printed on the cards and handed out at induction. Same credential store as
     * the client login, but its own URL, and only accounts that were actually issued card
     * holder access get through - the two audiences no longer share one door.
     */
    public function vocLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Legacy checked voc_users alone: the card's own password, never the person's client or
        // admin password. Checking the login password here is what let a card overwrite a client.
        $result = VocCardLogin::attempt((string) $credentials['email'], (string) $credentials['password']);

        if ($result === null) {
            throw ValidationException::withMessages([
                'email' => VocCardLogin::hasCardWithoutPassword((string) $credentials['email'])
                    ? 'This card login has no password yet. Use "Forgot your password?" to set one.'
                    : 'These credentials do not match our records.',
            ]);
        }

        [$user, $cardIds] = $result;

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        LoginEntrance::record(LoginEntrance::VOC);
        VocCardLogin::remember($cardIds);

        Filament::setCurrentPanel(Filament::getPanel('portal'));

        return redirect()->to(VocDashboard::getUrl(panel: 'portal'));
    }

    /**
     * @param  bool  $cardHolderEntrance  Always false now: the VOCC door checks the card password
     *                                    in vocLogin() and never reaches the login password.
     */
    protected function authenticate(Request $request, bool $cardHolderEntrance): RedirectResponse
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

            // Someone who typed a valid card password at the client login: point them to the VOCC
            // address. Only when the card password matched, so this never reveals who holds a card.
            if (! $cardHolderEntrance
                && VocCardLogin::attempt($email, (string) $request->input('password')) !== null) {
                throw ValidationException::withMessages([
                    'email' => 'This account signs in at the VOCC card holder login: '.url('/voclogin'),
                ]);
            }

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

        // Each door asks only whether the person holds the role it serves. The same login may
        // hold several — an admin who is also a card holder — so nothing is judged from the
        // account's single user_type before the door is known; that is what used to turn an
        // admin away from /voclogin despite a valid card.
        if ($cardHolderEntrance && ! $user->hasVocCardHolderAccess()) {
            $this->abandonSession($request);

            throw ValidationException::withMessages([
                'email' => 'This is the VOCC card holder login. Please use the main login instead.',
            ]);
        }

        // A card holder account has no client standing, so it cannot come in here - the rule
        // that used to hold because legacy kept the two in separate tables.
        if (! $cardHolderEntrance && ! $user->hasActiveClientMembership()) {
            $this->abandonSession($request);

            throw ValidationException::withMessages([
                'email' => $user->hasVocCardHolderAccess()
                    ? 'This account signs in at the VOCC card holder login: '.url('/voclogin')
                    : 'This account cannot access the client portal.',
            ]);
        }

        // Record which door was used, the way legacy's voc_user_id session value did. It decides
        // the role for the rest of the session, and replaces whatever a previous login recorded.
        LoginEntrance::record($cardHolderEntrance ? LoginEntrance::VOC : LoginEntrance::CLIENT);

        if (! $user->canAccessPanel($panel)) {
            $this->abandonSession($request);

            throw ValidationException::withMessages([
                'email' => 'This account cannot access the client portal.',
            ]);
        }

        Filament::setCurrentPanel($panel);

        // Always land in the portal on the Master Code List (profiles). Ignore stale
        // "intended" URLs from admin (or other panels) — those cause 403 for portal-only
        // users — and treat a bare "/portal" as "no intent" so it also lands on profiles.
        //
        // A fenced session sees only the card, so profiles is not theirs: land them straight on
        // the VOC dashboard rather than leaning on ConfineVocCardHolder to bounce them there on
        // the next request. Coming through the VOCC door is enough to fence the session, even
        // for someone who also has a client account.
        $fenced = ConfineVocCardHolder::fences($user);

        $fallback = $fenced
            ? VocDashboard::getUrl(panel: 'portal')
            : ProfileResource::getUrl('index', panel: 'portal');

        $intended = $request->session()->pull('url.intended');

        if (is_string($intended) && $intended !== '') {
            $path = parse_url($intended, PHP_URL_PATH) ?: $intended;

            if (str_starts_with($path, '/portal/')) {
                // Honouring a page the fence would only bounce is a redirect loop dressed up as
                // a courtesy; ask the middleware rather than guessing at one path.
                if ($fenced && ! ConfineVocCardHolder::allows($path)) {
                    return redirect()->to($fallback);
                }

                return redirect()->to($intended);
            }
        }

        return redirect()->to($fallback);
    }

    /** Drop a session that authenticated but is not allowed through this entrance. */
    protected function abandonSession(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
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
