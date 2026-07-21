<?php

/**
 * End-to-end location template parity check against local MySQL.
 * Matches legacy Kohana location/index.php + edit.php (as-is).
 *
 * Usage: php scripts/verify-location-parity.php
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

$typeId = EquipmentType::query()->where('slag', 'location')->value('id');
if (! $typeId) {
    fwrite(STDERR, "No location equipment type.\n");
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

$codeName = 'PARITY-LOC-'.date('YmdHis');

$profile = new Profile;
$profile->forceFill([
    'client_id' => $member->client_id,
    'user_id' => $member->id,
    'type_id' => $typeId,
    'code_profile_name' => $codeName,
    'name' => 'Parity Lobby',
    'address' => '100 Test St, Hobart TAS',
    'description' => 'Location parity description',
    'notes' => 'Location parity notes',
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

$ok('Created location profile #'.$profile->id);

Weblink::query()->create([
    'profile_id' => $profile->id,
    'link_button' => 1,
    'link_button_text' => 'Visit site',
    'link_button_url' => 'https://example.com/location',
    'link_button_color' => '008901',
    'link_button_align' => 'center',
]);

ProfileContact::query()->create([
    'profile_id' => $profile->id,
    'name_company' => 'Parity Contact Co',
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

$profile->refresh()->load(['weblinks', 'contacts', 'videos', 'qrImage', 'equipmentType']);

$checks = [
    'type is location' => $profile->typeSlug() === 'location',
    'code_profile_name' => $profile->code_profile_name === $codeName,
    'location name' => $profile->name === 'Parity Lobby',
    'address' => str_contains((string) $profile->address, '100 Test St'),
    'description' => $profile->description === 'Location parity description',
    'notes' => $profile->notes === 'Location parity notes',
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
];

foreach ($checks as $label => $pass) {
    $pass ? $ok($label) : $fail($label);
}

$schemaPath = app_path('Filament/Portal/Resources/Profiles/Schemas/PortalProfileForm.php');
$schema = file_get_contents($schemaPath) ?: '';
$typeSchema = file_get_contents(app_path('Filament/Resources/Profiles/Schemas/ProfileFormSchema.php')) ?: '';

$formPresence = [
    'section Code Profile Name' => str_contains($schema, "Section::make('Code Profile Name')"),
    'section Logo' => str_contains($schema, "Section::make('Logo')"),
    'section Videos' => str_contains($schema, "Section::make('Videos')"),
    'section Words' => str_contains($schema, "Section::make('Words')"),
    'section Pictures' => str_contains($schema, "Section::make('Pictures')"),
    'section Documents' => str_contains($schema, "Section::make('Documents')"),
    'section Web Link' => str_contains($schema, "Section::make('Web Link')"),
    'section Data Collection' => str_contains($schema, "Section::make('Data Collection')"),
    'section Set Code Type' => str_contains($schema, "Section::make('Set Code Type')"),
    'section Header' => str_contains($schema, "Section::make('Header')"),
    'section User Access Security' => str_contains($schema, "Section::make('User Access Security')"),
    'section Share' => str_contains($schema, "Section::make('Share')"),
    'contacts inside Words' => str_contains($schema, 'contactsFields') || str_contains($schema, "label('CONTACT:')"),
    'picture text footer' => str_contains($schema, "label('Text Footer')"),
    'document title/align/color' => str_contains($schema, "label('Title')") && str_contains($schema, "label('Text Alignment')") && str_contains($schema, "label('Button Color')"),
    'weblink align' => str_contains($schema, "label('Button Text Alignment:')"),
    'form submission format' => str_contains($schema, "form_submission_format"),
    'data collection yes/no' => str_contains($schema, "'Enable Data Collection Pop Up Window:'") && str_contains($schema, "'Yes'") && str_contains($schema, "'No'"),
    'password protect yes/no' => str_contains($schema, "'Password protect?:'"),
    'videos dehydrated for save' => ! str_contains($schema, "Repeater::make('video_titles')")
        || ! preg_match("/Repeater::make\\('video_titles'\\)[\\s\\S]*?->dehydrated\\(false\\)/", $schema),
    'location name field' => str_contains($typeSchema, "label('Location name:')"),
    'view map helper' => str_contains($typeSchema, 'view_map_link') || str_contains($typeSchema, 'View Map'),
    'no map link field' => ! str_contains($typeSchema, "label('Map Link')"),
    'no gps field' => ! str_contains($typeSchema, "label('GPS Coordinates')"),
    'edit fills logo/videos' => str_contains(
        file_get_contents(app_path('Filament/Portal/Resources/Profiles/Pages/EditProfile.php')) ?: '',
        'mutateFormDataBeforeFill'
    ),
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
    'embed form name/recipients' => str_contains(
        file_get_contents(resource_path('views/filament/portal/pages/form-builder-embed.blade.php')) ?: '',
        'Form Name'
    ) && str_contains(
        file_get_contents(resource_path('views/filament/portal/pages/form-builder-embed.blade.php')) ?: '',
        'Recipients Email'
    ) && str_contains(
        file_get_contents(resource_path('views/filament/portal/pages/form-builder-embed.blade.php')) ?: '',
        'CREATE YOUR FORM HERE'
    ) && str_contains(
        file_get_contents(resource_path('views/filament/portal/pages/form-builder-embed.blade.php')) ?: '',
        'quickAdd'
    ),
    'embed drag drop canvas' => str_contains(
        file_get_contents(resource_path('views/filament/portal/pages/form-builder-embed.blade.php')) ?: '',
        'fb-canvas-drop-zone'
    ) && str_contains(
        file_get_contents(resource_path('views/filament/portal/pages/form-builder-embed.blade.php')) ?: '',
        'draggable="true"'
    ),
];

foreach ($formPresence as $label => $pass) {
    $pass ? $ok('form: '.$label) : $fail('form: '.$label);
}

$profile->forceFill(['deleted' => true, 'code_profile_name' => $codeName.'-ARCHIVED'])->save();
$ok('Archived parity test profile');

echo "\n";
if ($failures === []) {
    echo 'LOCATION PARITY: PASS ('.count($checks).' data + '.count($formPresence)." form checks)\n";
    exit(0);
}

echo 'LOCATION PARITY: FAIL ('.count($failures)." issues)\n";
foreach ($failures as $item) {
    echo " - {$item}\n";
}
exit(1);
