<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_users', function (Blueprint $table): void {
            if (! Schema::hasColumn('client_users', 'access_analytics')) {
                $table->boolean('access_analytics')->default(false)->after('access_delete');
            }

            if (! Schema::hasColumn('client_users', 'access_form_submission')) {
                $table->boolean('access_form_submission')->default(false)->after('access_analytics');
            }

            if (! Schema::hasColumn('client_users', 'access_download')) {
                $table->boolean('access_download')->default(false)->after('access_form_submission');
            }

            if (! Schema::hasColumn('client_users', 'access_label')) {
                $table->boolean('access_label')->default(false)->after('access_download');
            }

            if (! Schema::hasColumn('client_users', 'access_log')) {
                $table->boolean('access_log')->default(false)->after('access_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_users', function (Blueprint $table): void {
            $columns = [
                'access_analytics',
                'access_form_submission',
                'access_download',
                'access_label',
                'access_log',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('client_users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
