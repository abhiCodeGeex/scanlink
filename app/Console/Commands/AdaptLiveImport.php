<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * After importing a live ScanLink dump: rename legacy portal `users` → `client_users`,
 * create Filament `users`, and baseline migrations so additive migrations can run.
 */
class AdaptLiveImport extends Command
{
    protected $signature = 'scanlink:adapt-live-import {--force : Run without confirmation}';

    protected $description = 'Adapt a live Kohana dump for Laravel (users → client_users)';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Adapt live dump schema for Laravel?')) {
            return self::SUCCESS;
        }

        if (! Schema::hasTable('users')) {
            $this->error('No `users` table found.');

            return self::FAILURE;
        }

        // Live portal users have client_id; Laravel admin users do not.
        $isPortalUsers = Schema::hasColumn('users', 'client_id');

        if ($isPortalUsers) {
            if (Schema::hasTable('client_users')) {
                $this->warn('`client_users` already exists — skipping rename.');
            } else {
                $this->info('Renaming portal `users` → `client_users` ...');
                DB::statement('RENAME TABLE `users` TO `client_users`');
            }
        } else {
            $this->info('`users` already looks like Laravel admin users — skipping rename.');
        }

        if (! Schema::hasTable('users')) {
            $this->info('Creating Laravel Filament `users` table ...');
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('client_users')) {
            Schema::table('client_users', function (Blueprint $table) {
                if (! Schema::hasColumn('client_users', 'notice')) {
                    $table->integer('notice')->nullable();
                }
                if (! Schema::hasColumn('client_users', 'is_password_change')) {
                    $table->boolean('is_password_change')->default(true);
                }
            });
        }

        if (Schema::hasTable('profiles') && ! Schema::hasColumn('profiles', 'color_code')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->string('color_code', 20)->nullable();
            });
        }

        // Soft deletes for clients — must exist before SoftDeletes trait queries run.
        // Do not only mark the migration as run; apply the column here too.
        if (Schema::hasTable('clients') && ! Schema::hasColumn('clients', 'deleted_at')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        if (! Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (! Schema::hasTable('migrations')) {
            Schema::create('migrations', function (Blueprint $table) {
                $table->id();
                $table->string('migration');
                $table->integer('batch');
            });
        }

        $this->baselineMigrations();

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'admin_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('admin_role')->nullable()->after('password');
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'app_authentication_secret')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('app_authentication_secret')->nullable();
                $table->text('app_authentication_recovery_codes')->nullable();
            });
        }

        $this->info('Live dump adapted for Laravel.');

        return self::SUCCESS;
    }

    protected function baselineMigrations(): void
    {
        $files = collect(glob(database_path('migrations/*.php')))
            ->map(fn (string $path): string => basename($path, '.php'))
            ->values();

        $batch = (int) (DB::table('migrations')->max('batch') ?? 0) + 1;
        $existing = DB::table('migrations')->pluck('migration')->all();

        foreach ($files as $migration) {
            if (in_array($migration, $existing, true)) {
                continue;
            }

            // Only baseline create_* / framework table migrations already satisfied by the dump.
            // Leave additive alter/add/make/seed migrations for `php artisan migrate`.
            if ($this->isAdditiveMigration($migration)) {
                continue;
            }

            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch,
            ]);
            $this->line("Baselined migration: {$migration}");
        }

        // Soft deletes may already be applied above — mark that migration run so migrate is a no-op.
        $softDelete = '2026_07_14_163000_add_soft_deletes_to_clients_table';
        if (
            Schema::hasColumn('clients', 'deleted_at')
            && ! in_array($softDelete, DB::table('migrations')->pluck('migration')->all(), true)
        ) {
            DB::table('migrations')->insert([
                'migration' => $softDelete,
                'batch' => $batch,
            ]);
            $this->line("Baselined migration: {$softDelete}");
        }
    }

    protected function isAdditiveMigration(string $migration): bool
    {
        return (bool) preg_match(
            '/_(add_|make_|alter_|seed_|update_|drop_|rename_|change_)/',
            $migration
        );
    }
}
