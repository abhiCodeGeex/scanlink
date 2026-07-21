<?php

/**
 * Full-stack browser-equivalent regression for location Form Builder.
 * Uses Laravel HTTP kernel + Livewire (same code paths as the browser).
 *
 * Usage: php scripts/browser-location-form-builder.php
 */

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$fail = 0;
$ok = fn (string $m) => print("[OK] {$m}\n");
$bad = function (string $m) use (&$fail) {
    $fail++;
    echo "[FAIL] {$m}\n";
};

$member = ClientUser::query()->where('email', 'portal-test@scanlink.local')->firstOrFail();
$user = User::query()->findOrFail($member->auth_user_id);
$user->forceFill(['user_type' => UserType::Portal, 'admin_role' => null])->save();
Auth::login($user);
Filament::setCurrentPanel(Filament::getPanel('portal'));

$locationTypeId = (int) EquipmentType::query()->where('slag', 'location')->value('id');

// --- A) Real browser entry: HTTP GET /portal/profiles/create?type=location ---
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$app['session']->start();
Auth::login($user);

$createReq = Request::create('/portal/profiles/create?type=location', 'GET');
$createReq->setLaravelSession($app['session']->driver());
$createReq->setUserResolver(fn () => Auth::user());
$app->instance('request', $createReq);

$createRes = $kernel->handle($createReq);
$createStatus = $createRes->getStatusCode();
$createLoc = $createRes->headers->get('Location');
$kernel->terminate($createReq, $createRes);

if (in_array($createStatus, [301, 302, 303, 307, 308], true)
    && is_string($createLoc)
    && preg_match('#/portal/profiles/(\d+)/edit#', $createLoc, $m)) {
    $profileId = (int) $m[1];
    $ok("HTTP create?type=location → {$createStatus} → edit #{$profileId}");
} else {
    $bad("HTTP create did not redirect to edit (status={$createStatus} loc=".($createLoc ?: 'none').')');
    $slot = app(\App\Services\ProfileDraftSlotService::class)
        ->claimForCreate((int) $member->client_id, 'location', (int) $member->id);
    $profileId = (int) ($slot?->id ?? 0);
}

if (! $profileId) {
    echo "BROWSER LOCATION FORM BUILDER: FAIL\n";
    exit(1);
}

$profile = Profile::query()->findOrFail($profileId);
if ((int) $profile->type_id !== $locationTypeId) {
    $bad("Profile type_id={$profile->type_id} expected location={$locationTypeId}");
} else {
    $ok('Claimed profile is location type');
}

if (! $profile->update_or_not) {
    $bad('Profile still open slot (update_or_not=0) after claim');
} else {
    $ok('Profile marked claimed (update_or_not=1)');
}

// --- B) HTTP kernel GET edit page (authenticated) ---
$editReq = Request::create('/portal/profiles/'.$profileId.'/edit', 'GET');
$editReq->setLaravelSession($app['session']->driver());
Auth::login($user);
$editReq->setUserResolver(fn () => Auth::user());
$app->instance('request', $editReq);
$editRes = $kernel->handle($editReq);
$editHtml = (string) $editRes->getContent();
$status = $editRes->getStatusCode();
$kernel->terminate($editReq, $editRes);

if ($status === 200 && str_contains($editHtml, 'CREATE YOUR FORM HERE')) {
    $ok("HTTP edit page 200 with Form Builder canvas");
} elseif (in_array($status, [301, 302], true)) {
    $ok('HTTP edit redirected (auth/login flow) Location='.$editRes->headers->get('Location'));
} else {
    $bad("HTTP edit unexpected status={$status} (canvas=".((str_contains($editHtml, 'CREATE YOUR FORM HERE')) ? 'yes' : 'no').')');
    file_put_contents(storage_path('logs/browser-edit.html'), $editHtml);
}

// --- C) Drag/drop equivalent: openFormBuilderTool on EditProfile ---
$before = FormBuilderQuestion::query()->where('profile_id', $profileId)->count();

$edit = Livewire::actingAs($user)
    ->test(EditProfile::class, ['record' => $profileId]);

$html = $edit->html();
str_contains($html, 'Save the profile first to add form elements')
    ? $bad('Edit HTML contains save-first text')
    : $ok('Edit Livewire HTML has no save-first text');

$edit->call('openFormBuilderTool', 13);
$after = FormBuilderQuestion::query()->where('profile_id', $profileId)->count();
($after === $before + 1)
    ? $ok("Drop Line Divider persisted ({$before}→{$after})")
    : $bad("Drop Line Divider failed ({$before}→{$after})");

$edit->call('openFormBuilderTool', 15)
    ->assertSet('fbComposingTypeId', 15)
    ->set('fbComposerText', 'Browser regression comments')
    ->call('saveFormBuilderQuestion');

FormBuilderQuestion::query()
    ->where('profile_id', $profileId)
    ->where('question_text', 'Browser regression comments')
    ->exists()
    ? $ok('Comments composer saved')
    : $bad('Comments composer missing in DB');

$notifications = json_encode(session('filament.notifications') ?? []);
str_contains((string) $notifications, 'Save the profile first')
    ? $bad('Save-first notification fired')
    : $ok('No save-first notification');

echo "\n";
if ($fail === 0) {
    echo "BROWSER LOCATION FORM BUILDER: PASS\n";
    echo "URL: /portal/profiles/{$profileId}/edit\n";
    exit(0);
}

echo "BROWSER LOCATION FORM BUILDER: FAIL ({$fail} issues)\n";
exit(1);
