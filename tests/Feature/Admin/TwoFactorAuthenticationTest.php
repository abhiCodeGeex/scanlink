<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminRole;
use App\Filament\Auth\ScanLinkAppAuthentication;
use App\Models\User;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
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
        $this->assertInstanceOf(ScanLinkAppAuthentication::class, collect($providers)->first());
    }

    public function test_mfa_qr_code_data_uri_is_valid_svg_not_double_encoded(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@scanlink.com',
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $uri = ScanLinkAppAuthentication::make()
            ->brandName('ScanLink')
            ->generateQrCodeDataUri('JFXVT35CYVZCZJ27');

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $uri);

        $decoded = base64_decode(substr($uri, strlen('data:image/svg+xml;base64,')), true);

        $this->assertIsString($decoded);
        $this->assertStringContainsString('<svg', $decoded);
        $this->assertStringStartsNotWith('data:', $decoded);
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

    public function test_settings_profile_nav_replaces_change_password(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@scanlink.com',
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        $this->actingAs($user);

        $home = $this->get('/admin')->assertSuccessful();
        $home->assertSee('Profile', false);
        $home->assertDontSee('>Change Password<', false);
    }

    public function test_login_page_still_renders_with_mfa_enabled(): void
    {
        $this->get('/admin/login')->assertSuccessful();
    }

    public function test_totp_and_recovery_codes_round_trip_and_login_challenge(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $user = User::factory()->create([
            'email' => 'mfa-admin@scanlink.com',
            'password' => 'Admin@12345',
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        /** @var AppAuthentication $appAuth */
        $appAuth = collect(Filament::getPanel('admin')->getMultiFactorAuthenticationProviders())
            ->first(fn ($provider) => $provider instanceof AppAuthentication);

        $this->assertNotNull($appAuth);

        $secret = $appAuth->generateSecret();
        $user->saveAppAuthenticationSecret($secret);
        $recoveryCodes = $appAuth->generateRecoveryCodes();
        $appAuth->saveRecoveryCodes($user, $recoveryCodes);
        $user = $user->fresh();

        $this->assertTrue($appAuth->isEnabled($user));
        $this->assertTrue($appAuth->verifyCode($appAuth->getCurrentCode($user), $secret));
        $this->assertTrue($appAuth->verifyRecoveryCode($recoveryCodes[0], $user->fresh()));

        Auth::logout();
        Cache::flush();

        $login = Livewire::test(Login::class)
            ->set('data.email', 'mfa-admin@scanlink.com')
            ->set('data.password', 'Admin@12345')
            ->call('authenticate');

        $this->assertNotEmpty($login->get('userUndertakingMultiFactorAuthentication'));

        Cache::flush();
        $otp = $appAuth->getCurrentCode($user->fresh());

        $login
            ->set('data.multiFactor.app.code', $otp)
            ->call('authenticate');

        $this->assertTrue(Filament::auth()->check() || Auth::check());
        $this->assertAuthenticatedAs($user->fresh());
    }
}
