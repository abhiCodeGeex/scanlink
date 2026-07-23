<?php

/**
 * End-to-end plant & equipment template parity check against local MySQL.
 * Matches legacy Kohana plant/edit.php (Words labels + shared shell + Form Builder).
 *
 * Usage: php scripts/verify-plant-parity.php
 */

use App\Enums\UserType;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\ProfileContact;
use App\Models\User;
use App\Models\Weblink;
use App\Services\ProfileMediaService;
use App\Services\ProfileQrService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$failures = [];
$ok = function (string $label) use (&$failures): void {
    echo "[OK] {$label}\n";
};
$fail = function (string $label, string $detail = '') use (&$failures): void {
    $failures[] = $label.($detail !== '' ? " — {$detail}" : '');
    echo "[FAIL] {$label}".($detail !== '' ? " — {$detail}" : '')."\n";
};

$typeId = EquipmentType::query()->where('slag', 'plant')->value('id');
if (! $typeId) {
    fwrite(STDERR, "No plant equipment type.\n");
    exit(1);
}

$member = ClientUser::query()
    ->where('email', 'portal-test@scanlink.local')
    ->where('status', true)
    ->first()
    ?? ClientUser::query()->where('status', true)->orderByDesc('role')->first();

if (! $member) {
    fwrite(STDERR, "No portal member available.\n");
    exit(1);
}

$user = User::query()->find($member->auth_user_id);
if ($user) {
    $user->forceFill(['user_type' => UserType::Portal, 'admin_role' => null])->save();
    Auth::login($user);
}

$codeName = 'PARITY-PLT-'.date('YmdHis');

$profile = new Profile;
$profile->forceFill([
    'client_id' => $member->client_id,
    'user_id' => $member->id,
    'type_id' => $typeId,
    'code_profile_name' => $codeName,
    'name' => 'Caterpillar 320D',
    'identification' => 'PLT-001',
    'serial_no' => 'SN-998877',
    'description' => 'Plant parity description',
    'notes' => 'Plant parity note',
    'enable_data_collection' => true,
    'set_up_compulsory' => true,
    'data_collection_name' => true,
    'data_collection_surname' => true,
    'data_collection_email' => true,
    'data_collection_mobile' => true,
    'data_collection_content' => 'Please leave your details',
    'form_is_enable' => true,
    'enable_form_analytics' => false,
    'form_submission_format' => 1,
    'code_type' => 0,
    'show_header' => true,
    'protect' => true,
    'password' => 'Secret123',
    'display_share_link' => true,
    'deleted' => false,
    'update_or_not' => true,
    'expired_at' => now()->addYear(),
    'activation_start_date' => now()->toDateString(),
    'activation_end_date' => now()->addMonth()->toDateString(),
]);
$profile->save();

$ok('Created plant profile #'.$profile->id);

Weblink::query()->create([
    'profile_id' => $profile->id,
    'link_button' => 1,
    'link_button_text' => 'Visit site',
    'link_button_url' => 'https://example.com/plant',
    'link_button_color' => '008901',
    'link_button_align' => 'center',
]);

ProfileContact::query()->create([
    'profile_id' => $profile->id,
    'name_company' => 'Plant Contact Co',
    'telephone' => '0364314025',
    'datestamp' => now(),
]);

app(ProfileMediaService::class)->syncUploads($profile, [
    'video_titles' => [
        ['title' => 'Welcome video', 'video_name' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
    ],
]);

try {
    app(ProfileQrService::class)->generateFor($profile->refresh());
    $ok('QR generated');
} catch (Throwable $e) {
    $fail('QR generated', $e->getMessage());
}

$profile->refresh()->load(['weblinks', 'contacts', 'videos', 'qrImage', 'equipmentType', 'checklistItems']);

$checks = [
    'type is plant' => $profile->typeSlug() === 'plant',
    'code_profile_name' => $profile->code_profile_name === $codeName,
    'make / model' => $profile->name === 'Caterpillar 320D',
    'identification' => $profile->identification === 'PLT-001',
    'serial_no' => $profile->serial_no === 'SN-998877',
    'description' => $profile->description === 'Plant parity description',
    'notes' => $profile->notes === 'Plant parity note',
    'data collection enabled' => (bool) $profile->enable_data_collection,
    'compulsory' => (bool) $profile->set_up_compulsory,
    'dc name' => (bool) $profile->data_collection_name,
    'dc surname' => (bool) $profile->data_collection_surname,
    'dc email' => (bool) $profile->data_collection_email,
    'dc mobile' => (bool) $profile->data_collection_mobile,
    'dc content' => $profile->data_collection_content === 'Please leave your details',
    'form enable' => (bool) $profile->form_is_enable,
    'form submission format' => (int) $profile->form_submission_format === 1,
    'show_header' => (bool) $profile->show_header,
    'protect' => (bool) $profile->protect,
    'password set' => filled($profile->password),
    'display_share_link' => (bool) $profile->display_share_link,
    'weblink saved' => $profile->weblinks->contains(fn ($w) => $w->link_button_text === 'Visit site' && $w->link_button_align === 'center'),
    'contact saved' => $profile->contacts->contains(fn ($c) => $c->telephone === '0364314025'),
    'video saved' => $profile->videos->isNotEmpty(),
    'video youtube id parsed' => $profile->videos->contains(fn ($v) => $v->video_name === 'dQw4w9WgXcQ'),
    'no checklist required for plant' => $profile->checklistItems->isEmpty(),
];

foreach ($checks as $label => $pass) {
    $pass ? $ok($label) : $fail($label);
}

$schemaPath = app_path('Filament/Portal/Resources/Profiles/Schemas/PortalProfileForm.php');
$schema = file_get_contents($schemaPath) ?: '';
$typeSchema = file_get_contents(app_path('Filament/Resources/Profiles/Schemas/ProfileFormSchema.php')) ?: '';

$formPresence = [
    'section Code Profile Name' => str_contains($schema, "Section::make('Code Profile Name')"),
    'section Logo' => str_contains($schema, "Section::make('Logo')") || str_contains($schema, "heading('Logo')"),
    'section Videos' => str_contains($schema, "heading('Videos')") || str_contains($schema, "Section::make('Videos')"),
    'section Words' => str_contains($schema, "heading('Words')") || str_contains($schema, "Section::make('Words')"),
    'section Pictures' => str_contains($schema, "heading('Pictures')") || str_contains($schema, "Section::make('Pictures')"),
    'section Documents' => str_contains($schema, "heading('Documents')") || str_contains($schema, "Section::make('Documents')"),
    'section Web Link' => str_contains($schema, "heading('Web Link')") || str_contains($schema, "Section::make('Web Link')"),
    'section Data Collection' => str_contains($schema, "heading('Data Collection')") || str_contains($schema, "Section::make('Data Collection')"),
    'section Set Code Type' => str_contains($schema, "heading('Set Code Type')") || str_contains($schema, "Section::make('Set Code Type')"),
    'section Header' => str_contains($schema, "heading('Header')") || str_contains($schema, "Section::make('Header')"),
    'section User Access Security' => str_contains($schema, "heading('User Access Security')") || str_contains($schema, "Section::make('User Access Security')"),
    'section Share' => str_contains($schema, "heading('Share')") || str_contains($schema, "Section::make('Share')"),
    'no Checklist section' => ! str_contains($schema, "Section::make('Checklist items')")
        && ! str_contains($schema, "Section::make('Checklist')"),
    'password always visible' => ! preg_match(
        "/TextInput::make\\('password'\\)[\\s\\S]*?->visible\\(/",
        $schema
    ),
    'contacts for plant' => str_contains($schema, "'plant'") && (
        str_contains($schema, 'contactsFields') || str_contains($schema, "label('CONTACT:')")
    ),
    'plant Make / Model label' => str_contains($typeSchema, "label('Make / Model:')"),
    'plant Identification label' => str_contains($typeSchema, "label('Identification:')"),
    'plant Serial No. label' => str_contains($typeSchema, "label('Serial No.:')"),
    'plant Note label (singular)' => preg_match(
        "/'plant'\\s*=>\\s*\\[[\\s\\S]*?label\\('Note:'\\)/",
        $typeSchema
    ) === 1,
    'plant Description label' => preg_match(
        "/'plant'\\s*=>\\s*\\[[\\s\\S]*?label\\('Description:'\\)/",
        $typeSchema
    ) === 1,
    'sidebar form builder controls' => str_contains(
        file_get_contents(resource_path('views/filament/portal/profiles/legacy-preview-sidebar.blade.php')) ?: '',
        'Use an existing form'
    ) && str_contains(
        file_get_contents(resource_path('views/filament/portal/profiles/legacy-preview-sidebar.blade.php')) ?: '',
        'data.form_is_enable'
    ),
    'sidebar form builder iframe' => str_contains(
        file_get_contents(resource_path('views/filament/portal/profiles/legacy-preview-sidebar.blade.php')) ?: '',
        'iframe_frm_builder'
    ) && str_contains(
        file_get_contents(resource_path('views/filament/portal/profiles/legacy-preview-sidebar.blade.php')) ?: '',
        'formBuilderEmbedUrl'
    ),
];

foreach ($formPresence as $label => $pass) {
    $pass ? $ok($label) : $fail($label);
}

// Live plant #564 (NWIT) should resolve as plant in portal.
$live = Profile::query()->find(564);
if ($live) {
    ($live->typeSlug() === 'plant')
        ? $ok('live profile #564 is plant')
        : $fail('live profile #564 is plant', (string) $live->typeSlug());
} else {
    $fail('live profile #564 exists');
}

$profile->forceFill(['deleted' => true, 'code_profile_name' => $codeName.'-ARCHIVED'])->save();
$ok('Archived parity test profile');

echo "\nPortal edit URLs:\n";
echo "  http://localhost:8000/portal/profiles/564/edit  (legacy plant/edit/564)\n";
echo "  http://localhost:8000/portal/profiles/6575/edit  (your plant profile)\n";

if ($failures !== []) {
    echo "\nPLANT PARITY: FAIL (".count($failures)." issues)\n";
    foreach ($failures as $item) {
        echo " - {$item}\n";
    }
    exit(1);
}

echo "\nPLANT PARITY: PASS\n";
