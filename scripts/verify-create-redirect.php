<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('email', 'acme@example.com')->firstOrFail();
Auth::login($user);

$request = Illuminate\Http\Request::create(
    'http://localhost:8000/portal/profiles/create?type=location',
    'GET',
    [],
    [],
    [],
    [
        'HTTP_HOST' => 'localhost:8000',
        'HTTP_ACCEPT' => 'text/html',
    ]
);
$request->setLaravelSession(app('session.store'));
$request->setUserResolver(fn () => $user);
app()->instance('request', $request);

$response = $kernel->handle($request);
echo 'status='.$response->getStatusCode().PHP_EOL;
echo 'location='.($response->headers->get('Location') ?? '').PHP_EOL;

// Follow redirect once if present
$loc = $response->headers->get('Location');
if ($loc && $response->isRedirection()) {
    $req2 = Illuminate\Http\Request::create($loc, 'GET', [], [], [], [
        'HTTP_HOST' => 'localhost:8000',
        'HTTP_ACCEPT' => 'text/html',
    ]);
    $req2->setLaravelSession(app('session.store'));
    $req2->setUserResolver(fn () => $user);
    app()->instance('request', $req2);
    Auth::login($user);
    $response = $kernel->handle($req2);
    echo 'edit_status='.$response->getStatusCode().PHP_EOL;
}

$body = (string) $response->getContent();
echo 'has_iframe='.(str_contains($body, 'iframe_frm_builder') ? 'yes' : 'no').PHP_EOL;
echo 'has_embed='.(str_contains($body, 'embed=1') ? 'yes' : 'no').PHP_EOL;
echo 'has_save_first='.(str_contains($body, 'Save the profile first') ? 'yes' : 'no').PHP_EOL;
echo 'has_form_name='.(str_contains($body, 'Form Name') || str_contains($body, 'fb_form_name') ? 'yes' : 'no').PHP_EOL;
