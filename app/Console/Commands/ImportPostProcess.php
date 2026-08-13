<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportPostProcess extends Command
{
    protected $signature = 'scanlink:import-postprocess
                            {--default-password=Admin@12345 : Fallback / admin password for synced users}
                            {--ensure-admin=admin@scanlink.com : Guaranteed Filament admin email}
                            {--skip-user-sync : Skip identity sync into Laravel users}
                            {--sync-phpmyadmin : Also copy Docker DB into host WAMP/XAMPP MySQL for phpMyAdmin}
                            {--skip-analytics : When syncing to phpMyAdmin, omit huge analytics tables}
                            {--force : Skip confirmations}';

    protected $description = 'Post-import: adapt schema, sync users, optionally sync host phpMyAdmin DB';

    public function handle(): int
    {
        $this->info('ScanLink live import post-processing');
        $this->newLine();

        $force = (bool) $this->option('force');

        if (! $this->option('skip-user-sync')) {
            $this->call('scanlink:sync-all-users', array_filter([
                '--force' => $force ?: null,
                '--default-password' => $this->option('default-password'),
                '--ensure-admin' => $this->option('ensure-admin'),
                '--admin-password' => $this->option('default-password'),
            ]));
            $this->newLine();
        } else {
            $this->call('scanlink:adapt-live-import', array_filter([
                '--force' => $force ?: null,
            ]));
            $this->newLine();
        }

        $this->call('scanlink:verify-import');

        if ($this->option('sync-phpmyadmin')) {
            $this->newLine();
            $this->info('Syncing Docker DB → host phpMyAdmin MySQL ...');
            $result = $this->call('scanlink:sync-to-phpmyadmin', array_filter([
                '--force' => true,
                '--skip-analytics' => $this->option('skip-analytics') ?: null,
            ]));

            if ($result !== self::SUCCESS) {
                $this->warn('Host phpMyAdmin sync skipped/failed — Docker data is still available on port 3307.');
                $this->line('In WAMP phpMyAdmin choose server: Docker ScanLink (3307) / scanlink / scanlink');
            }
        }

        $this->newLine();
        $this->info('Post-processing complete.');
        $this->line('Admin: '.$this->option('ensure-admin').' / '.$this->option('default-password'));
        $this->line('App: http://localhost:8000/admin');
        $this->line('Docker phpMyAdmin: http://localhost:8080');
        $this->line('WAMP phpMyAdmin: use server "Docker ScanLink (3307)" or host DB after --sync-phpmyadmin');

        return self::SUCCESS;
    }
}
