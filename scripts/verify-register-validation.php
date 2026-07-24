<?php

use App\Filament\Portal\Auth\Register;
use App\Services\ContactCaptchaService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$ok = fn (string $m) => print("[OK] {$m}\n");
$fail = function (string $m): void {
    echo "[FAIL] {$m}\n";
    exit(1);
};

Session::put(ContactCaptchaService::SESSION_KEY, sha1('TEST'));

Livewire::test(Register::class)
    ->set('data', [
        'first_name' => '',
        'last_name' => 'X',
        'company_name' => 'Co',
        'billing_address' => 'Addr',
        'email' => 'bad',
        'town' => 'Town',
        'phone' => 'abc',
        'postal_code' => 'xyz',
        'password' => '12',
        'cpassword' => '34',
        'captcha' => 'NOPE',
        'no_codes' => '',
    ])
    ->call('register')
    ->assertHasErrors([
        'data.first_name',
        'data.email',
        'data.phone',
        'data.postal_code',
        'data.password',
        'data.cpassword',
        'data.captcha',
    ])
    ->assertSet('wizardStep', 1);

$ok('invalid step 1 stays on step 1 with field errors');

echo "Validation gate OK.\n";
