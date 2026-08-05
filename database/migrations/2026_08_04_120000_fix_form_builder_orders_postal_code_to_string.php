<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The live legacy import created form_builder_orders.postal_code as INT, but postal
     * codes / billing data are strings. A non-numeric postal value crashes the Form
     * Builder order insert ("Incorrect integer value ... for column 'postal_code'").
     * The original create migration already declared string(20); align the live column.
     */
    public function up(): void
    {
        if (! Schema::hasTable('form_builder_orders') || ! Schema::hasColumn('form_builder_orders', 'postal_code')) {
            return;
        }

        // MySQL-only fix: the live import created postal_code as INT. Sqlite (tests) is
        // typeless and already stores strings fine, so skip there — `SHOW COLUMNS` is not
        // valid sqlite and would abort the whole migration run.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $column = collect(DB::select("SHOW COLUMNS FROM `form_builder_orders` WHERE Field = 'postal_code'"))->first();

        // Only widen when it is still an integer column (safe: existing numeric values
        // become their string form; no data loss).
        if ($column && str_contains(strtolower((string) $column->Type), 'int')) {
            DB::statement('ALTER TABLE `form_builder_orders` MODIFY `postal_code` VARCHAR(20) NULL');
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: reverting VARCHAR -> INT would corrupt any
        // non-numeric postal codes stored in the meantime.
    }
};
