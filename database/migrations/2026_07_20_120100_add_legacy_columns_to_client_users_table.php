<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_users', function (Blueprint $table) {
            if (! Schema::hasColumn('client_users', 'footer_logo')) {
                $table->string('footer_logo')->default('')->after('postal_code');
            }

            if (! Schema::hasColumn('client_users', 'reseller_code')) {
                $table->string('reseller_code')->default('')->after('footer_logo');
            }

            if (! Schema::hasColumn('client_users', 'reseller_email')) {
                $table->string('reseller_email')->default('')->after('reseller_code');
            }

            if (! Schema::hasColumn('client_users', 'enable_admin_access')) {
                $table->boolean('enable_admin_access')->default(false)->after('access_log');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_users', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['footer_logo', 'reseller_code', 'reseller_email', 'enable_admin_access'],
                fn (string $column): bool => Schema::hasColumn('client_users', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
