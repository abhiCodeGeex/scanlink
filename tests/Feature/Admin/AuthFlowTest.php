<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_auth_pages_are_available(): void
    {
        $this->get('/admin/login')->assertOk();
        $this->get('/admin/register')->assertOk();
        $this->get('/admin/password-reset/request')->assertOk();
        $this->get('/admin/password-reset/reset')->assertForbidden();
    }

    public function test_new_user_defaults_to_support_role_when_role_missing(): void
    {
        $user = User::query()->create([
            'name' => 'New Admin',
            'email' => 'new-admin@example.com',
            'password' => 'Admin@12345',
        ]);

        $this->assertSame(AdminRole::Support, $user->admin_role);
    }

    public function test_guest_is_redirected_from_admin_home(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_password_reset_request_accepts_registered_email(): void
    {
        User::factory()->create([
            'email' => 'admin@scanlink.com',
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        $response = $this->post('/admin/password-reset/request', [
            'email' => 'admin@scanlink.com',
        ]);

        $response->assertSessionHasNoErrors();
    }
}

