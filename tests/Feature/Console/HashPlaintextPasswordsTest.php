<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * `scanlink:legacy-passwords --hash-plaintext` bcrypts the passwords the conversion carried over
 * from Kohana as plaintext. Nobody has to change anything: the owner keeps the password they
 * know, it is simply no longer readable in the database.
 */
class HashPlaintextPasswordsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_hashes_plaintext_and_leaves_every_other_shape_alone(): void
    {
        $plain = $this->userWithRawPassword('plain@example.com', 'GEorge1971#');
        $md5 = $this->userWithRawPassword('md5@example.com', md5('legacy-pass'));
        $blank = $this->userWithRawPassword('blank@example.com', '');
        $bcrypt = $this->userWithRawPassword('bcrypt@example.com', Hash::make('already-hashed'));

        $this->artisan('scanlink:legacy-passwords', ['--hash-plaintext' => true])
            ->expectsOutputToContain('1 plaintext password(s) hashed.')
            ->assertSuccessful();

        $stored = $this->storedPassword($plain);
        $this->assertStringStartsWith('$2y$', $stored);
        $this->assertTrue(Hash::check('GEorge1971#', $stored), 'the owner keeps the password they know');

        $this->assertSame(md5('legacy-pass'), $this->storedPassword($md5), 'MD5 digests upgrade at sign-in instead');
        $this->assertSame('', $this->storedPassword($blank), 'the must-reset marker stays');
        $this->assertTrue(Hash::check('already-hashed', $this->storedPassword($bcrypt)), 'bcrypt is left as it is');
    }

    #[Test]
    public function a_dry_run_changes_nothing(): void
    {
        $plain = $this->userWithRawPassword('plain@example.com', 'GEorge1971#');

        $this->artisan('scanlink:legacy-passwords', ['--hash-plaintext' => true, '--dry-run' => true])
            ->expectsOutputToContain('[dry-run] 1 plaintext password(s) hashed.')
            ->assertSuccessful();

        $this->assertSame('GEorge1971#', $this->storedPassword($plain));
    }

    /** Write the column raw; the model's `hashed` cast would bcrypt whatever it is given. */
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
}
