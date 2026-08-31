<?php

namespace Tests\Feature\Portal;

use App\Enums\ClientUserRole;
use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\CreateProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubUserCreateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_sub_user_with_add_code_can_open_create_profile_page(): void
    {
        $client = Client::factory()->create();
        // 'location' ships in the seeder — a blind factory insert violates the unique slag.
        $locationType = EquipmentType::query()->firstOrCreate(
            ['slag' => 'location'],
            ['name' => 'Location'],
        );

        $subMember = ClientUser::factory()->subUser()->create([
            'client_id' => $client->id,
            'email' => 'sub-user@example.com',
            'password' => 'SubUser@12345',
            'status' => true,
            'is_password_change' => true,
            'access_addcode' => true,
            'show_code_profile_id_to_acc_user' => '999',
        ]);
        $subMember->refresh();

        Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $locationType->id,
            'update_or_not' => false,
            'deleted' => false,
            'expired_at' => now()->addYear(),
        ]);

        $user = User::query()->findOrFail($subMember->auth_user_id);
        $user->update([
            'email' => 'sub-user@example.com',
            'password' => 'SubUser@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($user);

        Livewire::withQueryParams(['type' => 'location'])
            ->test(CreateProfile::class)
            ->assertSuccessful();
    }

    public function test_sub_user_without_add_code_cannot_create_profile(): void
    {
        $client = Client::factory()->create();

        $subMember = ClientUser::factory()->subUser()->create([
            'client_id' => $client->id,
            'email' => 'sub-no-add@example.com',
            'password' => 'SubUser@12345',
            'status' => true,
            'is_password_change' => true,
            'access_addcode' => false,
            'show_code_profile_id_to_acc_user' => '',
        ]);
        $subMember->refresh();

        $user = User::query()->findOrFail($subMember->auth_user_id);
        $user->update([
            'email' => 'sub-no-add@example.com',
            'password' => 'SubUser@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($user);

        $this->get('/portal/profiles/create?type=location')
            ->assertForbidden();
    }
}
