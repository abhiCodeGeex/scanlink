<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Accounts imported from the Kohana site carry unsalted MD5 digests, which cannot be
 * converted to bcrypt because the plaintext is unrecoverable. LegacyPasswordUserProvider
 * accepts one at sign-in and replaces it with bcrypt in the same request.
 */
class LegacyPasswordUpgradeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Write the password column raw: the model's `hashed` cast would otherwise bcrypt the
     * digest, which is exactly what scanlink:sync-all-users avoids via DB::table().
     */
    private function userWithRawPassword(string $email, string $raw): User
    {
        $user = User::factory()->create(['email' => $email]);

        DB::table('users')->where('id', $user->id)->update(['password' => $raw]);

        return $user->refresh();
    }

    private function storedPassword(User $user): string
    {
        return (string) DB::table('users')->where('id', $user->id)->value('password');
    }

    public function test_md5_password_authenticates_and_is_replaced_with_bcrypt(): void
    {
        $user = $this->userWithRawPassword('legacy@example.com', md5('correct-horse'));

        $this->assertTrue(Auth::attempt([
            'email' => 'legacy@example.com',
            'password' => 'correct-horse',
        ]));

        $stored = $this->storedPassword($user);

        $this->assertStringStartsWith('$2y$', $stored, 'digest should have been replaced with bcrypt');
        $this->assertTrue(Hash::check('correct-horse', $stored));
    }

    public function test_upgraded_account_still_authenticates_on_the_next_attempt(): void
    {
        $this->userWithRawPassword('again@example.com', md5('second-time'));

        $this->assertTrue(Auth::attempt(['email' => 'again@example.com', 'password' => 'second-time']));
        Auth::logout();

        // Now a normal bcrypt credential — it must keep working, not just the first time.
        $this->assertTrue(Auth::attempt(['email' => 'again@example.com', 'password' => 'second-time']));
    }

    public function test_uppercase_md5_digest_is_accepted(): void
    {
        $user = $this->userWithRawPassword('upper@example.com', strtoupper(md5('shouty')));

        $this->assertTrue(Auth::attempt(['email' => 'upper@example.com', 'password' => 'shouty']));
        $this->assertStringStartsWith('$2y$', $this->storedPassword($user));
    }

    public function test_wrong_password_fails_and_leaves_the_digest_untouched(): void
    {
        $digest = md5('correct-horse');
        $user = $this->userWithRawPassword('wrong@example.com', $digest);

        $this->assertFalse(Auth::attempt([
            'email' => 'wrong@example.com',
            'password' => 'not-the-password',
        ]));

        $this->assertSame($digest, $this->storedPassword($user), 'a failed attempt must not rewrite the password');
    }

    public function test_blank_password_marker_never_authenticates(): void
    {
        // scanlink:sync-all-users writes '' for accounts with no reusable credential.
        $this->userWithRawPassword('blank@example.com', '');

        $this->assertFalse(Auth::attempt(['email' => 'blank@example.com', 'password' => '']));
        $this->assertFalse(Auth::attempt(['email' => 'blank@example.com', 'password' => 'anything']));
        $this->assertFalse(Auth::attempt(['email' => 'blank@example.com', 'password' => md5('')]));
    }

    public function test_bcrypt_accounts_are_unaffected(): void
    {
        $user = User::factory()->create([
            'email' => 'modern@example.com',
            'password' => 'plain-password',
        ]);

        $before = $this->storedPassword($user);

        $this->assertTrue(Auth::attempt(['email' => 'modern@example.com', 'password' => 'plain-password']));
        $this->assertFalse(Auth::attempt(['email' => 'modern@example.com', 'password' => 'nope']));
        $this->assertSame($before, $this->storedPassword($user));
    }

    /**
     * A 32-char hex string is the one shape we treat as legacy; guard against a bcrypt hash
     * or anything else being mistaken for one.
     */
    public function test_only_32_char_hex_is_treated_as_legacy(): void
    {
        $provider = \App\Auth\LegacyPasswordUserProvider::class;

        $this->assertTrue($provider::isLegacyMd5(md5('x')));
        $this->assertTrue($provider::isLegacyMd5(strtoupper(md5('x'))));
        $this->assertFalse($provider::isLegacyMd5(Hash::make('x')));
        $this->assertFalse($provider::isLegacyMd5(''));
        $this->assertFalse($provider::isLegacyMd5(null));
        $this->assertFalse($provider::isLegacyMd5(str_repeat('a', 31)));
        $this->assertFalse($provider::isLegacyMd5(str_repeat('z', 32)));
    }
}
