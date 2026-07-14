<?php

namespace Tests\Feature\Portal;

use App\Enums\ClientUserRole;
use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_auth_pages_are_available(): void
    {
        $this->get('/portal/login')->assertOk();
        $this->get('/portal/register')->assertOk();
        $this->get('/portal/password-reset/request')->assertOk();
    }

    public function test_guest_is_redirected_from_portal_dashboard(): void
    {
        $this->get('/portal')->assertRedirect('/portal/login');
    }

    public function test_portal_user_can_access_dashboard(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'portal-user@example.com',
            'status' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user)
            ->get('/portal/dashboard')
            ->assertOk();
    }

    public function test_admin_user_cannot_access_portal_without_membership(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@scanlink.com',
        ]);

        $this->actingAs($admin)
            ->get('/portal/dashboard')
            ->assertForbidden();
    }

    public function test_voclogin_redirects_to_portal_login(): void
    {
        $this->get('/voclogin')->assertRedirect('/portal/login');
    }
}
