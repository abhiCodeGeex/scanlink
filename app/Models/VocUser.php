<?php

namespace App\Models;

use App\Observers\VocUserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * A VOCC "Additional User Access Login": one card, one email, and that card's own password.
 *
 * Legacy /voclogin checked this table alone, so the same email could be a client with one
 * password and a card holder with another. The password lives here again (hashed) and is never
 * copied to, or taken from, the person's `users` login — see App\Support\VocCardLogin.
 */
#[ObservedBy([VocUserObserver::class])]
class VocUser extends Model
{
    protected $table = 'voc_users';

    protected $primaryKey = 'voc_user_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['voc_user_id', 'profile_id', 'email', 'password', 'auth_user_id'];

    protected $hidden = ['password'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function authUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auth_user_id');
    }

    /**
     * Plaintext from the profile editor is hashed on the way in. A value that is already a hash
     * (bcrypt, or a legacy MD5 digest) is stored as it is, so imports never double-hash. Blank
     * stays blank: the "no card password yet" marker.
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: function (?string $value): string {
                $value = (string) ($value ?? '');

                if ($value === '' || self::isBcrypt($value) || self::isMd5($value)) {
                    return $value;
                }

                return Hash::make($value);
            },
        );
    }

    public function hasCardPassword(): bool
    {
        return filled($this->getRawOriginal('password') ?? $this->getAttributes()['password'] ?? null);
    }

    /**
     * Does this card's own password match? Legacy stored plaintext and some imports carry MD5;
     * a match on either is upgraded to bcrypt in place, like LegacyPasswordUserProvider does for
     * `users`.
     */
    public function checkCardPassword(string $plain): bool
    {
        $stored = (string) ($this->getAttributes()['password'] ?? '');

        if ($stored === '' || $plain === '') {
            return false;
        }

        if (self::isBcrypt($stored)) {
            return Hash::check($plain, $stored);
        }

        $matches = self::isMd5($stored)
            ? hash_equals(strtolower($stored), md5($plain))
            : hash_equals($stored, $plain);

        if ($matches) {
            static::query()
                ->whereKey($this->getKey())
                ->update(['password' => Hash::make($plain)]);
        }

        return $matches;
    }

    public static function isBcrypt(?string $value): bool
    {
        return filled($value) && Str::startsWith($value, ['$2y$', '$2a$', '$2b$']);
    }

    public static function isMd5(?string $value): bool
    {
        return filled($value) && (bool) preg_match('/^[a-f0-9]{32}$/i', $value);
    }
}
