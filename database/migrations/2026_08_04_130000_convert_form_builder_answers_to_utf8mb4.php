<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The live legacy import left form_builder_answers as latin1. Public scan forms are
     * submitted from mobile keyboards that produce utf8mb4 characters (smart quotes,
     * em-dashes, emoji, arrows), which cannot convert into a latin1 column and 500 the
     * whole submission. Convert the table to utf8mb4.
     *
     * Existing content is genuine single-byte cp1252/latin1 (e.g. 0x92 = curly apostrophe),
     * so CONVERT TO CHARACTER SET reinterprets each byte to its correct Unicode form — no
     * double-encoding.
     */
    public function up(): void
    {
        if (! Schema::hasTable('form_builder_answers')) {
            return;
        }

        // Already utf8mb4? nothing to do (keeps this idempotent / safe to re-run).
        $collation = collect(DB::select("SHOW TABLE STATUS WHERE Name = 'form_builder_answers'"))->first()->Collation ?? '';
        if (str_starts_with((string) $collation, 'utf8mb4')) {
            return;
        }

        // Legacy rows contain zero dates ('0000-00-00 00:00:00'). The table rebuild would
        // reject them under strict / NO_ZERO_DATE mode, so relax sql_mode for this
        // connection first — the rebuild then preserves the existing values unchanged.
        $originalSqlMode = (string) (collect(DB::select('SELECT @@SESSION.sql_mode AS m'))->first()->m ?? '');
        DB::statement("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");

        try {
            DB::statement('ALTER TABLE `form_builder_answers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        } finally {
            DB::statement("SET SESSION sql_mode = '".str_replace("'", '', $originalSqlMode)."'");
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: reverting to latin1 would drop the utf8mb4 data
        // this migration exists to allow.
    }
};
