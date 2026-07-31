<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Live-imported phone columns are INT, which overflows for real numbers
 * (e.g. 4554000341 > signed INT max) and rejects +, spaces, leading zeros.
 * Laravel migrations already declare these as strings — fix the live schema.
 */
return new class extends Migration
{
    /**
     * @var list<array{0: string, 1: string}>
     */
    private array $phoneColumns = [
        ['clients', 'telephone'],
        ['client_users', 'phone'],
        ['code_purchase', 'phone'],
        ['form_builder_orders', 'phone'],
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->phoneColumns as [$table, $column]) {
            $this->changePhoneColumnToString($table, $column);
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: phone values may no longer fit in INT.
    }

    private function changePhoneColumnToString(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $dataType = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->value('DATA_TYPE');

        if (! in_array($dataType, ['int', 'bigint', 'mediumint', 'smallint', 'tinyint'], true)) {
            return;
        }

        $nullable = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->value('IS_NULLABLE') === 'YES';

        $nullSql = $nullable ? 'NULL' : 'NOT NULL';

        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` VARCHAR(50) {$nullSql}");
    }
};
