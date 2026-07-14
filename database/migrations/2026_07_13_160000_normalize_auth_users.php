<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Canonical auth identity lives on Laravel `users`.
 * Portal / admin / VOC tables keep domain data only and link via auth_user_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'user_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_type', 32)->default('admin')->after('admin_role')->index();
            });
        }

        if (Schema::hasTable('client_users') && ! Schema::hasColumn('client_users', 'auth_user_id')) {
            Schema::table('client_users', function (Blueprint $table) {
                $table->foreignId('auth_user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('admin') && ! Schema::hasColumn('admin', 'auth_user_id')) {
            Schema::table('admin', function (Blueprint $table) {
                $table->unsignedBigInteger('auth_user_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('voc_users') && ! Schema::hasColumn('voc_users', 'auth_user_id')) {
            Schema::table('voc_users', function (Blueprint $table) {
                $table->unsignedBigInteger('auth_user_id')->nullable()->after('voc_user_id')->index();
            });
        }

        if (Schema::hasTable('user_register') && ! Schema::hasColumn('user_register', 'auth_user_id')) {
            Schema::table('user_register', function (Blueprint $table) {
                $table->unsignedBigInteger('auth_user_id')->nullable()->after('id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('client_users') && Schema::hasColumn('client_users', 'auth_user_id')) {
            Schema::table('client_users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('auth_user_id');
            });
        }

        foreach (['admin', 'voc_users', 'user_register'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'auth_user_id')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $blueprint->dropColumn('auth_user_id');
                });
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'user_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_type');
            });
        }
    }
};
