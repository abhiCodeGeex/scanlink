<?php

/**
 * Verify Form Builder iframe embed (live location pattern).
 */

use App\Enums\UserType;
use App\Filament\Portal\Pages\FormBuilder;
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

$profile = app(ProfileDraftSlotService::class)
    ->claimForCreate((int) $member->client_id, 'location', (int) $member->id);
$ok('Claimed location profile #'.$profile->id);

// Edit page must expose iframe embed URL in preview data
$edit = Livewire::actingAs($user)->test(EditProfile::class, ['record' => $profile->id]);
$data = $edit->instance()->legacyPreviewData();
$embedUrl = $data['formBuilderEmbedUrl'] ?? null;

if (is_string($embedUrl) && str_contains($embedUrl, 'embed=1') && str_contains($embedUrl, (string) $profile->id)) {
    $ok('Edit legacyPreviewData has embed URL');
} else {
    $bad('Missing embed URL in legacyPreviewData: '.json_encode($embedUrl));
}

$html = $edit->html();
str_contains($html, 'iframe_frm_builder')
    ? $ok('Edit HTML contains Form Builder iframe')
    : $bad('Edit HTML missing iframe_frm_builder');

str_contains($html, 'form-builder') && str_contains($html, 'embed=1')
    ? $ok('Iframe points at form-builder?embed=1')
    : $bad('Iframe src missing form-builder embed');

// FormBuilder embed Livewire page
$fb = Livewire::actingAs($user)
    ->withQueryParams(['profile' => $profile->id, 'embed' => '1'])
    ->test(FormBuilder::class);

$fb->assertSet('selectedProfileId', $profile->id);
$fb->assertSet('isEmbed', true);
$ok('FormBuilder embed bound to profile');

$fbHtml = $fb->html();
foreach ([
    'Form Name',
    'Recipients Email',
    'CREATE YOUR FORM HERE',
    'fb-canvas-drop-zone',
    'draggable="true"',
] as $needle) {
    str_contains($fbHtml, $needle)
        ? $ok("Embed HTML has {$needle}")
        : $bad("Embed HTML missing {$needle}");
}

str_contains($fbHtml, 'Save the profile first')
    ? $bad('Embed shows save-first')
    : $ok('No save-first on embed');

$before = FormBuilderQuestion::query()->where('profile_id', $profile->id)->count();
$fb->call('quickAdd', 13);
$after = FormBuilderQuestion::query()->where('profile_id', $profile->id)->count();
($after === $before + 1)
    ? $ok("quickAdd Line Divider ({$before}→{$after})")
    : $bad("quickAdd failed ({$before}→{$after})");

$fb->call('quickAdd', 1)->assertSet('composingTypeId', 1);
$ok('quickAdd Text Field opens composer');

$fb->set('composerQuestionText', 'Iframe regression text')
    ->call('saveQuestion')
    ->assertSet('composingTypeId', null);

FormBuilderQuestion::query()
    ->where('profile_id', $profile->id)
    ->where('question_text', 'Iframe regression text')
    ->exists()
    ? $ok('Composer save persisted')
    : $bad('Composer save missing');

echo "\n";
echo $fail === 0 ? "FORM BUILDER IFRAME REGRESSION: PASS\n" : "FORM BUILDER IFRAME REGRESSION: FAIL ({$fail})\n";
exit($fail === 0 ? 0 : 1);
