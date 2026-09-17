<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use App\Models\VocUser;
use Illuminate\Support\Str;

/**
 * Points a VOCC card (voc_users row) at the `users` login that carries the card holder's session.
 *
 * The card keeps its own password in voc_users and /voclogin checks that password alone (see
 * App\Support\VocCardLogin). This class therefore never writes a password or a user_type onto
 * an existing login. It used to: a login labelled `voc` had its password replaced by the card's
 * every time the card was saved, which is how a client member — relabelled `voc` by the old sync —
 * had their client login overwritten by their card.
 *
 * An email with no login yet gets one, typed `voc`, with a random password nobody knows. It exists
 * only so the portal has someone to sign in as; the card password is what opens it.
 */
class VocUserProvisioner
{
    public function provision(VocUser $vocUser): void
    {
        $this->identityFor($vocUser);
    }

    /** The login this card signs in as, created if the email has none, and linked to the card. */
    public function identityFor(VocUser $vocUser): ?User
    {
        $email = $this->normalizeEmail($vocUser->email);

        if (! $email) {
            return null;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = new User(['email' => $email]);
            $user->name = $email;
            $user->user_type = UserType::Voc;
            $user->password = Str::random(40);
            $user->save();
        }

        $this->link($vocUser, (int) $user->id);

        return $user;
    }

    protected function link(VocUser $vocUser, int $userId): void
    {
        if ((int) $vocUser->auth_user_id === $userId) {
            return;
        }

        // Query-level write: no model events, so linking never re-enters the observer.
        VocUser::query()
            ->whereKey($vocUser->getKey())
            ->update(['auth_user_id' => $userId]);

        $vocUser->setAttribute('auth_user_id', $userId);
        $vocUser->syncOriginalAttribute('auth_user_id');
    }

    protected function normalizeEmail(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return $email !== '' && str_contains($email, '@') ? $email : null;
    }
}
