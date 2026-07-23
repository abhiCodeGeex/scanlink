<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\Phase3Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultipleCodeRenewalTest extends TestCase
{
    use RefreshDatabase;

    public function test_renewal_list_includes_non_code_profiles_expiring_within_sixty_days(): void
    {
        $this->seed(Phase3Seeder::class);

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'renewal-user@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $assetType = EquipmentType::query()->where('slag', 'asset')->firstOrFail();

        $expiringProfile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $assetType->id,
            'name' => 'Expiring asset profile',
            'expired_at' => now()->addDays(20),
            'deleted' => false,
        ]);

        $this->actingAs($user)
            ->get('/portal/renew-codes')
            ->assertOk()
            ->assertSee('Expiring asset profile')
            ->assertSee((string) $expiringProfile->id);
    }
}
