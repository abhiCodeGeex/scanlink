<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ClientUser;
use App\Models\User;
use App\Services\ProfileDraftSlotService;
use Illuminate\Support\Facades\Auth;

$email = 'acme@example.com';
$user = User::where('email', $email)->first();
$member = ClientUser::where('email', $email)->first();

if (! $user || ! $member) {
    fwrite(STDERR, "Missing portal user\n");
    exit(1);
}

Auth::login($user);

$slot = app(ProfileDraftSlotService::class)->claimForCreate(
    (int) $member->client_id,
    'location',
    (int) $member->id,
);

echo 'claimed_id='.($slot?->id ?? 'null').PHP_EOL;
echo 'type_id='.($slot?->type_id ?? 'null').PHP_EOL;

$editUrl = \App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl(
    'edit',
    ['record' => $slot->getKey()],
    panel: 'portal',
);
$embedUrl = \App\Filament\Portal\Pages\FormBuilder::getUrl(panel: 'portal')
    .'?profile='.$slot->id
    .'&embed=1';

echo 'edit_url='.$editUrl.PHP_EOL;
echo 'embed_url='.$embedUrl.PHP_EOL;

// Hit embed page as authenticated session via HTTP kernel
$request = Illuminate\Http\Request::create($embedUrl, 'GET');
$request->setLaravelSession(app('session')->driver());
$request->setUserResolver(fn () => $user);

$response = $app->handle($request);
echo 'embed_status='.$response->getStatusCode().PHP_EOL;
$body = $response->getContent();
echo 'has_palette='.(str_contains($body, 'Covid check-in') ? 'yes' : 'no').PHP_EOL;
echo 'has_drop='.(str_contains($body, 'CREATE YOUR FORM HERE') ? 'yes' : 'no').PHP_EOL;
echo 'has_save_first='.(str_contains($body, 'Save the profile first') ? 'yes' : 'no').PHP_EOL;
