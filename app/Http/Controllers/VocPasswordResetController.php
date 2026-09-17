<?php

namespace App\Http\Controllers;

use App\Mail\ScanlinkMail;
use App\Models\VocUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

/**
 * "Forgot your password?" at the VOCC card holder login.
 *
 * It resets the card's own password in voc_users — never the person's `users` login, which may be
 * their client or admin account. Legacy voclogin/forgot emailed the stored plaintext password;
 * cards are hashed now, so the email carries a signed, one-hour link to set a new one instead.
 */
class VocPasswordResetController extends Controller
{
    public function request(): View
    {
        return view('marketing.voclogin-forgot');
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $email = strtolower(trim((string) $data['email']));

        if ($this->cards($email)->isNotEmpty()) {
            $url = URL::temporarySignedRoute('marketing.voclogin.reset', now()->addHour(), [
                'email' => $email,
                'v' => $this->fingerprint($email),
            ]);

            try {
                Mail::to($email)->send(new ScanlinkMail(
                    'Reset your ScanLink VOCC card password',
                    'emails.voc.card-password-reset',
                    ['resetUrl' => $url],
                ));
            } catch (\Throwable $e) {
                Log::warning('VOCC card password reset email failed', ['email' => $email, 'error' => $e->getMessage()]);
            }
        }

        // Same answer either way, so the form cannot be used to find out which emails hold a card.
        return back()->with('status', 'If that email has VOCC card holder access, we have sent a link to set a new password.');
    }

    public function edit(Request $request): View|RedirectResponse
    {
        if (! $this->linkIsCurrent($request)) {
            return redirect()->route('marketing.voclogin.forgot')
                ->withErrors(['email' => 'That reset link has expired or was already used. Please request a new one.']);
        }

        return view('marketing.voclogin-reset', ['email' => (string) $request->query('email')]);
    }

    public function update(Request $request): RedirectResponse
    {
        if (! $this->linkIsCurrent($request)) {
            return redirect()->route('marketing.voclogin.forgot')
                ->withErrors(['email' => 'That reset link has expired or was already used. Please request a new one.']);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:255', 'confirmed'],
        ]);

        $email = strtolower(trim((string) $request->query('email')));

        // Every card on this email: legacy signed a card holder in to all cards sharing the email
        // and password, so they keep moving together.
        VocUser::query()
            ->whereIn('voc_user_id', $this->cards($email)->pluck('voc_user_id'))
            ->update(['password' => Hash::make((string) $data['password'])]);

        return redirect()->route('marketing.voclogin')
            ->with('status', 'Your VOCC card password has been updated. Please log in.');
    }

    /** @return \Illuminate\Support\Collection<int, VocUser> */
    private function cards(string $email)
    {
        return VocUser::query()->whereRaw('LOWER(TRIM(email)) = ?', [$email])->get();
    }

    /** Changes once the password changes, so a reset link works only once. */
    private function fingerprint(string $email): string
    {
        $stored = $this->cards($email)
            ->sortBy('voc_user_id')
            ->map(fn (VocUser $card): string => (string) ($card->getAttributes()['password'] ?? ''))
            ->implode('|');

        return substr(hash_hmac('sha256', $email.'#'.$stored, (string) config('app.key')), 0, 32);
    }

    private function linkIsCurrent(Request $request): bool
    {
        $email = strtolower(trim((string) $request->query('email')));

        return $request->hasValidSignature()
            && $email !== ''
            && $this->cards($email)->isNotEmpty()
            && hash_equals($this->fingerprint($email), (string) $request->query('v'));
    }
}
