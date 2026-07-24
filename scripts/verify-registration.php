<?php

use App\Filament\Portal\Auth\Register;
use App\Mail\RegistrationWelcomeMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CodePurchase;
use App\Models\User;
use App\Services\ContactCaptchaService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
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

$html = (string) $app->handle(Illuminate\Http\Request::create('/portal/register', 'GET'))->getContent();

foreach ([
    'First name:',
    'Last name:',
    'Company name/Business name:',
    'Address:',
    'Town:',
    'Postal code:',
    'Telephone number:',
    'Verification code:',
    'Reseller Code',
    'Confirm Password:',
] as $needle) {
    str_contains($html, $needle) ? $ok("label: {$needle}") : $fail("missing label: {$needle}");
}

str_contains($html, 'Upload') && str_contains($html, 'web links, videos')
    ? $ok('step 3 visible in wizard bar (legacy)')
    : $fail('step 3 missing from wizard bar');

Mail::fake();
$email = 'reg-verify-'.time().'@scanlink.local';
Session::put(ContactCaptchaService::SESSION_KEY, sha1('ABCD'));

try {
    $component = Livewire::test(Register::class)
        ->set('data', [
            'first_name' => 'Reg',
            'last_name' => 'Verify',
            'company_name' => 'Reg Verify Co',
            'billing_address' => '10 Demo Rd',
            'email' => $email,
            'town' => 'Sydney',
            'phone' => '0411222333',
            'postal_code' => '2000',
            'password' => 'Demo12',
            'cpassword' => 'Demo12',
            'client_reseller_code' => '',
            'captcha' => 'ABCD',
            'no_codes' => '1',
        ])
        ->call('register')
        ->assertHasNoErrors()
        ->assertSet('wizardStep', 2);

    $ok('step 1 NEXT advances to step 2');

    User::query()->where('email', $email)->exists()
        ? $fail('account created before step 2')
        : $ok('no account until step 2');

    $component
        ->call('register')
        ->assertHasNoErrors()
        ->assertSet('showNearlyDoneModal', true);

    Auth::check()
        ? $ok('step 2 NEXT shows nearly-done popup and logs in')
        : $fail('user not authenticated after step 2');

    Mail::assertSent(RegistrationWelcomeMail::class, fn (RegistrationWelcomeMail $mail): bool => $mail->hasTo($email));
    $ok('welcome email sent');

    $user = User::query()->where('email', $email)->first();
    $member = ClientUser::query()->where('email', $email)->first();
    $client = $member?->client_id ? Client::query()->find($member->client_id) : null;
    $purchase = $client
        ? CodePurchase::query()->where('client_id', $client->id)->where('free_code', true)->first()
        : null;

    $user && $member && $client && $purchase
        ? $ok('registration created user + client + free code')
        : $fail('registration incomplete');
} catch (Throwable $e) {
    $fail('Livewire register: '.$e->getMessage());
}

if ($failures !== []) {
    echo "\n".count($failures)." failure(s)\n";
    exit(1);
}

echo "\nAll registration checks passed.\n";
