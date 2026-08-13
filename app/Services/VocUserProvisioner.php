<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use App\Models\VocUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Legacy parity: a VOCC "Additional User Access Login" (voc_users row) must be able to
 * sign in the moment it is added. Laravel authenticates against the unified `users` table
 * (user_type = Voc, linked via voc_users.auth_user_id). The batch `scanlink:sync-all-users`
 * command only runs after a data import, so this provisions the same identity per-row, in
 * real time — mirroring SyncAllUsers::syncVocUsers.
 */
class VocUserProvisioner
{
    public function provision(VocUser $vocUser): void
    {
        $email = $this->normalizeEmail($vocUser->email);

        if (! $email) {
            return;
        }

        $existing = User::query()->where('email', $email)->first();

        // The email already logs in via another source (admin or client member): link the
        // voc row to it but never change its type/password — the person keeps their identity.
        if ($existing && $existing->user_type !== UserType::Voc) {
            $this->link($vocUser, (int) $existing->id);

            return;
        }

        // New identity, or an existing voc-owned one: (re)provision type + password so an
        // edited voc password re-syncs to the login.
        $user = $existing ?? new User(['email' => $email]);
        $user->name = $user->name ?: $email;
        $user->user_type = UserType::Voc;

        $raw = (string) ($vocUser->password ?? '');

        if (filled($raw) && $this->isBcrypt($raw)) {
            // Already-hashed legacy value: store verbatim (bypass the hashing cast).
            $user->save();
            DB::table('users')->where('id', $user->id)->update(['password' => $raw]);
            $this->link($vocUser, (int) $user->id);

            return;
        }

        if (filled($raw)) {
            // Editor-entered plaintext → hashed by the User `password` cast on save.
            $user->password = $raw;
        } elseif (! $user->exists) {
            // Brand-new identity with no usable password yet.
            $user->password = Str::random(24);
        }
        // Blank password on an existing identity → leave the current login password untouched.

        $user->save();
        $this->link($vocUser, (int) $user->id);
    }

    protected function link(VocUser $vocUser, int $userId): void
    {
        if ((int) $vocUser->auth_user_id === $userId) {
            return;
        }

        // Raw update so this does not re-fire the VocUser observer.
        DB::table('voc_users')
            ->where('voc_user_id', $vocUser->voc_user_id)
            ->update(['auth_user_id' => $userId]);

        $vocUser->setAttribute('auth_user_id', $userId);
        $vocUser->syncOriginalAttribute('auth_user_id');
    }

    protected function normalizeEmail(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return filled($email) && str_contains($email, '@') ? $email : null;
    }

    protected function isBcrypt(?string $value): bool
    {
        return filled($value) && Str::startsWith($value, ['$2y$', '$2a$', '$2b$']);
    }
}
