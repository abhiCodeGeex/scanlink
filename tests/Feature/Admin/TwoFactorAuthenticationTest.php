<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminRole;
use App\Models\User;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_supports_app_authentication(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@scanlink.com',
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        $this->assertInstanceOf(HasAppAuthentication::class, $user);
        $this->assertInstanceOf(HasAppAuthenticationRecovery::class, $user);
        $this->assertNull($user->getAppAuthenticationSecret());

        $user->saveAppAuthenticationSecret('test-secret');
        $user->saveAppAuthenticationRecoveryCodes(['code-one', 'code-two']);

        $fresh = $user->fresh();

        $this->assertSame('test-secret', $fresh->getAppAuthenticationSecret());
        $this->assertSame(['code-one', 'code-two'], $fresh->getAppAuthenticationRecoveryCodes());

        // Secret is stored encrypted, never plaintext in DB.
        $raw = \DB::table('users')->where('id', $user->id)->value('app_authentication_secret');
        $this->assertNotSame('test-secret', $raw);
    }

    public function test_panel_has_app_authentication_provider(): void
    {
        $panel = filament()->getCurrentOrDefaultPanel();

        $this->assertTrue($panel->hasMultiFactorAuthentication());

        $providers = $panel->getMultiFactorAuthenticationProviders();

        $this->assertNotEmpty($providers);
        $this->assertInstanceOf(AppAuthentication::class, collect($providers)->first());
    }

    public function test_profile_page_is_available_for_two_factor_setup(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@scanlink.com',
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        $this->actingAs($user);

        $this->get('/admin/profile')->assertSuccessful();
    }

    public function test_login_page_still_renders_with_mfa_enabled(): void
    {
        $this->get('/admin/login')->assertSuccessful();
    }
}
