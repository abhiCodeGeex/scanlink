<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Widen short varchar phone fields so longer numbers (and +country codes) save cleanly.
 */
return new class extends Migration
{
    /**
     * @var list<array{0: string, 1: string}>
     */
    private array $phoneColumns = [
        ['profiles', 'telephone'],
        ['profiles', 'mobile'],
        ['profile_contact', 'telephone'],
    ];

    public function up(): void
    {
        $previousMode = DB::selectOne('SELECT @@SESSION.sql_mode AS mode')->mode ?? '';

        // Live profiles rows may have zero dates; MySQL refuses ALTER while those modes are on.
        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

        try {
            foreach ($this->phoneColumns as [$table, $column]) {
                $this->widenPhoneColumn($table, $column);
            }
        } finally {
            DB::statement('SET SESSION sql_mode = ?', [$previousMode]);
        }
    }

    public function down(): void
    {
        // Intentionally irreversible.
    }

    private function widenPhoneColumn(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $meta = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->first(['DATA_TYPE', 'CHARACTER_MAXIMUM_LENGTH', 'IS_NULLABLE']);

        if (! $meta || $meta->DATA_TYPE !== 'varchar') {
            return;
        }

        if ((int) $meta->CHARACTER_MAXIMUM_LENGTH >= 50) {
            return;
        }

        $nullSql = $meta->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';

        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` VARCHAR(50) {$nullSql}");
    }
};
