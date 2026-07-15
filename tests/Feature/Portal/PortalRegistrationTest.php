<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_registration_creates_client_and_membership(): void
    {
        $email = 'new-client@example.com';

        Livewire::test(\App\Filament\Portal\Auth\Register::class)
            ->set('data', [
                'name' => 'Jane Client',
                'company_name' => 'Acme Scan Co',
                'phone' => '0400000000',
                'email' => $email,
                'password' => 'Password1!',
                'passwordConfirmation' => 'Password1!',
            ])
            ->call('register')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'client_name' => 'Acme Scan Co',
            'email' => $email,
        ]);

        $user = User::query()->where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertSame(UserType::Portal, $user->user_type);

        $this->assertTrue(
            ClientUser::query()
                ->where('email', $email)
                ->where('auth_user_id', $user->id)
                ->exists(),
        );
    }

    public function test_portal_user_can_open_master_code_list(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'codes@example.com',
            'status' => true,
            'is_password_change' => false,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user)
            ->get('/portal/profiles')
            ->assertOk();
    }
}
