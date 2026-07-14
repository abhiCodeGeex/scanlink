<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\Phase2Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Phase2SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase2_seeder_creates_admin_and_sample_data(): void
    {
        $this->seed(Phase2Seeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@scanlink.com',
        ]);

        $admin = User::query()->where('email', 'admin@scanlink.com')->first();
        $this->assertTrue(Hash::check('Admin@12345', $admin->password));

        $this->assertDatabaseCount('clients', 2);
        $this->assertDatabaseCount('client_users', 3);
        $this->assertDatabaseCount('code_purchase', 2);
    }
}
