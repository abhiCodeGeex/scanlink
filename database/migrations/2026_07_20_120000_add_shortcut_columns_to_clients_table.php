<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'shortcut_title')) {
                $table->string('shortcut_title')->default('')->after('approve');
            }

            if (! Schema::hasColumn('clients', 'shortcut_image1')) {
                $table->string('shortcut_image1')->default('')->after('shortcut_title');
            }

            if (! Schema::hasColumn('clients', 'shortcut_image2')) {
                $table->string('shortcut_image2')->default('')->after('shortcut_image1');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['shortcut_title', 'shortcut_image1', 'shortcut_image2'],
                fn (string $column): bool => Schema::hasColumn('clients', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
