<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Transparent password upgrade for accounts imported from the Kohana site, which stored
 * unsalted MD5 digests. Those cannot be converted to bcrypt — MD5 is one-way, the plaintext
 * is not recoverable — so the digest is kept until the owner next signs in successfully,
 * and is replaced right then with a bcrypt hash of the password they just proved they know.
 *
 * The user types their usual password and notices nothing; each account upgrades itself on
 * first login. `scanlink:legacy-passwords` counts what is left; once it reaches zero
 * the `users` provider in config/auth.php can go back to the stock `eloquent` driver.
 */
class LegacyPasswordUserProvider extends EloquentUserProvider
{
    /**
     * A bare 32-character hex string — the shape Kohana's `md5($password)` left behind.
     * Nothing bcrypt produces can collide with this ($2y$… is neither 32 chars nor hex).
     */
    public static function isLegacyMd5(?string $value): bool
    {
        return is_string($value) && preg_match('/^[a-f0-9]{32}$/i', $value) === 1;
    }

    public function validateCredentials(UserContract $user, #[\SensitiveParameter] array $credentials): bool
    {
        $plain = $credentials['password'] ?? null;
        $stored = $user->getAuthPassword();

        // A blank password is the "must reset" marker written by scanlink:sync-all-users for
        // accounts with no reusable credential. It must never authenticate.
        if (! is_string($plain) || $plain === '' || ! is_string($stored) || $stored === '') {
            return false;
        }

        if (self::isBcrypt($stored)) {
            return parent::validateCredentials($user, $credentials);
        }

        $matches = self::isLegacyMd5($stored)
            ? hash_equals(strtolower($stored), md5($plain))
            // Kohana kept some passwords as plaintext, and the conversion carried them over as
            // they were. Asking the bcrypt hasher about one throws ("This password does not use
            // the Bcrypt algorithm"), which is a 500 at the login instead of a sign-in — the
            // admin login was failing on exactly this. Compare it here and upgrade it, the same
            // way an MD5 digest is handled.
            : hash_equals($stored, $plain);

        if (! $matches) {
            return false;
        }

        $this->upgradeToBcrypt($user, $plain);

        return true;
    }

    /** A bcrypt hash — the only shape the stock hasher will look at without throwing. */
    public static function isBcrypt(?string $value): bool
    {
        return is_string($value) && preg_match('/^\$2[aby]\$/', $value) === 1;
    }

    /**
     * The stock implementation asks the hasher whether the stored value needs rehashing, but
     * a legacy digest is not a bcrypt hash at all and `needsRehash()` on it is meaningless.
     * validateCredentials() has already replaced it by this point, so there is nothing to do.
     */
    public function rehashPasswordIfRequired(UserContract $user, #[\SensitiveParameter] array $credentials, bool $force = false): void
    {
        if (! self::isBcrypt($user->getAuthPassword())) {
            return;
        }

        parent::rehashPasswordIfRequired($user, $credentials, $force);
    }

    /**
     * Persist the proven plaintext as bcrypt. The model's `hashed` cast does the hashing, so
     * the digest never reaches the database.
     */
    private function upgradeToBcrypt(UserContract $user, #[\SensitiveParameter] string $plain): void
    {
        if (! $user instanceof Model) {
            return;
        }

        $user->forceFill([$user->getAuthPasswordName() => $plain])->save();
    }
}
