<?php

/**
 * End-to-end admin 2FA + Profile nav verification.
 */

use App\Enums\AdminRole;
use App\Enums\UserType;
use App\Models\User;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\Pages\EditProfile;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$failures = [];
$ok = fn (string $m) => print("[OK] {$m}\n");
$fail = function (string $m) use (&$failures): void {
    $failures[] = $m;
    echo "[FAIL] {$m}\n";
};

Filament::setCurrentPanel(Filament::getPanel('admin'));

Schema::hasColumn('users', 'app_authentication_secret')
    ? $ok('users.app_authentication_secret exists')
    : $fail('users.app_authentication_secret missing');
Schema::hasColumn('users', 'app_authentication_recovery_codes')
    ? $ok('users.app_authentication_recovery_codes exists')
    : $fail('users.app_authentication_recovery_codes missing');

$panel = Filament::getPanel('admin');
$panel->hasMultiFactorAuthentication()
    ? $ok('admin panel MFA enabled')
    : $fail('admin panel MFA disabled');

$providers = $panel->getMultiFactorAuthenticationProviders();
$appAuth = collect($providers)->first(fn ($p) => $p instanceof AppAuthentication);
$appAuth
    ? $ok('AppAuthentication provider registered')
    : $fail('AppAuthentication provider missing');

if (! $appAuth) {
    echo "RESULT: cannot continue without AppAuthentication\n";
    exit(1);
}

$email = '2fa-verify-admin@scanlink.local';
$user = User::query()->updateOrCreate(
    ['email' => $email],
    [
        'name' => '2FA Verify Admin',
        'password' => 'Admin@12345',
        'user_type' => UserType::Admin,
        'admin_role' => AdminRole::SuperAdmin,
    ]
);
$user->forceFill([
    'password' => 'Admin@12345',
    'user_type' => UserType::Admin,
    'admin_role' => AdminRole::SuperAdmin,
    'app_authentication_secret' => null,
    'app_authentication_recovery_codes' => null,
])->save();
$user = $user->fresh();

$secret = $appAuth->generateSecret();
$user->saveAppAuthenticationSecret($secret);
$plainCodes = $appAuth->generateRecoveryCodes();
$appAuth->saveRecoveryCodes($user, $plainCodes);
$user = $user->fresh();

$user->getAppAuthenticationSecret() === $secret
    ? $ok('secret encrypt/decrypt round-trip')
    : $fail('secret round-trip failed');

$raw = \DB::table('users')->where('id', $user->id)->value('app_authentication_secret');
($raw !== $secret && filled($raw))
    ? $ok('secret stored encrypted in DB')
    : $fail('secret not encrypted in DB');

$appAuth->isEnabled($user)
    ? $ok('AppAuthentication::isEnabled true after secret')
    : $fail('isEnabled false after secret');

$code = $appAuth->getCurrentCode($user);
$appAuth->verifyCode($code, $secret, shouldPreventCodeReuse: false)
    ? $ok('TOTP verifyCode works')
    : $fail('TOTP verifyCode failed');

! $appAuth->verifyCode('000000', $secret, shouldPreventCodeReuse: false)
    ? $ok('wrong TOTP rejected')
    : $fail('wrong TOTP unexpectedly accepted');

$recoveryOk = false;
foreach ($plainCodes as $plain) {
    if ($appAuth->verifyRecoveryCode($plain, $user->fresh())) {
        $recoveryOk = true;
        break;
    }
}
$recoveryOk ? $ok('recovery code verifies') : $fail('recovery code verify failed');

Auth::login($user);
$profileStatus = $app->handle(Illuminate\Http\Request::create('/admin/profile', 'GET'))->getStatusCode();
$profileStatus === 200
    ? $ok('GET /admin/profile => 200')
    : $fail("GET /admin/profile => {$profileStatus}");

try {
    $url = EditProfile::getUrl(panel: 'admin');
    str_contains($url, '/admin/profile')
        ? $ok('Settings Profile targets /admin/profile')
        : $fail("EditProfile URL unexpected: {$url}");
} catch (Throwable $e) {
    $fail('EditProfile::getUrl: '.$e->getMessage());
}

Auth::logout();
Illuminate\Support\Facades\Cache::flush();
Illuminate\Support\Facades\RateLimiter::clear('filament');

try {
    $login = Livewire::test(Login::class)
        ->set('data.email', $email)
        ->set('data.password', 'Admin@12345')
        ->call('authenticate');

    $undertaking = $login->get('userUndertakingMultiFactorAuthentication');
    filled($undertaking)
        ? $ok('login pauses for MFA challenge')
        : $fail('login did not enter MFA challenge');

    if (filled($undertaking)) {
        // Clear rate limit between challenge steps (Livewire rateLimit(5) on authenticate).
        Illuminate\Support\Facades\Cache::flush();

        $otp = $appAuth->getCurrentCode($user->fresh());
        $login
            ->set('data.multiFactor.app.code', $otp)
            ->call('authenticate');

        $authenticated = Filament::auth()->check() || Auth::check();
        if (! $authenticated) {
            usleep(500000);
            Illuminate\Support\Facades\Cache::flush();
            $otp = $appAuth->getCurrentCode($user->fresh());
            $login
                ->set('data.multiFactor.app.code', $otp)
                ->call('authenticate');
            $authenticated = Filament::auth()->check() || Auth::check();
        }

        $authenticated
            ? $ok('login completes after valid TOTP')
            : $fail('still unauthenticated after MFA code: '.json_encode($login->errors()->toArray()));
    }
} catch (Throwable $e) {
    $fail('Livewire login MFA flow: '.$e->getMessage());
}

// Leave test account without MFA so it stays usable without an authenticator app.
$user->fresh()->saveAppAuthenticationSecret(null);
$user->fresh()->saveAppAuthenticationRecoveryCodes(null);
$ok('cleared MFA on test account after verification');

echo "\n";
if ($failures === []) {
    echo "RESULT: 2FA fully working; Settings → Profile opens /admin/profile.\n";
    exit(0);
}

echo "RESULT: Issues:\n";
foreach ($failures as $f) {
    echo " - {$f}\n";
}
exit(1);
