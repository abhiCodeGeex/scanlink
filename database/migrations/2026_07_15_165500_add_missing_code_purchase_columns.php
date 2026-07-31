<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Live code_purchase dump is missing columns the Laravel app writes on create/update.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (! Schema::hasTable('code_purchase')) {
            return;
        }

        $previousMode = DB::selectOne('SELECT @@SESSION.sql_mode AS mode')->mode ?? '';
        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

        try {
            if (! Schema::hasColumn('code_purchase', 'transaction_id')) {
                DB::statement('ALTER TABLE `code_purchase` ADD `transaction_id` VARCHAR(255) NULL AFTER `total_amount`');
            }

            if (! Schema::hasColumn('code_purchase', 'reseller_client_id')) {
                DB::statement('ALTER TABLE `code_purchase` ADD `reseller_client_id` INT NULL AFTER `is_reseller_pricing_code`');
            }

            if (! Schema::hasColumn('code_purchase', 'free_code')) {
                DB::statement("ALTER TABLE `code_purchase` ADD `free_code` TINYINT(1) NOT NULL DEFAULT 0 AFTER `reseller_client_id`");
            }

            if (! Schema::hasColumn('code_purchase', 'ordered_on')) {
                DB::statement('ALTER TABLE `code_purchase` ADD `ordered_on` DATETIME NULL AFTER `free_code`');
            }
        } finally {
            DB::statement('SET SESSION sql_mode = ?', [$previousMode]);
        }
    }

    public function down(): void
    {
        // Keep columns — data-safe rollback not needed.
    }
};
