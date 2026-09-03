<?php

namespace Tests\Feature;

use App\Enums\AdminRole;
use App\Models\User;
use Database\Seeders\Phase2Seeder;
use Database\Seeders\Phase3Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Phase3SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase3_seeder_creates_equipment_types_and_profiles(): void
    {
        $this->seed(Phase2Seeder::class);
        $this->seed(Phase3Seeder::class);

        $this->assertDatabaseCount('equipment_types', 10);
        $this->assertDatabaseCount('profiles', 3);
        $this->assertDatabaseHas('profiles', [
            'code_profile_name' => 'acme-forklift-a12',
            'deleted' => false,
        ]);
        $this->assertDatabaseHas('profiles', [
            'code_profile_name' => 'acme-ladder-archived',
            'deleted' => true,
        ]);
    }

    public function test_admin_user_has_super_admin_role(): void
    {
        $this->seed(Phase2Seeder::class);

        $admin = User::query()->where('email', 'admin@scanlink.com')->first();

        $this->assertSame(AdminRole::SuperAdmin, $admin->admin_role);
        $this->assertTrue($admin->canManageAdmins());
        $this->assertTrue(Hash::check('Admin@12345', $admin->password));
    }
}
