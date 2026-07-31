<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'admin_role')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('users', function ($table): void {
                $table->string('admin_role')->nullable()->default(null)->change();
            });

            return;
        }

        DB::statement('ALTER TABLE users MODIFY admin_role VARCHAR(255) NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'admin_role')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('users', function ($table): void {
                $table->string('admin_role')->default('super_admin')->nullable(false)->change();
            });

            return;
        }

        DB::statement("ALTER TABLE users MODIFY admin_role VARCHAR(255) NOT NULL DEFAULT 'super_admin'");
    }
};
