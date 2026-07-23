<?php

/**
 * Person/Business (asset) template parity checks.
 * Matches legacy Kohana asset/edit.php Words section.
 *
 * Usage: php scripts/verify-asset-parity.php
 */

use App\Models\EquipmentType;
use App\Models\Profile;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$failures = [];
$ok = function (string $label) use (&$failures): void {
    echo "[OK] {$label}\n";
};
$fail = function (string $label, string $detail = '') use (&$failures): void {
    $failures[] = $label.($detail !== '' ? " — {$detail}" : '');
    echo "[FAIL] {$label}".($detail !== '' ? " — {$detail}" : '')."\n";
};

$typeId = EquipmentType::query()->where('slag', 'asset')->value('id');
$typeId ? $ok('asset type exists') : $fail('asset type exists');

$schema = file_get_contents(app_path('Filament/Resources/Profiles/Schemas/ProfileFormSchema.php')) ?: '';
$portal = file_get_contents(app_path('Filament/Portal/Resources/Profiles/Schemas/PortalProfileForm.php')) ?: '';
$model = file_get_contents(app_path('Models/Profile.php')) ?: '';

$checks = [
    'show_name checkbox' => str_contains($schema, "Checkbox::make('show_name')") && str_contains($schema, "label('Name:')"),
    'show_description checkbox' => str_contains($schema, "Checkbox::make('show_description')"),
    'show_address checkbox' => str_contains($schema, "Checkbox::make('show_address')"),
    'show_telephone checkbox' => str_contains($schema, "Checkbox::make('show_telephone')"),
    'show_mobile checkbox' => str_contains($schema, "Checkbox::make('show_mobile')"),
    'show_email checkbox' => str_contains($schema, "Checkbox::make('show_email')"),
    'show_url checkbox (Website)' => str_contains($schema, "Checkbox::make('show_url')") && str_contains($schema, "label('Website:')"),
    'mobile field' => preg_match("/'asset'\\s*=>\\s*\\[[\\s\\S]*?TextInput::make\\('mobile'\\)/", $schema) === 1,
    'email field' => preg_match("/'asset'\\s*=>\\s*\\[[\\s\\S]*?TextInput::make\\('email'\\)/", $schema) === 1,
    'url/website field' => preg_match("/'asset'\\s*=>\\s*\\[[\\s\\S]*?TextInput::make\\('url'\\)/", $schema) === 1,
    'tick heading hint' => str_contains($schema, 'Tick a box to display the heading on mobile'),
    'view map for asset' => str_contains($schema, 'asset_view_map_link'),
    'no contacts for asset' => ! preg_match("/contactsFields[\\s\\S]*'asset'/", $portal)
        && str_contains($portal, "'location', 'plant', 'product', 'procedure'"),
    'model fillable show flags' => str_contains($model, "'show_name'") && str_contains($model, "'show_url'") && str_contains($model, "'mobile'"),
    'model casts show flags' => str_contains($model, "'show_name' =>") && str_contains($model, "'show_url' =>"),
];

foreach ($checks as $label => $pass) {
    $pass ? $ok($label) : $fail($label);
}

$live = Profile::query()->find(5306);
if ($live) {
    ($live->typeSlug() === 'asset') ? $ok('live #5306 is asset') : $fail('live #5306 is asset', (string) $live->typeSlug());
} else {
    $fail('live #5306 exists');
}

$local = Profile::query()->find(6579);
if ($local) {
    ($local->typeSlug() === 'asset') ? $ok('local #6579 is asset') : $fail('local #6579 is asset', (string) $local->typeSlug());
} else {
    $fail('local #6579 exists');
}

echo "\nPortal edit: http://localhost:8000/portal/profiles/6579/edit\n";
echo "Legacy ref: https://scanlink.com.au/asset/edit/5306\n";

if ($failures !== []) {
    echo "\nASSET PARITY: FAIL (".count($failures)." issues)\n";
    foreach ($failures as $item) {
        echo " - {$item}\n";
    }
    exit(1);
}

echo "\nASSET PARITY: PASS\n";
