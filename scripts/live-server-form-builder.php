<?php

/**
 * Hit live artisan serve with a properly encrypted session cookie.
 */

use App\Enums\UserType;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$base = 'http://127.0.0.1:8000';

$member = ClientUser::query()->where('email', 'portal-test@scanlink.local')->firstOrFail();
$user = User::query()->findOrFail($member->auth_user_id);
$user->forceFill(['user_type' => UserType::Portal, 'admin_role' => null])->save();

$app['session']->start();
Auth::login($user);
$sessionName = (string) config('session.cookie');
$sessionId = $app['session']->getId();
$app['session']->save();

$encrypter = $app['encrypter'];
$payload = CookieValuePrefix::create($sessionName, $encrypter->getKey()).$sessionId;
$cookieValue = $encrypter->encrypt($payload, false);
$cookieHeader = $sessionName.'='.urlencode($cookieValue);

$fail = 0;
$ok = fn (string $m) => print("[OK] {$m}\n");
$bad = function (string $m) use (&$fail) {
    $fail++;
    echo "[FAIL] {$m}\n";
};

echo "GET {$base}/portal/profiles/create?type=location\n";

$ch = curl_init($base.'/portal/profiles/create?type=location');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTPHEADER => ['Cookie: '.$cookieHeader, 'Accept: text/html'],
]);
$raw = (string) curl_exec($ch);
$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "status={$code}\n";

if (! preg_match('/^Location:\s*(.+)$/mi', $raw, $lm)) {
    echo "FAIL: no Location\n";
    exit(1);
}

$loc = trim($lm[1]);
echo "location={$loc}\n";

if (! preg_match('#/portal/profiles/(\d+)/edit#', $loc, $m)) {
    echo "FAIL: expected edit redirect\n";
    exit(1);
}

$profileId = $m[1];
$ok("create→edit #{$profileId}");

$editUrl = str_starts_with($loc, 'http') ? $loc : $base.$loc;
$ch = curl_init($editUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTPHEADER => ['Cookie: '.$cookieHeader, 'Accept: text/html'],
]);
$html = (string) curl_exec($ch);
$ecode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "edit status={$ecode}\n";

str_contains($html, 'iframe_frm_builder')
    ? $ok('Parent has Form Builder iframe')
    : $bad('Parent missing iframe_frm_builder');

str_contains($html, 'Save the profile first to add form elements')
    ? $bad('Parent shows save-first toast text')
    : $ok('No save-first on parent');

if (! preg_match('/id="iframe_frm_builder"[^>]*src="([^"]+)"/', $html, $im)
    && ! preg_match('/src="([^"]*form-builder[^"]*embed=1[^"]*)"/', $html, $im)) {
    $bad('Could not parse iframe src');
    echo "LIVE SERVER FORM BUILDER: FAIL\n";
    exit(1);
}

$embedUrl = html_entity_decode($im[1]);
if (! str_starts_with($embedUrl, 'http')) {
    $embedUrl = $base.(str_starts_with($embedUrl, '/') ? '' : '/').ltrim($embedUrl, '/');
}
$ok('Iframe src parsed');

$ch = curl_init($embedUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTPHEADER => ['Cookie: '.$cookieHeader, 'Accept: text/html'],
]);
$embedHtml = (string) curl_exec($ch);
$embCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "embed status={$embCode}\n";

foreach ([
    'Form Name' => 'Form Name',
    'CREATE YOUR FORM HERE' => 'CREATE YOUR FORM HERE',
    'drop canvas' => 'fb-canvas-drop-zone',
    'draggable tools' => 'draggable="true"',
    'ondragstart handlers' => 'ondragstart=',
    'document DnD binder' => '__slFbDndBound',
    'wire:click quickAdd' => 'quickAdd(',
] as $label => $needle) {
    str_contains($embedHtml, $needle) ? $ok("Embed has {$label}") : $bad("Embed missing {$label}");
}

str_contains($embedHtml, 'Save the profile first')
    ? $bad('Embed shows save-first')
    : $ok('Embed profile bound');

echo "\n";
echo $fail === 0 ? "LIVE SERVER FORM BUILDER: PASS\nOpen: {$editUrl}\n" : "LIVE SERVER FORM BUILDER: FAIL ({$fail})\n";
exit($fail === 0 ? 0 : 1);
