<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('clients')) {
            return;
        }

        if (! Schema::hasColumn('clients', 'reseller_code_active')) {
            Schema::table('clients', function (Blueprint $table): void {
                // ENUM('0','1') matches live MySQL boolean style used by approve / free_code.
                $table->enum('reseller_code_active', ['0', '1'])
                    ->default('1')
                    ->after('reseller_code');
            });
        }

        // Existing assigned reseller codes stay usable until an admin deactivates them.
        DB::table('clients')
            ->whereNotNull('reseller_code')
            ->where('reseller_code', '!=', '')
            ->update(['reseller_code_active' => '1']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('clients') || ! Schema::hasColumn('clients', 'reseller_code_active')) {
            return;
        }

        Schema::table('clients', function (Blueprint $table): void {
            $table->dropColumn('reseller_code_active');
        });
    }
};
