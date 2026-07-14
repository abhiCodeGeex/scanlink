<?php

namespace App\Console\Commands;

use App\Enums\AdminRole;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Build a single Laravel `users` identity table from legacy sources,
 * then link satellite tables via auth_user_id and clear duplicated credentials.
 */
class SyncAllUsers extends Command
{
    protected $signature = 'scanlink:sync-all-users
                            {--default-password= : Fallback password when legacy hash cannot be reused}
                            {--ensure-admin=admin@scanlink.com : Guarantee this admin email exists}
                            {--admin-password=Admin@12345 : Password for --ensure-admin}
                            {--force : Skip confirmation}';

    protected $description = 'Sync admin/portal/VOC/registrant identities into Laravel users (FK-linked, no credential duplicates)';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Sync all user identities into Laravel users and clear duplicated passwords?')) {
            return self::SUCCESS;
        }

        $this->call('scanlink:adapt-live-import', ['--force' => true]);

        if (! Schema::hasTable('users') || Schema::hasColumn('users', 'client_id')) {
            $this->error('Adapt failed: Laravel `users` table is missing or still holds portal rows.');

            return self::FAILURE;
        }

        $this->ensureSchema();

        $defaultPassword = $this->option('default-password') ?: null;
        $synced = [
            'admin' => $this->syncAdmins($defaultPassword),
            'portal' => $this->syncPortalUsers($defaultPassword),
            'voc' => $this->syncVocUsers($defaultPassword),
            'registrant' => $this->syncRegistrants($defaultPassword),
        ];

        $this->ensureKnownAdmin(
            (string) $this->option('ensure-admin'),
            (string) $this->option('admin-password'),
        );

        $this->clearDuplicatedCredentials();

        $this->info('Sync complete:');
        foreach ($synced as $type => $count) {
            $this->line("  {$type}: {$count}");
        }
        $this->line('  laravel users total: '.User::query()->count());

        return self::SUCCESS;
    }

    protected function ensureSchema(): void
    {
        if (Schema::hasTable('client_users') && Schema::hasColumn('client_users', 'expire_at')) {
            DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(@@sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");
            DB::statement('ALTER TABLE client_users MODIFY expire_at DATETIME NULL');
            DB::statement("UPDATE client_users SET expire_at = NULL WHERE expire_at < '1970-01-01'");
        }

        if (! Schema::hasColumn('users', 'user_type')) {
            DB::statement("ALTER TABLE users ADD COLUMN user_type VARCHAR(32) NOT NULL DEFAULT 'admin'");
            DB::statement('CREATE INDEX users_user_type_index ON users (user_type)');
        }

        if (! Schema::hasColumn('users', 'admin_role')) {
            DB::statement('ALTER TABLE users ADD COLUMN admin_role VARCHAR(255) NULL');
        }

        if (Schema::hasTable('client_users') && ! Schema::hasColumn('client_users', 'auth_user_id')) {
            DB::statement('ALTER TABLE client_users ADD COLUMN auth_user_id BIGINT UNSIGNED NULL');
            DB::statement('CREATE INDEX client_users_auth_user_id_index ON client_users (auth_user_id)');
        }

        foreach (['admin', 'voc_users', 'user_register'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'auth_user_id')) {
                DB::statement("ALTER TABLE {$table} ADD COLUMN auth_user_id BIGINT UNSIGNED NULL");
                DB::statement("CREATE INDEX {$table}_auth_user_id_index ON {$table} (auth_user_id)");
            }
        }

        $migration = '2026_07_13_160000_normalize_auth_users';
        if (! DB::table('migrations')->where('migration', $migration)->exists()) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => ((int) DB::table('migrations')->max('batch')) + 1,
            ]);
        }
    }

    protected function syncAdmins(?string $defaultPassword): int
    {
        if (! Schema::hasTable('admin')) {
            return 0;
        }

        $count = 0;

        foreach (DB::table('admin')->get() as $admin) {
            $email = $this->normalizeEmail($admin->email ?? null)
                ?? $this->usernameToEmail($admin->username ?? null);

            if (! $email) {
                $this->warn("Skipping admin id {$admin->id}: no email/username.");

                continue;
            }

            $user = $this->upsertIdentity(
                email: $email,
                name: $admin->username ?? $email,
                rawPassword: $admin->password ?? null,
                defaultPassword: $defaultPassword,
                type: UserType::Admin,
                adminRole: AdminRole::SuperAdmin,
            );

            DB::table('admin')->where('id', $admin->id)->update(['auth_user_id' => $user->id]);
            $count++;
        }

        return $count;
    }

    protected function syncPortalUsers(?string $defaultPassword): int
    {
        if (! Schema::hasTable('client_users')) {
            return 0;
        }

        $count = 0;

        foreach (DB::table('client_users')->orderBy('id')->get() as $row) {
            $email = $this->normalizeEmail($row->email ?? null);

            if (! $email) {
                $this->warn("Skipping client_users id {$row->id}: no email.");

                continue;
            }

            $name = trim(implode(' ', array_filter([
                $row->first_name ?? null,
                $row->last_name ?? null,
            ]))) ?: ($row->company_name ?? $email);

            $user = $this->upsertIdentity(
                email: $email,
                name: $name,
                rawPassword: $row->password ?? null,
                defaultPassword: $defaultPassword,
                type: UserType::Portal,
                adminRole: null,
            );

            DB::table('client_users')->where('id', $row->id)->update(['auth_user_id' => $user->id]);
            $count++;
        }

        return $count;
    }

    protected function syncVocUsers(?string $defaultPassword): int
    {
        if (! Schema::hasTable('voc_users')) {
            return 0;
        }

        $pk = Schema::hasColumn('voc_users', 'voc_user_id') ? 'voc_user_id' : 'id';
        $count = 0;

        foreach (DB::table('voc_users')->get() as $row) {
            $email = $this->normalizeEmail($row->email ?? null);

            if (! $email) {
                continue;
            }

            $user = $this->upsertIdentity(
                email: $email,
                name: $email,
                rawPassword: $row->password ?? null,
                defaultPassword: $defaultPassword,
                type: UserType::Voc,
                adminRole: null,
            );

            DB::table('voc_users')->where($pk, $row->{$pk})->update(['auth_user_id' => $user->id]);
            $count++;
        }

        return $count;
    }

    protected function syncRegistrants(?string $defaultPassword): int
    {
        if (! Schema::hasTable('user_register')) {
            return 0;
        }

        $count = 0;

        foreach (DB::table('user_register')->get() as $row) {
            $email = $this->normalizeEmail($row->Email ?? $row->email ?? null);

            if (! $email) {
                continue;
            }

            $name = trim(implode(' ', array_filter([
                $row->FName ?? null,
                $row->LName ?? null,
                $row->BName ?? null,
            ]))) ?: $email;

            $user = $this->upsertIdentity(
                email: $email,
                name: $name,
                rawPassword: $row->Pass ?? $row->password ?? null,
                defaultPassword: $defaultPassword,
                type: UserType::Registrant,
                adminRole: null,
            );

            DB::table('user_register')->where('id', $row->id)->update(['auth_user_id' => $user->id]);
            $count++;
        }

        return $count;
    }

    protected function ensureKnownAdmin(string $email, string $password): void
    {
        $email = $this->normalizeEmail($email);

        if (! $email) {
            return;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->name = $user->name ?: 'ScanLink Admin';
        $user->user_type = UserType::Admin;
        $user->admin_role = AdminRole::SuperAdmin;
        $user->password = $password;
        $user->save();

        $this->info("Ensured admin login: {$email}");
    }

    protected function clearDuplicatedCredentials(): void
    {
        // Keep satellite emails for display/legacy FK joins until UI fully moves to users.email,
        // but never keep passwords outside Laravel users.
        if (Schema::hasTable('client_users') && Schema::hasColumn('client_users', 'password')) {
            DB::table('client_users')->whereNotNull('auth_user_id')->update(['password' => '']);
        }

        if (Schema::hasTable('admin') && Schema::hasColumn('admin', 'password')) {
            DB::table('admin')->whereNotNull('auth_user_id')->update(['password' => '']);
        }

        if (Schema::hasTable('voc_users') && Schema::hasColumn('voc_users', 'password')) {
            DB::table('voc_users')->whereNotNull('auth_user_id')->update(['password' => '']);
        }

        if (Schema::hasTable('user_register') && Schema::hasColumn('user_register', 'Pass')) {
            DB::table('user_register')->whereNotNull('auth_user_id')->update(['Pass' => '']);
        }

        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'password')) {
            DB::table('clients')->update(['password' => '']);
        }

        $this->info('Cleared duplicated password columns on satellite tables.');
    }

    protected function upsertIdentity(
        string $email,
        string $name,
        ?string $rawPassword,
        ?string $defaultPassword,
        UserType $type,
        ?AdminRole $adminRole,
    ): User {
        $existing = User::query()->where('email', $email)->first();

        // Prefer admin type if the same email appears in multiple sources.
        if ($existing && $existing->user_type === UserType::Admin && $type !== UserType::Admin) {
            return $existing;
        }

        $user = $existing ?? new User(['email' => $email]);
        $user->name = $name ?: $email;
        $user->user_type = $type === UserType::Admin
            ? UserType::Admin
            : ($existing?->user_type === UserType::Admin ? UserType::Admin : $type);

        if ($adminRole) {
            $user->admin_role = $adminRole;
        } elseif ($user->user_type === UserType::Admin && $user->admin_role === null) {
            $user->admin_role = AdminRole::Support;
        } elseif ($user->user_type !== UserType::Admin) {
            $user->admin_role = null;
        }

        $needsPassword = ! $user->exists || blank($user->getRawOriginal('password') ?? null);

        if ($needsPassword && $this->isBcrypt($rawPassword)) {
            $user->save();
            DB::table('users')->where('id', $user->id)->update(['password' => $rawPassword]);

            return $user->refresh();
        }

        if ($needsPassword) {
            $this->assignPassword($user, $rawPassword, $defaultPassword);
        }

        $user->save();

        return $user;
    }

    protected function assignPassword(User $user, ?string $rawPassword, ?string $defaultPassword): void
    {
        if ($this->isLegacyPlain($rawPassword)) {
            $user->password = $rawPassword;

            return;
        }

        if ($this->isMd5($rawPassword)) {
            $user->password = $defaultPassword ?: 'changeme';
            $this->warn("MD5 password for {$user->email} — set to fallback; user should reset.");

            return;
        }

        $user->password = $defaultPassword ?: 'changeme';
    }

    protected function isBcrypt(?string $value): bool
    {
        return filled($value) && Str::startsWith($value, ['$2y$', '$2a$', '$2b$']);
    }

    protected function isMd5(?string $value): bool
    {
        return filled($value) && (bool) preg_match('/^[a-f0-9]{32}$/i', $value);
    }

    protected function isLegacyPlain(?string $value): bool
    {
        return filled($value) && ! $this->isBcrypt($value) && ! $this->isMd5($value);
    }

    protected function normalizeEmail(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return filled($email) && str_contains($email, '@') ? $email : null;
    }

    protected function usernameToEmail(?string $username): ?string
    {
        $username = strtolower(trim((string) $username));

        if (! filled($username)) {
            return null;
        }

        return str_contains($username, '@')
            ? $username
            : $username.'@scanlink.local';
    }
}
