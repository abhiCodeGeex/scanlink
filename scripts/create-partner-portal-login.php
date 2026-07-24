<?php

/**
 * Create portal login for partner client that has reseller code TESTRESELLER.
 * Admin client edit stores email/password on clients only — portal needs client_users + users.
 */

use App\Enums\ClientUserRole;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$email = 'test1@gmail.com';
$password = '12345678';

$client = Client::query()->where('reseller_code', 'TESTRESELLER')->first()
    ?? Client::query()->find(10);

if (! $client) {
    fwrite(STDERR, "Partner client not found.\n");
    exit(1);
}

$member = ClientUser::query()->firstOrNew([
    'client_id' => $client->id,
    'email' => $email,
]);

$parts = preg_split('/\s+/', trim((string) $client->client_name), 2) ?: [];

$member->forceFill([
    'client_id' => $client->id,
    'email' => $email,
    'password' => $password,
    'role' => ClientUserRole::Primary,
    'status' => true,
    'notice' => false,
    'video_upload' => true,
    'checklist_option' => false,
    'customqr_option' => false,
    'is_password_change' => true,
    'is_sub_user' => false,
    'expire_at' => now()->addYear(),
    'first_name' => $parts[0] ?? 'Partner',
    'last_name' => $parts[1] ?? 'User',
    'company_name' => $client->client_name ?: 'Partner',
    'billing_address' => $client->address ?: '',
    'town' => '',
    'phone' => $client->telephone ?: 0,
    'postal_code' => 0,
    'client_reseller_code' => '',
    'reseller_code' => $client->reseller_code ?: '',
    'reseller_email' => $client->reseller_email ?: '',
    'footer_logo' => '',
    'show_code_profile_id_to_acc_user' => false,
    'access_edit' => true,
    'access_delete' => true,
    'access_addcode' => true,
    'access_analytics' => true,
    'access_form_submission' => true,
    'access_download' => true,
    'access_label' => true,
    'access_log' => true,
]);
$member->save();

$auth = User::query()->where('email', $email)->first();
$ok = $auth && Hash::check($password, $auth->password);

echo "OK partner portal login ready\n";
echo "Email:    {$email}\n";
echo "Password: {$password}\n";
echo "Client:   #{$client->id} {$client->client_name}\n";
echo "Reseller: {$client->reseller_code}\n";
echo "Member:   #{$member->id}\n";
echo "Auth ID:  ".($auth?->id ?? 'missing')."\n";
echo "Password check: ".($ok ? 'PASS' : 'FAIL')."\n";
echo "Login at: /portal  (or home Login popup)\n";
