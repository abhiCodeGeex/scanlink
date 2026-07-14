<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

/**
 * Copy the local Docker Laravel DB into the host MySQL used by WAMP/XAMPP phpMyAdmin (usually :3306).
 */
class SyncToPhpMyAdmin extends Command
{
    protected $signature = 'scanlink:sync-to-phpmyadmin
                            {--host= : Host MySQL hostname (default HOST_MYSQL_HOST / host.docker.internal)}
                            {--port= : Host MySQL port (default HOST_MYSQL_PORT / 3306)}
                            {--username= : Host MySQL user (default HOST_MYSQL_USERNAME / root)}
                            {--password= : Host MySQL password (default HOST_MYSQL_PASSWORD / empty)}
                            {--database= : Target database name (default HOST_MYSQL_DATABASE / DB_DATABASE)}
                            {--skip-analytics : Omit huge analytics/answer tables for a safer/faster sync}
                            {--force : Skip confirmation}';

    protected $description = 'Sync Docker scanlink_laravel into host phpMyAdmin MySQL (WAMP/XAMPP :3306)';

    /** @var list<string> */
    protected array $heavyTables = [
        'ana_item_analytics',
        'form_builder_answers',
        'analytics_datafirst',
        'analytics_datafirstsecond',
    ];

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Overwrite host phpMyAdmin database with Docker data?')) {
            return self::SUCCESS;
        }

        $host = (string) ($this->option('host') ?: env('HOST_MYSQL_HOST', 'host.docker.internal'));
        $port = (string) ($this->option('port') ?: env('HOST_MYSQL_PORT', '3306'));
        $username = (string) ($this->option('username') ?: env('HOST_MYSQL_USERNAME', 'root'));
        $password = (string) ($this->option('password') ?? env('HOST_MYSQL_PASSWORD', ''));
        $database = (string) ($this->option('database') ?: env('HOST_MYSQL_DATABASE', env('DB_DATABASE', 'scanlink_laravel')));

        $sourceDb = (string) env('DB_DATABASE', 'scanlink_laravel');
        $dumpPath = storage_path('app/tmp_host_sync.sql');

        if (! $this->hostIsReachable($host, $port, $username, $password)) {
            $this->error("Cannot connect to host MySQL at {$host}:{$port}.");
            $this->line('Use WAMP phpMyAdmin → server "Docker ScanLink (3307)" instead (same Docker data).');

            return self::FAILURE;
        }

        if ($this->hostIsReadOnly($host, $port, $username, $password)) {
            $this->error('Host MySQL is read-only (innodb_force_recovery > 0).');
            $this->line('Fix WAMP my.ini (set innodb_force_recovery=0) and restart MySQL, or use phpMyAdmin server "Docker ScanLink (3307)".');

            return self::FAILURE;
        }

        $this->info("Dumping Docker DB `{$sourceDb}` ...");
        if (! $this->dumpDockerDatabase($sourceDb, $dumpPath)) {
            return self::FAILURE;
        }

        $sizeMb = round(filesize($dumpPath) / 1048576, 1);
        $this->info("Dump ready ({$sizeMb} MB). Importing into {$host}:{$port}/{$database} ...");

        if (! $this->importToHost($host, $port, $username, $password, $database, $dumpPath)) {
            @unlink($dumpPath);

            return self::FAILURE;
        }

        @unlink($dumpPath);
        $this->info("Synced to host phpMyAdmin DB `{$database}` on {$host}:{$port}.");
        $this->line('Open WAMP phpMyAdmin → MySQL (3306) → '.$database);

        return self::SUCCESS;
    }

    protected function hostIsReachable(string $host, string $port, string $username, string $password): bool
    {
        $process = $this->mysqlProcess($host, $port, $username, $password, ['-e', 'SELECT 1']);
        $process->run();

        return $process->isSuccessful();
    }

    protected function hostIsReadOnly(string $host, string $port, string $username, string $password): bool
    {
        $process = $this->mysqlProcess($host, $port, $username, $password, [
            '-N',
            '-e',
            'SELECT @@innodb_force_recovery',
        ]);
        $process->run();

        if (! $process->isSuccessful()) {
            return true;
        }

        return ((int) trim($process->getOutput())) > 0;
    }

    protected function dumpDockerDatabase(string $sourceDb, string $dumpPath): bool
    {
        File::ensureDirectoryExists(dirname($dumpPath));

        $ignore = [];
        if ($this->option('skip-analytics')) {
            foreach ($this->heavyTables as $table) {
                $ignore[] = '--ignore-table='.$sourceDb.'.'.$table;
            }
        }

        $mysqldump = trim((string) shell_exec('command -v mysqldump 2>/dev/null'));
        if ($mysqldump === '') {
            $this->error('`mysqldump` not found in this container.');
            $this->line('Run from host instead:');
            $this->line('  .\\scripts\\sync-docker-to-wamp.ps1 -SkipAnalytics');
            $this->line('Or rebuild the app image (Dockerfile now includes mariadb-client).');

            return false;
        }

        $cmd = array_merge([
            'mysqldump',
            '-h', env('DB_HOST', 'mysql'),
            '-P', (string) env('DB_PORT', '3306'),
            '-u', env('DB_USERNAME', 'scanlink'),
            '-p'.env('DB_PASSWORD', 'scanlink'),
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--hex-blob',
            '--column-statistics=0',
            '--set-gtid-purged=OFF',
            '--no-tablespaces',
            ...$ignore,
            $sourceDb,
        ], []);

        $process = new Process($cmd);
        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('mysqldump failed: '.$process->getErrorOutput());

            return false;
        }

        File::put($dumpPath, $process->getOutput());

        return File::exists($dumpPath) && filesize($dumpPath) > 0;
    }

    protected function importToHost(
        string $host,
        string $port,
        string $username,
        string $password,
        string $database,
        string $dumpPath,
    ): bool {
        $create = $this->mysqlProcess($host, $port, $username, $password, [
            '-e',
            "DROP DATABASE IF EXISTS `{$database}`; CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;",
        ]);
        $create->run();

        if (! $create->isSuccessful()) {
            $this->error('Could not recreate host database: '.$create->getErrorOutput());

            return false;
        }

        // Raise packet/timeouts for large dumps (best-effort).
        $this->mysqlProcess($host, $port, $username, $password, [
            '-e',
            'SET GLOBAL max_allowed_packet=1073741824; SET GLOBAL net_read_timeout=600; SET GLOBAL net_write_timeout=600;',
        ])->run();

        $import = $this->mysqlProcess($host, $port, $username, $password, [
            '--max_allowed_packet=1G',
            $database,
        ]);
        $import->setInput(File::get($dumpPath));
        $import->setTimeout(900);
        $import->run();

        if (! $import->isSuccessful()) {
            $this->error('Host import failed: '.$import->getErrorOutput());
            $this->line('Tip: retry with --skip-analytics, or use phpMyAdmin "Docker ScanLink (3307)".');

            return false;
        }

        return true;
    }

    /**
     * @param  list<string>  $args
     */
    protected function mysqlProcess(string $host, string $port, string $username, string $password, array $args): Process
    {
        $cmd = [
            'mysql',
            '-h', $host,
            '-P', $port,
            '-u', $username,
        ];

        if ($password !== '') {
            $cmd[] = '-p'.$password;
        }

        return new Process(array_merge($cmd, $args));
    }
}
