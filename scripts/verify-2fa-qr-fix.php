<?php

use App\Enums\AdminRole;
use App\Enums\UserType;
use App\Filament\Auth\ScanLinkAppAuthentication;
use App\Models\User;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Facades\Filament;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

Filament::setCurrentPanel(Filament::getPanel('admin'));

$user = User::query()->updateOrCreate(
    ['email' => 'qr-fix-admin@scanlink.local'],
    [
        'name' => 'QR Fix Admin',
        'password' => 'Admin@12345',
        'user_type' => UserType::Admin,
        'admin_role' => AdminRole::SuperAdmin,
    ]
);
Auth::login($user);
Filament::auth()->login($user);

$secret = 'JFXVT35CYVZCZJ27';

$broken = AppAuthentication::make()->brandName('ScanLink');
$fixed = ScanLinkAppAuthentication::make()->brandName('ScanLink');

$brokenUri = $broken->generateQrCodeDataUri($secret);
$fixedUri = $fixed->generateQrCodeDataUri($secret);

$brokenDecoded = base64_decode(substr($brokenUri, strlen('data:image/svg+xml;base64,')), true);
$fixedDecoded = base64_decode(substr($fixedUri, strlen('data:image/svg+xml;base64,')), true);

echo 'broken_double_encoded='.(is_string($brokenDecoded) && str_starts_with($brokenDecoded, 'data:') ? 'yes' : 'no').PHP_EOL;
echo 'fixed_is_svg='.(is_string($fixedDecoded) && str_contains($fixedDecoded, '<svg') ? 'yes' : 'no').PHP_EOL;
echo 'fixed_starts_data='.(str_starts_with($fixedUri, 'data:image/svg+xml;base64,') ? 'yes' : 'no').PHP_EOL;
echo 'provider='.get_class(collect(Filament::getPanel('admin')->getMultiFactorAuthenticationProviders())->first()).PHP_EOL;

if (
    str_starts_with($fixedUri, 'data:image/svg+xml;base64,')
    && is_string($fixedDecoded)
    && str_contains($fixedDecoded, '<svg')
    && ! str_starts_with($fixedDecoded, 'data:')
) {
    echo "RESULT: QR data URI is valid.\n";
    exit(0);
}

echo "RESULT: QR still invalid.\n";
exit(1);
