<?php

namespace App\Support;

use App\Enums\UserType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocUser;
use App\Services\VocUserProvisioner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * The VOCC card holder login, checked against the card's own password.
 *
 * Legacy /voclogin looked only at `voc_users`: email + that card's password, and the session
 * then held the cards that matched (profile_id_array). The rebuild checked the person's `users`
 * password instead and kept the card password in step with it, which is how a client's login
 * came to be overwritten by their card. Now:
 *
 *  - the card password is checked here and nowhere else;
 *  - the person's `users` row is only the session carrier, its password is never used or changed;
 *  - the session remembers which cards were unlocked, and a card holder sees only those.
 */
final class VocCardLogin
{
    /** voc_user_id values unlocked at sign-in — legacy's profile_id_array. */
    public const SESSION_KEY = 'voc_card_ids';

    /**
     * Check a VOCC sign-in. Returns the carrier login and the cards the password unlocked, or
     * null when no card on that email has this password.
     *
     * @return array{0: User, 1: list<int>}|null
     */
    public static function attempt(string $email, string $password): ?array
    {
        $email = self::normalizeEmail($email);

        if ($email === null || $password === '') {
            return null;
        }

        $cards = VocUser::query()->whereRaw('LOWER(TRIM(email)) = ?', [$email])->get();

        if ($cards->isEmpty()) {
            return null;
        }

        $matched = $cards->filter(fn (VocUser $card): bool => $card->checkCardPassword($password));

        if ($matched->isEmpty()) {
            $matched = self::adoptLoginPassword($cards, $email, $password);
        }

        if ($matched->isEmpty()) {
            return null;
        }

        $provisioner = app(VocUserProvisioner::class);
        $user = $provisioner->identityFor($matched->first());

        // One email is one login, so every matched card resolves to the same carrier; link any
        // card that was not pointed at it yet.
        $matched->each(fn (VocUser $card) => $provisioner->identityFor($card));

        return [$user, $matched->map(fn (VocUser $card): int => (int) $card->voc_user_id)->values()->all()];
    }

    /**
     * Record the unlocked cards for this session. Call after the session is regenerated.
     *
     * @param  list<int>  $cardIds
     */
    public static function remember(array $cardIds): void
    {
        session()->put(self::SESSION_KEY, array_values(array_map('intval', $cardIds)));
    }

    /** Does any card on this email still have no password of its own? */
    public static function hasCardWithoutPassword(string $email): bool
    {
        $email = self::normalizeEmail($email);

        return $email !== null && VocUser::query()
            ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
            ->where(fn ($query) => $query->whereNull('password')->orWhere('password', ''))
            ->exists();
    }

    /**
     * Profiles this card holder may see. In a VOCC session, the cards unlocked at sign-in; with
     * no record of that (a session from before this change), every card linked to the login.
     *
     * @return Collection<int, int>
     */
    public static function profileIdsFor(User $user): Collection
    {
        $query = VocUser::query()->where('auth_user_id', $user->id)->whereNotNull('profile_id');

        if (request()->hasSession() && session()->has(self::SESSION_KEY)) {
            $query->whereIn('voc_user_id', (array) session(self::SESSION_KEY));
        }

        return $query->pluck('profile_id')->map(fn ($id): int => (int) $id)->unique()->values();
    }

    /**
     * May this session act as the card holder of this profile? Only a VOCC session can; a client
     * session works on the profile through client membership instead.
     */
    public static function unlocks(User $user, Profile $profile): bool
    {
        return LoginEntrance::current($user) === LoginEntrance::VOC
            && self::profileIdsFor($user)->contains((int) $profile->id);
    }

    public static function forget(): void
    {
        if (request()->hasSession()) {
            session()->forget(self::SESSION_KEY);
        }
    }

    /**
     * Cards imported before they had a password of their own. A pure card holder's login password
     * was the card password (the old sync copied it across), so accept it once and give those
     * cards their own copy. Never for a login that is also a client member or an admin: their
     * login password is not the card's, which is the whole point.
     *
     * @param  Collection<int, VocUser>  $cards
     * @return Collection<int, VocUser>
     */
    private static function adoptLoginPassword(Collection $cards, string $email, string $password): Collection
    {
        $blank = $cards->reject(fn (VocUser $card): bool => $card->hasCardPassword());

        if ($blank->count() !== $cards->count()) {
            return collect();
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user instanceof User
            || $user->user_type !== UserType::Voc
            || $user->holdsAdminRole()
            || $user->hasActiveClientMembership()
            || ! Auth::guard('web')->getProvider()->validateCredentials($user, ['password' => $password])) {
            return collect();
        }

        $hash = Hash::make($password);

        VocUser::query()
            ->whereIn('voc_user_id', $blank->pluck('voc_user_id'))
            ->update(['password' => $hash]);

        return $blank->each(fn (VocUser $card) => $card->setRawAttributes(
            array_merge($card->getAttributes(), ['password' => $hash]),
            true,
        ));
    }

    private static function normalizeEmail(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return $email !== '' && str_contains($email, '@') ? $email : null;
    }
}
