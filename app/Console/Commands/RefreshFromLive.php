<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

/**
 * One command to refresh this app's database from the live production DB:
 *   download live (read-only) -> temp dump -> wipe + import into this DB ->
 *   adapt legacy schema -> `php artisan migrate` -> sync users -> verify.
 *
 * Runs identically on local and server (inside the `app` container, which has the
 * mysql client). Live credentials come from the gitignored .env (LIVE_DB_*), never
 * from this file.
 */
class RefreshFromLive extends Command
{
    protected $signature = 'scanlink:refresh-from-live
                            {--force : Skip the confirmation prompt}
                            {--keep-dump : Keep the downloaded SQL dump instead of deleting it}
                            {--use-existing-dump : Reuse the last downloaded dump instead of re-downloading}
                            {--default-password=Admin@12345 : Fallback login for legacy MD5/unknown password hashes}
                            {--sync-phpmyadmin : Also copy the result into the host phpMyAdmin MySQL}
                            {--skip-analytics : With --sync-phpmyadmin, omit the large analytics tables}';

    protected $description = 'Download the live DB, load it here, then adapt + migrate it to the Laravel structure';

    private string $dumpFile = '/tmp/scanlink_live_refresh.sql';

    public function handle(): int
    {
        $live = config('scanlink.live_db');

        if (blank($live['host'] ?? null) || blank($live['username'] ?? null) || blank($live['password'] ?? null)) {
            $this->error('Missing live DB credentials.');
            $this->line('Set LIVE_DB_HOST, LIVE_DB_PORT, LIVE_DB_DATABASE, LIVE_DB_USERNAME, LIVE_DB_PASSWORD in this machine\'s (gitignored) .env, then run `php artisan config:clear`.');

            return self::FAILURE;
        }

        $target = config('database.connections.'.config('database.default'));
        $targetName = (string) $target['database'];

        $this->warn('This DROPS every table in `'.$targetName.'` (host: '.$target['host'].') and reloads it from live '
            .$live['database'].'@'.$live['host'].'.');

        if (! $this->option('force') && ! $this->confirm('Continue?', false)) {
            $this->info('Aborted — nothing changed.');

            return self::SUCCESS;
        }

        if ($this->option('use-existing-dump') && is_file($this->dumpFile)) {
            $this->info('==> [1/6] Reusing existing dump ('.$this->humanBytes((int) (@filesize($this->dumpFile) ?: 0)).') ...');
        } elseif (($code = $this->downloadLive($live)) !== self::SUCCESS) {
            return $code;
        }

        if (($code = $this->importDump($targetName)) !== self::SUCCESS) {
            return $code;
        }

        if (! $this->option('keep-dump')) {
            @unlink($this->dumpFile);
        }

        // Adapt (rename users→client_users, framework tables, baseline create_* migrations),
        // then apply the additive migrations, then sync identities, then verify.
        $this->info('==> [4/6] Adapting legacy schema for Laravel ...');
        DB::purge();
        $this->call('scanlink:adapt-live-import', ['--force' => true]);

        // adapt-live-import baselines create_* migrations for tables the dump provides, but
        // that also marks Laravel-only create tables (e.g. `notifications`) as "run" without
        // creating them. The migrations are idempotent (hasTable/hasColumn guards), so reset
        // the ledger and let migrate create every missing table while skipping existing ones.
        DB::table('migrations')->delete();

        $this->info('==> [5/6] Applying migrations (Laravel structure) ...');
        $this->call('migrate', ['--force' => true]);

        $this->info('==> [6/6] Syncing users + verifying ...');
        $this->call('scanlink:sync-all-users', [
            '--force' => true,
            '--default-password' => (string) $this->option('default-password'),
        ]);
        $this->call('scanlink:verify-import');

        if ($this->option('sync-phpmyadmin')) {
            $this->newLine();
            $this->call('scanlink:sync-to-phpmyadmin', array_filter([
                '--force' => true,
                '--skip-analytics' => $this->option('skip-analytics') ?: null,
            ]));
        }

        $this->newLine();
        $this->info('Done — local DB refreshed from live and migrated to the Laravel structure.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $live
     */
    private function downloadLive(array $live): int
    {
        $this->info('==> [1/6] Downloading live DB (read-only mysqldump) ...');

        $dump = new Process([
            'mysqldump',
            '-h', (string) $live['host'],
            '-P', (string) $live['port'],
            '-u', (string) $live['username'],
            '--single-transaction', '--quick', '--lock-tables=false',
            '--no-tablespaces', '--skip-triggers', '--hex-blob',
            '--default-character-set=utf8mb4',
            '--result-file='.$this->dumpFile,
            (string) $live['database'],
        ], null, ['MYSQL_PWD' => (string) $live['password']], null, 7200);

        $dump->run();

        if (! $dump->isSuccessful()) {
            $this->error('mysqldump failed:');
            $this->line($dump->getErrorOutput());

            return self::FAILURE;
        }

        $this->line('    downloaded '.$this->humanBytes((int) (@filesize($this->dumpFile) ?: 0)));

        return self::SUCCESS;
    }

    private function importDump(string $targetName): int
    {
        $this->info('==> [2/6] Wiping target DB `'.$targetName.'` ...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (DB::select('SHOW TABLES') as $row) {
            $table = array_values((array) $row)[0];
            DB::statement('DROP TABLE IF EXISTS `'.$table.'`');
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Import through PDO (mysqlnd) rather than the CLI client: the app image ships the
        // MariaDB client, which cannot authenticate to MySQL 8.x (caching_sha2_password),
        // whereas PDO handles it everywhere. The dump's own SET/charset comment lines are
        // skipped — we set the equivalent session state ourselves.
        $this->info('==> [3/6] Importing dump into `'.$targetName.'` ...');

        $handle = @fopen($this->dumpFile, 'r');
        if ($handle === false) {
            $this->error('Could not open dump file '.$this->dumpFile);

            return self::FAILURE;
        }

        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET SESSION sql_mode=''");
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $pdo->exec('SET UNIQUE_CHECKS=0');
        $pdo->exec('SET NAMES utf8mb4');

        $buffer = '';
        $statements = 0;

        try {
            while (($line = fgets($handle)) !== false) {
                if ($buffer === '') {
                    $ltrim = ltrim($line);
                    if ($ltrim === '' || str_starts_with($ltrim, '--') || str_starts_with($ltrim, '/*')) {
                        continue; // skip comment / charset-directive lines
                    }
                }

                $buffer .= $line;

                if (str_ends_with(rtrim($line, "\r\n"), ';')) {
                    $pdo->exec($buffer);
                    $buffer = '';

                    if ((++$statements % 250) === 0) {
                        $this->output->write('.');
                    }
                }
            }
        } catch (\Throwable $e) {
            fclose($handle);
            $this->newLine();
            $this->error('Import failed after '.$statements.' statements: '.$e->getMessage());

            return self::FAILURE;
        }

        fclose($handle);
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        $pdo->exec('SET UNIQUE_CHECKS=1');

        $this->newLine();
        $this->line('    imported '.$statements.' statements');

        return self::SUCCESS;
    }

    private function humanBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $value = (float) $bytes;

        while ($value >= 1024 && $i < count($units) - 1) {
            $value /= 1024;
            $i++;
        }

        return round($value, 1).' '.$units[$i];
    }
}
