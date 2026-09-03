<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ImportCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_import_runs_without_legacy_admin_table(): void
    {
        $this->artisan('scanlink:verify-import')
            ->assertSuccessful();
    }

    public function test_sync_admin_users_maps_legacy_rows(): void
    {
        if (! Schema::hasTable('admin')) {
            Schema::create('admin', function (Blueprint $table) {
                $table->id();
                $table->string('username');
                $table->string('password');
            });
        }

        DB::table('admin')->insert([
            'username' => 'legacyadmin',
            'password' => 'plainpass',
        ]);

        $this->artisan('scanlink:sync-admin-users', ['--default-password' => 'Sync@12345'])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'legacyadmin@scanlink.local',
        ]);

        $user = User::query()->where('email', 'legacyadmin@scanlink.local')->first();
        $this->assertNotNull($user);
    }
}
