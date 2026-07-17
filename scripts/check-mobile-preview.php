<?php

/**
 * Verify portal mobile preview bypasses expiry for same-client editors.
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Services\MobileProfileViewResolver;
use App\Support\PortalProfilePreview;
use Illuminate\Support\Facades\Auth;

$member = ClientUser::query()
    ->where('status', true)
    ->whereNotNull('auth_user_id')
    ->orderByDesc('role')
    ->first();

Auth::login(User::query()->find($member->auth_user_id));

$cases = [
    ['slag' => 'location', 'view' => 'scan.types.standard'],
    ['slag' => 'code', 'view' => 'scan.types.code'],
    ['slag' => 'survey', 'view' => 'scan.types.survey'],
];

$failed = 0;

foreach ($cases as $case) {
    $profile = Profile::query()
        ->where('client_id', $member->client_id)
        ->where('type_id', EquipmentType::query()->where('slag', $case['slag'])->value('id'))
        ->where('deleted', false)
        ->with('client', 'equipmentType')
        ->orderByDesc('id')
        ->first();

    if (! $profile) {
        echo 'SKIP no '.$case['slag'].PHP_EOL;
        continue;
    }

    $resolverView = app(MobileProfileViewResolver::class)->viewFor($profile);
    $url = '/'.$profile->client->url.'/'.$profile->id.'?ask_for_location=no&portal_preview=1';
    $request = Illuminate\Http\Request::create($url, 'GET');
    $app->instance('request', $request);
    $html = (string) $app->handle($request)->getContent();

    $okView = $resolverView === $case['view'];
    $okNotExpired = ! str_contains($html, 'Code expired');

    echo ($okView ? 'PASS' : 'FAIL').' view_'.$case['slag'].'='.$resolverView.PHP_EOL;
    echo ($okNotExpired ? 'PASS' : 'FAIL').' preview_'.$case['slag'].PHP_EOL;

    if (! $okView || ! $okNotExpired) {
        $failed++;
    }
}

echo 'preview_url_has_flag='.(str_contains(PortalProfilePreview::previewUrl(
    Profile::query()->where('client_id', $member->client_id)->first()
), 'portal_preview=1') ? 'yes' : 'no').PHP_EOL;

exit($failed > 0 ? 2 : 0);
