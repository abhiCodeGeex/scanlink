<?php

/**
 * Create (or reset) a local client-portal test account.
 *
 * Usage: php scripts/create-portal-test-user.php
 */

use App\Enums\ClientUserRole;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$email = 'portal-test@scanlink.local';
$password = 'Portal@12345';

$client = Client::query()->where('url', 'baulderstone')->first()
    ?? Client::query()->orderBy('id')->first();

if (! $client) {
    fwrite(STDERR, "No clients found. Import data first.\n");
    exit(1);
}

$member = ClientUser::query()->firstOrNew(['email' => $email]);
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
    'is_password_change' => false,
    'is_sub_user' => false,
    'first_name' => 'Portal',
    'last_name' => 'Tester',
    'company_name' => $client->client_name ?: 'ScanLink Test',
    'billing_address' => '',
    'town' => '',
    'phone' => 0,
    'postal_code' => 0,
    'client_reseller_code' => '',
    'reseller_code' => '',
    'reseller_email' => '',
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

$user = User::query()->where('email', $email)->first();

echo "OK portal test account ready\n";
echo "URL:      http://localhost:8000/portal/login\n";
echo "Email:    {$email}\n";
echo "Password: {$password}\n";
echo "Client:   #{$client->id} {$client->client_name} ({$client->url})\n";
echo "Auth ID:  ".($user?->id ?? 'missing')."\n";
echo "Member:   #{$member->id}\n";
