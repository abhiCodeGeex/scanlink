<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerifyImport extends Command
{
    protected $signature = 'scanlink:verify-import';

    protected $description = 'Verify row counts for critical tables after a live DB import';

    /**
     * @var array<int, string>
     */
    protected array $tables = [
        'clients',
        'client_users',
        'profiles',
        'code_purchase',
        'code_purchase_detail',
        'orders',
        'form_builder_orders',
        'equipment_types',
        'settings',
        'code_prising',
        'reseller_pricing',
        'testimonial',
        'gallery',
        'qrimage',
        'users',
        'admin',
    ];

    public function handle(): int
    {
        $this->info('ScanLink import verification');
        $this->newLine();

        $failures = 0;

        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                $this->warn("[SKIP] {$table} — table not present");
                continue;
            }

            $count = DB::table($table)->count();
            $this->line("[OK] {$table}: {$count} rows");
        }

        $this->newLine();

        if (Schema::hasTable('admin') && Schema::hasTable('users')) {
            $adminCount = DB::table('admin')->count();
            $userCount = DB::table('users')->count();
            $this->info("Admin mapping: {$adminCount} legacy admin row(s), {$userCount} Laravel user(s).");
            $this->line('Run `php artisan scanlink:sync-admin-users` if users count is low.');
        }

        $this->newLine();
        $this->info('Done.');

        return $failures > 0 ? self::FAILURE : self::SUCCESS;
    }
}
