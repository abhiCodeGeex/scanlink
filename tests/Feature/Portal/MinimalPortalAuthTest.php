<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinimalPortalAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_home_shows_login_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    public function test_portal_user_can_login_and_reach_home(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'portal-user@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'portal-user@example.com',
            'password' => 'Portal@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->post('/portal-login', [
            'email' => 'portal-user@example.com',
            'password' => 'Portal@12345',
        ])->assertRedirect('/portal/account');

        $this->assertAuthenticatedAs($user);

        $this->get('/portal/account')->assertOk();
    }

    public function test_portal_login_page_redirects_guests_to_landing(): void
    {
        $this->get('/portal/login')->assertRedirect('/');
    }

    public function test_admin_user_cannot_access_portal(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@scanlink.com',
            'user_type' => UserType::Admin,
        ]);

        $this->actingAs($admin)
            ->get('/portal/account')
            ->assertForbidden();
    }
}
