<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$member = ClientUser::query()->where('status', true)->whereNotNull('auth_user_id')->first();
Auth::login(User::query()->find($member->auth_user_id));

$request = Illuminate\Http\Request::create('/portal/dashboard', 'GET');
$app->instance('request', $request);

try {
    $response = $app->handle($request);
    $html = (string) $response->getContent();
    echo 'STATUS '.$response->getStatusCode().PHP_EOL;
    echo (str_contains($html, 'Server Error') ? 'FAIL' : 'PASS').' dashboard'.PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR '.$e->getMessage().PHP_EOL;
    echo $e->getFile().':'.$e->getLine().PHP_EOL;
}
