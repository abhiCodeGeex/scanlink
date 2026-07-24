<?php

/**
 * Verify forgot-password for admin + portal panels.
 */

use App\Enums\AdminRole;
use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Filament\Auth\Notifications\ResetPassword as FilamentResetPassword;
use Filament\Facades\Filament;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$failures = [];
$ok = fn (string $msg) => print("[OK] {$msg}\n");
$fail = function (string $msg) use (&$failures): void {
    $failures[] = $msg;
    echo "[FAIL] {$msg}\n";
};

echo 'MAIL_MAILER='.config('mail.default')."\n";
echo 'MAIL='.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port')."\n";
echo 'FROM='.config('mail.from.address')."\n\n";

if (! Schema::hasTable('password_reset_tokens')) {
    $fail('password_reset_tokens table missing — forgot password cannot store tokens');
    echo "RESULT: broken\n";
    exit(1);
}
$ok('password_reset_tokens table exists');

foreach ([
    'admin' => '/admin/password-reset/request',
    'portal' => '/portal/password-reset/request',
] as $label => $path) {
    $status = $app->handle(Illuminate\Http\Request::create($path, 'GET'))->getStatusCode();
    $status === 200 ? $ok("{$label} request page HTTP 200") : $fail("{$label} request page HTTP {$status}");
}

$adminEmail = 'forgot-admin-test@scanlink.local';
$portalEmail = 'forgot-portal-test@scanlink.local';

$admin = User::query()->updateOrCreate(
    ['email' => $adminEmail],
    [
        'name' => 'Forgot Admin Test',
        'password' => 'OldPass@12345',
        'user_type' => UserType::Admin,
        'admin_role' => AdminRole::Support,
    ]
);

$portal = User::query()->updateOrCreate(
    ['email' => $portalEmail],
    [
        'name' => 'Forgot Portal Test',
        'password' => 'OldPass@12345',
        'user_type' => UserType::Portal,
        'admin_role' => null,
    ]
);

$client = Client::query()->orderBy('id')->first();
if (! $client) {
    $fail('no client row available for portal membership');
} else {
    $member = ClientUser::query()->where('auth_user_id', $portal->id)->first()
        ?? ClientUser::query()->where('email', $portalEmail)->first();

    if ($member) {
        $member->forceFill([
            'client_id' => $client->id,
            'email' => $portalEmail,
            'status' => true,
            'auth_user_id' => $portal->id,
        ])->save();
    } else {
        ClientUser::query()->create([
            'client_id' => $client->id,
            'email' => $portalEmail,
            'password' => 'OldPass@12345',
            'status' => true,
            'is_password_change' => true,
            'auth_user_id' => $portal->id,
            'role' => 5,
            'first_name' => 'Forgot',
            'last_name' => 'Portal',
        ]);
    }
}

$admin->refresh();
$portal->refresh();

$admin->canAccessPanel(Filament::getPanel('admin'))
    ? $ok('admin user canAccessPanel(admin)')
    : $fail('admin user cannot access admin panel');

$portal->canAccessPanel(Filament::getPanel('portal'))
    ? $ok('portal user canAccessPanel(portal)')
    : $fail('portal user cannot access portal panel');

$sendFilamentReset = function (string $panelId, User $user) use ($ok, $fail): void {
    Filament::setCurrentPanel(Filament::getPanel($panelId));
    Notification::fake();

    // Clear prior token + cache throttle key so broker will send again.
    DB::table('password_reset_tokens')->where('email', $user->email)->delete();
    $key = 'password.reset|'.sha1(strtolower($user->email).'|'.request()->ip());
    cache()->forget($key);
    // Laravel Password broker throttle uses Cache::add with key from PasswordBroker
    Illuminate\Support\Facades\Cache::flush();

    $notified = false;
    $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
        ['email' => $user->email],
        function ($authUser, string $token) use ($panelId, &$notified): void {
            if (
                ($authUser instanceof \Filament\Models\Contracts\FilamentUser)
                && (! $authUser->canAccessPanel(Filament::getPanel($panelId)))
            ) {
                return;
            }

            $notification = app(FilamentResetPassword::class, ['token' => $token]);
            $notification->url = Filament::getResetPasswordUrl($token, $authUser);
            $authUser->notify($notification);
            $notified = true;
        }
    );

    if ($status !== Password::RESET_LINK_SENT) {
        $fail("{$panelId} broker status={$status}");

        return;
    }

    if (! $notified) {
        $fail("{$panelId} broker returned SENT but Filament panel guard blocked notify");

        return;
    }

    $sent = Notification::sent($user->fresh(), FilamentResetPassword::class);
    if ($sent->isEmpty()) {
        $fail("{$panelId} ResetPassword notification not recorded");

        return;
    }

    $url = $sent->first()->url ?? '';
    if (! str_contains((string) $url, "/{$panelId}/password-reset/reset")) {
        $fail("{$panelId} reset URL unexpected: {$url}");

        return;
    }

    $ok("{$panelId} forgot-password sends email notification with correct reset URL");
};

$sendFilamentReset('admin', $admin);
$sendFilamentReset('portal', $portal);

$adminLogin = (string) $app->handle(Illuminate\Http\Request::create('/admin/login', 'GET'))->getContent();
(stripos($adminLogin, 'password-reset') !== false || stripos($adminLogin, 'Forgot') !== false)
    ? $ok('admin login shows forgot-password link')
    : $fail('admin login missing forgot-password link');

$home = (string) $app->handle(Illuminate\Http\Request::create('/', 'GET'))->getContent();
(str_contains($home, '/portal/password-reset/request') && stripos($home, 'Forgot') !== false)
    ? $ok('client login (marketing) shows Forgot password? link')
    : $fail('client login missing Forgot password? link');

echo "\n";
if ($failures === []) {
    echo "RESULT: Forgot password is working for both admin and client panels.\n";
    echo "NOTE: mailer is smtp (".config('mail.mailers.smtp.host').") — check inbox/spam for real delivery.\n";
    exit(0);
}

echo "RESULT: Issues found:\n";
foreach ($failures as $f) {
    echo " - {$f}\n";
}
exit(1);
