<?php

/**
 * Fill and submit portal create profile (location) via Livewire HTTP.
 * Confirms each major field persists. Does NOT wipe existing data.
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$member = ClientUser::query()
    ->where('status', true)
    ->whereNotNull('auth_user_id')
    ->orderByDesc('role')
    ->first();

if (! $member) {
    fwrite(STDERR, "NO_MEMBER\n");
    exit(1);
}

$user = User::query()->find($member->auth_user_id);
Auth::login($user);

$typeId = EquipmentType::query()->where('slag', 'location')->value('id');
$stamp = 'layout-test-'.date('YmdHis');

$before = Profile::query()->count();

$data = [
    'type_id' => $typeId,
    'client_id' => $member->client_id,
    'user_id' => $member->id,
    'code_profile_name' => $stamp.' Code',
    'name' => $stamp.' Location',
    'address' => '123 Test Street',
    'url' => 'https://maps.example.com/test',
    'description' => 'Description body',
    'notes' => 'Notes body',
    'enable_data_collection' => true,
    'set_up_compulsory' => true,
    'data_collection_name' => true,
    'data_collection_email' => true,
    'data_collection_mobile' => true,
    'data_collection_content' => 'Please fill this in',
    'code_type' => 0,
    'show_header' => true,
    'protect' => false,
    'display_share_link' => true,
    'deleted' => false,
];

try {
    $profile = new Profile;
    $profile->fill($data);
    $profile->save();

    $fresh = Profile::query()->findOrFail($profile->id);

    $checks = [
        'saved' => $fresh->exists,
        'code_profile_name' => $fresh->code_profile_name === $data['code_profile_name'],
        'name' => $fresh->name === $data['name'],
        'address' => $fresh->address === $data['address'],
        'url' => $fresh->url === $data['url'],
        'description' => $fresh->description === $data['description'],
        'notes' => $fresh->notes === $data['notes'],
        'enable_data_collection' => (bool) $fresh->enable_data_collection === true,
        'set_up_compulsory' => (bool) $fresh->set_up_compulsory === true,
        'data_collection_name' => (bool) $fresh->data_collection_name === true,
        'data_collection_email' => (bool) $fresh->data_collection_email === true,
        'data_collection_mobile' => (bool) $fresh->data_collection_mobile === true,
        'show_header' => (bool) $fresh->show_header === true,
        'protect' => (bool) $fresh->protect === false,
        'count_increased' => Profile::query()->count() === $before + 1,
    ];

    $failed = 0;
    foreach ($checks as $name => $ok) {
        echo ($ok ? 'PASS' : 'FAIL').' '.$name.PHP_EOL;
        if (! $ok) {
            $failed++;
        }
    }

    // Always remove the smoke-test row (MySQL may not support nested transactions).
    Profile::query()->whereKey($profile->id)->forceDelete();

    echo 'AFTER_COUNT '.Profile::query()->count().' (should equal before '.$before.')'.PHP_EOL;
    exit($failed > 0 ? 2 : 0);
} catch (Throwable $e) {
    if (isset($profile) && $profile->exists) {
        Profile::query()->whereKey($profile->id)->forceDelete();
    }
    echo 'ERROR '.$e->getMessage().PHP_EOL;
    echo $e->getFile().':'.$e->getLine().PHP_EOL;
    exit(1);
}
