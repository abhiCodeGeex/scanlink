<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'app_authentication_secret')) {
                $table->text('app_authentication_secret')->nullable()->after('password');
            }

            if (! Schema::hasColumn('users', 'app_authentication_recovery_codes')) {
                $after = Schema::hasColumn('users', 'app_authentication_secret')
                    ? 'app_authentication_secret'
                    : 'password';
                $table->text('app_authentication_recovery_codes')->nullable()->after($after);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'app_authentication_recovery_codes')) {
                $table->dropColumn('app_authentication_recovery_codes');
            }

            if (Schema::hasColumn('users', 'app_authentication_secret')) {
                $table->dropColumn('app_authentication_secret');
            }
        });
    }
};
