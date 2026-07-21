<?php

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
$cookieValue = $encrypter->encrypt(CookieValuePrefix::create($sessionName, $encrypter->getKey()).$sessionId, false);
$cookieHeader = $sessionName.'='.urlencode($cookieValue);

// claim via create redirect
$ch = curl_init($base.'/portal/profiles/create?type=location');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_HTTPHEADER => ['Cookie: '.$cookieHeader]]);
$raw = (string) curl_exec($ch);
curl_close($ch);
preg_match('#/portal/profiles/(\d+)/edit#', $raw, $m);
$profileId = $m[1] ?? null;
if (! $profileId) { echo "no profile\n"; exit(1); }

$url = $base.'/portal/form-builder?profile='.$profileId.'&embed=1';
$ch = curl_init($url);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTPHEADER => ['Cookie: '.$cookieHeader]]);
$html = (string) curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

file_put_contents(storage_path('logs/fb-embed-full.html'), $html);
echo "status={$code} len=".strlen($html)."\n";
echo str_contains($html, '__slFbDndBound') ? "HAS layout DnD script\n" : "MISSING layout DnD script\n";
echo str_contains($html, 'ondragstart=') ? "HAS ondragstart\n" : "MISSING ondragstart\n";
echo str_contains($html, 'wire:id') ? "HAS wire:id\n" : "MISSING wire:id\n";
echo str_contains($html, '@filamentScripts') || str_contains($html, 'livewire') ? "HAS livewire assets\n" : "MISSING livewire\n";

// Structure around drop zone
if (preg_match('/.{0,200}id="fb-canvas-drop-zone".{0,200}/s', $html, $mm)) {
    echo "DROP CONTEXT: ".$mm[0]."\n";
}

// Is drop zone inside wire:id?
$pos = strpos($html, 'id="fb-canvas-drop-zone"');
$before = substr($html, max(0, $pos - 5000), 5000);
$wireOpen = substr_count($before, 'wire:id');
echo "wire:id occurrences before drop zone in last 5k: {$wireOpen}\n";

if (preg_match_all('/wire:id="([^"]+)"/', $html, $wm)) {
    echo "wire ids: ".implode(', ', $wm[1])."\n";
}

// Check if Filament panel layout used instead of embed layout
echo str_contains($html, 'fi-body') || str_contains($html, 'fi-main') ? "USES FILAMENT PANEL CHROME\n" : "no filament panel chrome\n";
echo str_contains($html, 'form-builder-embed') || str_contains($html, 'fb-root') ? "has fb-root\n" : "no fb-root\n";
