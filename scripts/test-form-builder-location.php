<?php

/**
 * Regression: location Form Builder requires profile_id (live Kohana behaviour)
 * and can add palette tools (drag/drop target) onto the canvas.
 *
 * Usage: php scripts/test-form-builder-location.php
 */

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\ClientUser;
use App\Models\FormBuilderQuestion;
use App\Models\User;
use App\Services\FormBuilderService;
use App\Services\ProfileDraftSlotService;
use Filament\Facades\Filament;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$fail = 0;
$ok = function (string $msg) {
    echo "[OK] {$msg}\n";
};
$bad = function (string $msg) use (&$fail) {
    $fail++;
    echo "[FAIL] {$msg}\n";
};

$member = ClientUser::query()->where('email', 'portal-test@scanlink.local')->first()
    ?? ClientUser::query()->where('status', true)->orderByDesc('role')->first();

if (! $member) {
    fwrite(STDERR, "No portal member\n");
    exit(1);
}

$user = User::query()->find($member->auth_user_id);
if ($user) {
    $user->forceFill(['user_type' => UserType::Portal, 'admin_role' => null])->save();
    Auth::login($user);
}

Filament::setCurrentPanel(Filament::getPanel('portal'));

$svc = app(ProfileDraftSlotService::class);

// 1) claimForCreate must always return a profile (even when open slots = 0)
$beforeOpen = $svc->slotCounts((int) $member->client_id)['open'];
$profile = $svc->claimForCreate((int) $member->client_id, 'location', (int) $member->id);

if ($profile && $profile->exists && (int) $profile->type_id > 0) {
    $ok("claimForCreate returned location profile #{$profile->id} (open_before={$beforeOpen})");
} else {
    $bad('claimForCreate did not return a usable location profile');
    echo "LOCATION FORM BUILDER REGRESSION: FAIL\n";
    exit(1);
}

// 2) Service-level add tool (what drop calls after Livewire)
$fb = app(FormBuilderService::class);
$beforeCount = FormBuilderQuestion::query()->where('profile_id', $profile->id)->count();

$q = $fb->saveQuestion($profile, [
    'question_type_id' => 1, // Text Field
    'question_text' => 'Regression Text Field '.date('His'),
    'is_mandatory' => true,
]);

$afterCount = FormBuilderQuestion::query()->where('profile_id', $profile->id)->count();
if ($afterCount === $beforeCount + 1 && $q->question_id) {
    $ok("FormBuilderService saved Text Field question #{$q->question_id}");
} else {
    $bad("FormBuilderService did not persist question (before={$beforeCount} after={$afterCount})");
}

// 3) Line divider quick-add (live drops straight onto canvas)
$fb->saveQuestion($profile, [
    'question_type_id' => 13,
    'question_text' => '—',
    'is_mandatory' => false,
]);
$ok('Line Divider saved');

// 4) Livewire EditProfile openFormBuilderTool (drag/drop / click handler)
try {
    Livewire::actingAs($user)
        ->test(EditProfile::class, ['record' => $profile->id])
        ->call('openFormBuilderTool', 15) // Comments
        ->assertSet('fbComposingTypeId', 15)
        ->set('fbComposerText', 'Regression comments')
        ->call('saveFormBuilderQuestion')
        ->assertSet('fbComposingTypeId', null);

    $hasComments = FormBuilderQuestion::query()
        ->where('profile_id', $profile->id)
        ->where('question_type_id', 15)
        ->where('question_text', 'Regression comments')
        ->exists();

    $hasComments ? $ok('Livewire EditProfile openFormBuilderTool + save works')
        : $bad('Livewire saved composer but question missing in DB');
} catch (Throwable $e) {
    $bad('Livewire EditProfile Form Builder failed: '.$e->getMessage());
}

// 5) Palette groups non-empty
$palette = $fb->paletteGroups();
$paletteOk = ($palette['question']?->count() ?? 0) + ($palette['format']?->count() ?? 0) + ($palette['answer']?->count() ?? 0) > 0;
$paletteOk ? $ok('Palette groups loaded from DB') : $bad('Palette groups empty');

// 6) Second claim still works (creates another open slot if needed)
$profile2 = $svc->claimForCreate((int) $member->client_id, 'location', (int) $member->id);
($profile2 && $profile2->id !== $profile->id)
    ? $ok("Second claimForCreate created profile #{$profile2->id}")
    : $bad('Second claimForCreate failed');

echo "\n";
if ($fail === 0) {
    echo "LOCATION FORM BUILDER REGRESSION: PASS\n";
    echo "Open edit URL: /portal/profiles/{$profile->id}/edit\n";
    exit(0);
}

echo "LOCATION FORM BUILDER REGRESSION: FAIL ({$fail} issues)\n";
exit(1);
