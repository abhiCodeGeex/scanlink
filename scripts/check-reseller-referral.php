<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$partner = App\Models\Client::where('reseller_code', 'TESTRESELLER')->first();

echo "PARTNER\n";
echo "id={$partner->id} name={$partner->client_name}\n";

foreach (App\Models\ClientUser::where('client_id', $partner->id)->get(['id', 'email', 'first_name', 'last_name']) as $user) {
    echo "login_email={$user->email} name={$user->first_name} {$user->last_name}\n";
}

echo "\nREFERRED_WITH_TESTRESELLER\n";
$referred = App\Models\ClientUser::where('client_reseller_code', 'TESTRESELLER')->orderByDesc('id')->get();

if ($referred->isEmpty()) {
    echo "none\n";
}

foreach ($referred as $user) {
    echo "user_id={$user->id} email={$user->email} name={$user->first_name} {$user->last_name} client_id={$user->client_id} created={$user->created_at}\n";
    $client = App\Models\Client::find($user->client_id);
    echo "  company=" . ($client?->client_name ?? '-') . "\n";
    if ($client && method_exists($client, 'resellerName')) {
        echo "  resellerName=" . ($client->resellerName() ?? '-') . "\n";
    }
}
