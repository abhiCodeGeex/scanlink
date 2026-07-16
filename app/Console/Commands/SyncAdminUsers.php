<?php

namespace App\Console\Commands;

use App\Enums\AdminRole;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncAdminUsers extends Command
{
    protected $signature = 'scanlink:sync-admin-users
                            {--default-password= : Set a known password for all synced admins (hashed)}';

    protected $description = 'Map legacy admin table rows into Laravel users for Filament login';

    public function handle(): int
    {
        if (! Schema::hasTable('admin')) {
            $this->error('Legacy `admin` table not found. Import the live database dump first.');

            return self::FAILURE;
        }

        $rows = DB::table('admin')->get();

        if ($rows->isEmpty()) {
            $this->warn('No rows in `admin` table.');

            return self::SUCCESS;
        }

        $defaultPassword = $this->option('default-password');
        $synced = 0;

        foreach ($rows as $admin) {
            // Prefer real email; fall back to username@scanlink.local
            $email = $this->resolveEmail($admin->email ?? null)
                ?? $this->resolveEmail($admin->username ?? null);

            if (! $email) {
                $this->warn("Skipping admin id {$admin->id}: no username/email.");

                continue;
            }

            $user = User::query()->firstOrNew(['email' => $email]);

            $user->name = $admin->username ?? $email;
            $user->admin_role = AdminRole::SuperAdmin;
            $user->user_type = UserType::Admin;

            if ($defaultPassword) {
                $user->password = $defaultPassword;
            } elseif (! $user->exists && filled($admin->password ?? null)) {
                // Legacy stores plain text passwords — re-hash for Laravel.
                $user->password = $admin->password;
            } elseif (! $user->exists) {
                $user->password = 'changeme';
            }

            $user->save();
            $synced++;
            $this->line("Synced admin id {$admin->id} → {$email}");
        }

        $this->info("Synced {$synced} admin user(s).");

        return self::SUCCESS;
    }

    protected function resolveEmail(?string $username): ?string
    {
        if (! filled($username)) {
            return null;
        }

        return str_contains($username, '@')
            ? strtolower($username)
            : strtolower($username).'@scanlink.local';
    }
}
