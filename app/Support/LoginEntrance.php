<?php

namespace App\Support;

use App\Enums\UserType;
use App\Http\Middleware\ConfineVocCardHolder;
use App\Models\User;

/**
 * Which login door this session came through — and so which role the person is acting in.
 *
 * One person can hold several roles under a single login: a ScanLink admin who is also a VOCC
 * card holder, a client member who also holds a card. Legacy kept a credential store per role,
 * so the door you used *was* the role. The rebuild merged them into one `users` row with one
 * `user_type`, which can only name one role and so locked multi-role people out of every door
 * but one. The door is recorded at sign-in instead, and each area asks for its own door plus
 * the role that door needs:
 *
 *  - ADMIN  → the admin panel, for someone holding an admin role;
 *  - CLIENT → the client portal, for someone with an active client membership;
 *  - VOC    → the card holder's own card, for someone a voc_users row points at.
 */
final class LoginEntrance
{
    public const SESSION_KEY = 'login_entrance';

    public const ADMIN = 'admin';

    public const CLIENT = 'client';

    public const VOC = 'voc';

    public static function record(string $door): void
    {
        session()->put(self::SESSION_KEY, $door);

        // The VOC fence predates this and still reads its own flag; keep the two in step so a
        // session can never be recorded as one door and fenced as another.
        if ($door === self::VOC) {
            session()->put(ConfineVocCardHolder::SESSION_KEY, true);
        } else {
            session()->forget(ConfineVocCardHolder::SESSION_KEY);
        }
    }

    public static function current(?User $user): ?string
    {
        if (! $user instanceof User) {
            return null;
        }

        if (self::hasSession()) {
            $door = session(self::SESSION_KEY);

            if (in_array($door, [self::ADMIN, self::CLIENT, self::VOC], true)) {
                return $door;
            }

            if (session(ConfineVocCardHolder::SESSION_KEY) === true) {
                return self::VOC;
            }
        }

        // No door on record: a session opened before this was introduced, a "remember me"
        // restore, or a test acting as a user. Fall back to the role the account leads with —
        // exactly how access was decided before, so nobody is logged out by the change.
        return match (true) {
            $user->user_type === UserType::Admin => self::ADMIN,
            $user->user_type === UserType::Voc && ! $user->hasActiveClientMembership() => self::VOC,
            default => self::CLIENT,
        };
    }

    /** Came in at the client or VOCC door — i.e. is using the portal panel at all. */
    public static function isPortalSession(?User $user): bool
    {
        return in_array(self::current($user), [self::CLIENT, self::VOC], true);
    }

    private static function hasSession(): bool
    {
        $request = request();

        return $request->hasSession();
    }
}
