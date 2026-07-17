<?php

/**
 * Authenticated HTML dump + layout geometry markers for create?type=location.
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

$user = User::query()->find($member->auth_user_id);
Auth::login($user);

$request = Illuminate\Http\Request::create('/portal/profiles/create?type=location', 'GET');
$app->instance('request', $request);
Illuminate\Support\Facades\URL::forceRootUrl(config('app.url') ?: 'http://localhost:8000');

$response = $app->handle($request);
$html = (string) $response->getContent();

$out = storage_path('app/screenshots');
if (! is_dir($out)) {
    mkdir($out, 0777, true);
}

file_put_contents($out.'/create-location.html', $html);

$checks = [
    'status_200' => $response->getStatusCode() === 200,
    'scanlink-container' => str_contains($html, 'scanlink-container'),
    'sl-profile-editor' => str_contains($html, 'sl-profile-editor'),
    'iphone-preview' => str_contains($html, 'iphone-preview'),
    'form_builder' => str_contains($html, 'Form Builder'),
    'form_cols_1' => str_contains($html, '--cols-lg: repeat(1, minmax(0, 1fr))'),
    'not_form_cols_2_root' => ! preg_match(
        '/x-data="filamentSchema\(\{ livewireId: \'[^\']+\' \}\)".{0,80}--cols-lg: repeat\(2/',
        $html
    ),
    'theme_v23' => str_contains($html, 'scanlink-theme.css?v=23'),
    'Location Name' => str_contains($html, 'Location name') || str_contains($html, 'Location Name'),
    'Data Collection Name toggle' => str_contains($html, 'data_collection_name') || str_contains($html, 'data.collection'),
];

$failed = 0;
foreach ($checks as $name => $ok) {
    echo ($ok ? 'PASS' : 'FAIL').' '.$name.PHP_EOL;
    if (! $ok) {
        $failed++;
    }
}

echo 'HTML '.$out.'/create-location.html'.PHP_EOL;
exit($failed > 0 ? 2 : 0);
