<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClientUser;
use App\Models\User;

foreach (['acme@example.com', 'portal-test@scanlink.local', 'admin@scanlink.com'] as $email) {
    $user = User::where('email', $email)->first();
    $member = ClientUser::where('email', $email)->first();
    echo $email.PHP_EOL;
    echo '  user='.($user?->id ?? 'null').' type='.($user?->user_type?->value ?? $user?->user_type ?? 'null').PHP_EOL;
    echo '  member='.($member?->id ?? 'null').' auth_user_id='.($member?->auth_user_id ?? 'null').' client='.($member?->client_id ?? 'null').PHP_EOL;
    if ($user) {
        $via = $user->clientMemberships()->active()->count();
        echo '  memberships_active='.$via.PHP_EOL;
    }
}
