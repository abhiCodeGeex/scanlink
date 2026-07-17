<?php

/**
 * Local layout smoke check for portal create?type=location.
 * Prints PASS/FAIL markers only (no account emails).
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$member = ClientUser::query()
    ->where('status', true)
    ->whereNotNull('auth_user_id')
    ->orderByDesc('role')
    ->first();

if (! $member) {
    fwrite(STDERR, "NO_MEMBER\n");
    exit(1);
}

$user = User::query()->find($member->auth_user_id);

if (! $user) {
    fwrite(STDERR, "NO_USER\n");
    exit(1);
}

Auth::login($user);

$request = Illuminate\Http\Request::create('/portal/profiles/create?type=location', 'GET');
$app->instance('request', $request);
Illuminate\Support\Facades\URL::forceRootUrl(config('app.url') ?: 'http://localhost:8000');

try {
    $response = $app->handle($request);
    $html = (string) $response->getContent();

    $checks = [
        'status_200' => $response->getStatusCode() === 200,
        'sl-profile-editor' => str_contains($html, 'sl-profile-editor'),
        'add-form-left' => str_contains($html, 'add-form-left'),
        'add-form-right' => str_contains($html, 'add-form-right'),
        'iphone-preview' => str_contains($html, 'iphone-preview'),
        'Form Builder' => str_contains($html, 'Form Builder'),
        'Code Profile Name' => str_contains($html, 'Code Profile Name'),
        'Location Name' => str_contains($html, 'Location name') || str_contains($html, 'Location Name'),
        'Words' => str_contains($html, 'Words'),
        'Web Link' => str_contains($html, 'Web Link'),
        'Data Collection' => str_contains($html, 'Data Collection'),
        'iphone.png' => str_contains($html, 'iphone.png') || str_contains($html, 'iphone-preview'),
    ];

    $failed = 0;
    foreach ($checks as $name => $ok) {
        echo ($ok ? 'PASS' : 'FAIL').' '.$name.PHP_EOL;
        if (! $ok) {
            $failed++;
        }
    }

    file_put_contents(storage_path('app/layout-check-create-location.html'), $html);
    echo 'HTML_LEN '.strlen($html).PHP_EOL;
    echo 'STATUS '.$response->getStatusCode().PHP_EOL;

    exit($failed > 0 ? 2 : 0);
} catch (Throwable $e) {
    echo 'ERROR '.$e->getMessage().PHP_EOL;
    echo $e->getFile().':'.$e->getLine().PHP_EOL;
    exit(1);
}
