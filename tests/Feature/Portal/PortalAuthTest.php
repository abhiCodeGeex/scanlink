<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_home_shows_login_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Create', false)
            ->assertSee('Connect', false)
            ->assertSee('Measure', false)
            ->assertSee('Login')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('Express Code Generator');
    }

    public function test_portal_login_page_redirects_guests_to_marketing_home(): void
    {
        $this->get('/portal/login')
            ->assertRedirect('/');
    }

    public function test_portal_register_and_password_reset_remain_available(): void
    {
        $this->get('/portal/register')->assertOk();
        $this->get('/portal/password-reset/request')->assertOk();
    }

    public function test_guest_is_redirected_from_portal_dashboard(): void
    {
        $this->get('/portal')->assertRedirect('/portal/login');
    }

    public function test_portal_user_can_login_from_marketing_home(): void
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
        ])->assertRedirect('/portal/profiles');

        $this->assertAuthenticatedAs($user);
    }

    public function test_portal_login_ignores_stale_admin_intended_url(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'portal-intended@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'portal-intended@example.com',
            'password' => 'Portal@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->withSession(['url.intended' => 'http://localhost/admin/subdivide-client'])
            ->post('/portal-login', [
                'email' => 'portal-intended@example.com',
                'password' => 'Portal@12345',
            ])
            ->assertRedirect('/portal/profiles');

        $this->assertAuthenticatedAs($user);
    }

    public function test_portal_user_can_access_master_code_list(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'portal-user@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user)
            ->get('/portal/profiles')
            ->assertOk();
    }

    public function test_portal_dashboard_redirects_to_edit_user_profile(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'portal-user@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user)
            ->get('/portal/dashboard')
            ->assertRedirect('/portal/account');
    }

    public function test_admin_user_cannot_access_portal_without_membership(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@scanlink.com',
        ]);

        $this->actingAs($admin)
            ->get('/portal/profiles')
            ->assertForbidden();
    }

    public function test_voclogin_redirects_to_marketing_home(): void
    {
        $this->get('/voclogin')->assertRedirect('/#login');
    }

    public function test_voc_card_holder_login_lands_on_voc_dashboard(): void
    {
        $user = $this->makeVocCardHolder('voc-login@example.com', 'Voc@12345');

        $this->post('/portal-login', [
            'email' => 'voc-login@example.com',
            'password' => 'Voc@12345',
        ])->assertRedirect('/portal/voc-dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_voc_card_holder_is_redirected_from_master_code_list(): void
    {
        $user = $this->makeVocCardHolder('voc-profiles@example.com', 'Voc@12345');

        $this->actingAs($user)
            ->get('/portal/profiles')
            ->assertRedirect('/portal/voc-dashboard');
    }

    public function test_voc_card_holder_can_open_voc_dashboard(): void
    {
        $user = $this->makeVocCardHolder('voc-dash@example.com', 'Voc@12345');

        $this->actingAs($user)
            ->get('/portal/voc-dashboard')
            ->assertOk();
    }

    public function test_portal_logout_redirects_to_marketing_home(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'portal-logout@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user)
            ->post('/portal/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_portal_user_must_change_password_when_flag_is_false(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'force-pwd@example.com',
            'status' => true,
            'is_password_change' => false,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user)
            ->get('/portal/profiles')
            ->assertRedirect('/portal/force-password-change');
    }

    public function test_force_password_change_clears_flag_and_allows_portal(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'force-pwd-save@example.com',
            'status' => true,
            'is_password_change' => false,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'password' => 'OldPass@123',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Filament\Portal\Pages\ForcePasswordChange::class)
            ->fillForm([
                'password' => 'NewPass@12345',
                'password_confirmation' => 'NewPass@12345',
            ])
            ->call('updatePassword')
            ->assertHasNoFormErrors()
            ->assertRedirect('/portal/account');

        $this->assertTrue((bool) $member->fresh()->is_password_change);

        $this->actingAs($user->fresh())
            ->get('/portal/account')
            ->assertOk();
    }

    private function makeVocCardHolder(string $email, string $password): User
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;
        $client = Client::factory()->create();
        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $typeId,
            'code_profile_name' => 'VOC Card Holder Test',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        $nextId = ((int) VocUser::query()->max('voc_user_id')) + 1;
        $vocUser = VocUser::create([
            'voc_user_id' => $nextId,
            'profile_id' => $profile->id,
            'email' => $email,
            'password' => $password,
        ]);

        return User::query()->findOrFail($vocUser->fresh()->auth_user_id);
    }
}
