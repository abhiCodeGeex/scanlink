<?php

/**
 * Remaining template Words / section parity checks.
 * Usage: php scripts/verify-remaining-templates-parity.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$failures = [];
$ok = fn (string $l) => print("[OK] {$l}\n");
$fail = function (string $l, string $d = '') use (&$failures): void {
    $failures[] = $l;
    echo "[FAIL] {$l}".($d !== '' ? " — {$d}" : '')."\n";
};

$schema = file_get_contents(app_path('Filament/Resources/Profiles/Schemas/ProfileFormSchema.php')) ?: '';
$portal = file_get_contents(app_path('Filament/Portal/Resources/Profiles/Schemas/PortalProfileForm.php')) ?: '';
$model = file_get_contents(app_path('Models/Profile.php')) ?: '';

$checks = [
    'product Product name:' => str_contains($schema, "label('Product name:')")
        && str_contains($schema, 'Legacy product/edit.php — no Identification/Serial'),
    'procedure Title:' => str_contains($schema, "label('Title:')"),
    'misc Name: unlabeled desc' => str_contains($schema, "'misc' =>")
        && preg_match("/'misc'\\s*=>\\s*\\[[\\s\\S]*?hiddenLabel\\(\\)/", $schema)
        && preg_match("/'misc'\\s*=>\\s*\\[[\\s\\S]*?sl-ckeditor/", $schema),
    'code Application via name' => preg_match("/'code'\\s*=>\\s*\\[[\\s\\S]*?TextInput::make\\('name'\\)->label\\('Application:'\\)/", $schema) === 1,
    'code URL label' => str_contains($schema, 'Enter the Web page address(URL) here:'),
    'code no bridge graphic' => ! preg_match("/'code'\\s*=>\\s*\\[[\\s\\S]*?activate_bridge_graphic/", $schema),
    'exhibit name2/description2' => str_contains($schema, "Textarea::make('description')")
        && str_contains($portal, "heading('Words #2')")
        && str_contains($portal, "TextInput::make('name2')")
        && str_contains($portal, "Textarea::make('description2')"),
    'portal exhibit Logo #2' => str_contains($portal, "heading('Logo #2')"),
    'portal exhibit Videos #2' => str_contains($portal, "heading('Videos #2')"),
    'portal exhibit Pictures #2' => str_contains($portal, "heading('Pictures #2')"),
    'voc first/last name' => str_contains($schema, "voc_first_name") && str_contains($schema, "voc_last_name"),
    'voc employer fields' => str_contains($schema, 'voc_employer') && str_contains($schema, 'voc_emp_phone'),
    'fillable name2' => str_contains($model, "'name2'"),
    'fillable voc_first_name' => str_contains($model, "'voc_first_name'"),
    'fillable voc_title_bar_enable' => str_contains($model, "'voc_title_bar_enable'"),
    'portal code embeds data collection' => str_contains($portal, 'legacyCodeUrlFields')
        && str_contains($portal, "=== 'code'"),
    'portal code hides separate data collection' => preg_match("/Data Collection[\\s\\S]*?\\['survey', 'code'\\]/", $portal) === 1
        || str_contains($portal, "['survey', 'code']"),
    'portal code hides logo' => str_contains($portal, "!== 'code'"),
    'portal code hides profile name' => preg_match("/Code Profile Name[\\s\\S]*?!== 'code'/", $portal) === 1,
    'portal voc Profile Information' => str_contains($portal, "heading('Profile Information')"),
    'portal voc Title Bar' => str_contains($portal, "heading('Title Bar')"),
    'portal voc Email Notification' => str_contains($portal, 'Email Notification Settings'),
    'portal voc Additional User Access' => str_contains($portal, 'Additional User Access Login'),
    'portal voc Document Upload' => str_contains($portal, "heading('Document Upload')"),
    'portal voc Profile Picture' => str_contains($portal, "heading('Profile Picture')"),
    'portal voc hides data collection' => preg_match("/Data Collection[\\s\\S]*?\\['survey', 'code', 'voc'\\]/", $portal) === 1
        || str_contains($portal, "['survey', 'code', 'voc']"),
    'portal survey no Form Name section' => ! str_contains($portal, "label('Form Name')")
        && str_contains($portal, 'no separate "Form Name" block'),
    'portal survey left form builder' => str_contains(
        (string) file_get_contents(resource_path('views/filament/portal/profiles/legacy-profile-page.blade.php')),
        'sl-survey-form-builder'
    ),
    'portal no asset contacts' => str_contains($portal, "'location', 'plant', 'product', 'procedure'"),
];

foreach ($checks as $label => $pass) {
    $pass ? $ok($label) : $fail($label);
}

echo "\n";
if ($failures === []) {
    echo "REMAINING TEMPLATES PARITY: PASS\n";
    exit(0);
}

echo 'REMAINING TEMPLATES PARITY: FAIL ('.count($failures)." issues)\n";
exit(1);
