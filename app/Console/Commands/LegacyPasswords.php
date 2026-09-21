<?php

namespace App\Console\Commands;

use App\Auth\LegacyPasswordUserProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Reports how many accounts still hold a legacy MD5 digest, and can re-import digests from
 * the live Kohana DB for accounts an earlier sync blanked.
 *
 * Context: scanlink:sync-all-users used to overwrite MD5 digests with '' (a "must reset"
 * marker). It now keeps them, because App\Auth\LegacyPasswordUserProvider verifies a digest
 * at sign-in and swaps it for bcrypt in the same request. Accounts blanked before that change
 * need --restore-from-live once; after that this command is only a progress report.
 */
class LegacyPasswords extends Command
{
    protected $signature = 'scanlink:legacy-passwords
                            {--restore-from-live : Re-import MD5 digests from the live DB for accounts whose password is blank}
                            {--hash-plaintext : Bcrypt any password still stored as plaintext}
                            {--dry-run : With --restore-from-live or --hash-plaintext, report what would change without writing}';

    protected $description = 'Report legacy MD5 password accounts, and optionally restore digests blanked by an earlier sync';

    public function handle(): int
    {
        $this->report();

        if ($this->option('hash-plaintext')) {
            return $this->hashPlaintext();
        }

        if (! $this->option('restore-from-live')) {
            return self::SUCCESS;
        }

        return $this->restoreFromLive();
    }

    private function report(): void
    {
        $rows = DB::table('users')->select('id', 'password')->get();

        $bcrypt = $md5 = $blank = $other = 0;

        foreach ($rows as $row) {
            $value = (string) ($row->password ?? '');

            match (true) {
                $value === '' => $blank++,
                str_starts_with($value, '$2y$') || str_starts_with($value, '$2a$') || str_starts_with($value, '$2b$') => $bcrypt++,
                LegacyPasswordUserProvider::isLegacyMd5($value) => $md5++,
                default => $other++,
            };
        }

        $this->table(
            ['shape', 'accounts', 'can sign in?'],
            [
                ['bcrypt', $bcrypt, 'yes'],
                ['legacy MD5', $md5, 'yes — upgrades to bcrypt on next sign-in'],
                ['blank (must reset)', $blank, 'no — reset link on attempt'],
                ['plaintext', $other, 'yes — upgrades to bcrypt on next sign-in; run --hash-plaintext'],
            ],
        );
    }

    /**
     * Bcrypt the passwords the conversion carried over as plaintext. Nobody has to change
     * anything: the owner keeps the password they know, it is simply stored hashed from now on.
     */
    private function hashPlaintext(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $hashed = 0;

        DB::table('users')->select('id', 'password')->orderBy('id')->chunk(500, function ($chunk) use ($dryRun, &$hashed): void {
            foreach ($chunk as $row) {
                $value = (string) ($row->password ?? '');

                if ($value === ''
                    || LegacyPasswordUserProvider::isBcrypt($value)
                    || LegacyPasswordUserProvider::isLegacyMd5($value)) {
                    continue;
                }

                if (! $dryRun) {
                    DB::table('users')->where('id', $row->id)->update(['password' => Hash::make($value)]);
                }

                $hashed++;
            }
        });

        $this->newLine();
        $this->info(($dryRun ? '[dry-run] ' : '').$hashed.' plaintext password(s) hashed.');

        return self::SUCCESS;
    }

    private function restoreFromLive(): int
    {
        $live = config('scanlink.live_db');

        if (blank($live['host'] ?? null) || blank($live['username'] ?? null) || blank($live['password'] ?? null)) {
            $this->error('Missing live DB credentials — set LIVE_DB_* in .env (single-quote the password), then run `php artisan config:clear`.');

            return self::FAILURE;
        }

        config(['database.connections.scanlink_live' => [
            'driver' => 'mysql',
            'host' => $live['host'],
            'port' => $live['port'],
            'database' => $live['database'],
            'username' => $live['username'],
            'password' => $live['password'],
            'charset' => 'utf8mb4',
            'prefix' => '',
            'strict' => false,
        ]]);

        // Only accounts with no reusable credential are eligible; a bcrypt hash here means the
        // owner has already set a password since the import and must never be overwritten.
        $blankEmails = DB::table('users')
            ->where('password', '')
            ->pluck('id', 'email');

        if ($blankEmails->isEmpty()) {
            $this->info('No blank-password accounts — nothing to restore.');

            return self::SUCCESS;
        }

        $this->info('Reading digests from live '.$live['database'].'@'.$live['host'].' ...');

        $restored = 0;
        $skipped = 0;
        $dryRun = (bool) $this->option('dry-run');

        DB::connection('scanlink_live')
            ->table('users')
            ->select('email', 'password')
            ->orderBy('id')
            ->chunk(500, function ($chunk) use ($blankEmails, &$restored, &$skipped, $dryRun): void {
                foreach ($chunk as $row) {
                    $email = strtolower(trim((string) ($row->email ?? '')));
                    $digest = (string) ($row->password ?? '');

                    if ($email === '' || ! $blankEmails->has($email)) {
                        continue;
                    }

                    if (! LegacyPasswordUserProvider::isLegacyMd5($digest)) {
                        $skipped++;

                        continue;
                    }

                    if (! $dryRun) {
                        // Raw write: the model's `hashed` cast would bcrypt the digest itself,
                        // which would make it unverifiable against the user's plaintext.
                        DB::table('users')
                            ->where('id', $blankEmails->get($email))
                            ->update(['password' => strtolower($digest)]);
                    }

                    $restored++;
                }
            });

        $this->newLine();
        $this->info(($dryRun ? '[dry-run] ' : '').$restored.' account(s) restored to a legacy MD5 digest.');

        if ($skipped > 0) {
            $this->line($skipped.' blank account(s) had no usable digest in live either — they still need a reset.');
        }

        if (! $dryRun && $restored > 0) {
            $this->newLine();
            $this->report();
        }

        return self::SUCCESS;
    }
}
