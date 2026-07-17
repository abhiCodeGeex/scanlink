<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Profile;
use App\Services\ProfileMediaService;

$profile = Profile::query()->find(6563);

if (! $profile) {
    fwrite(STDERR, "NO_PROFILE\n");
    exit(1);
}

try {
    app(ProfileMediaService::class)->syncUploads($profile, [
        'picture_uploads' => ['profiles/pictures/test-save.jpg'],
        'logo_upload' => 'profiles/logos/test-logo.jpg',
    ]);

    $picture = $profile->pictures()->latest('id')->first();
    echo 'PICTURE_OK txt_footer='.var_export($picture?->txt_footer, true)."\n";
    echo 'picture_name='.$picture?->picture_name."\n";

    $logo = $profile->logos()->latest('id')->first();
    echo 'LOGO_OK logo_name='.$logo?->logo_name."\n";
} catch (Throwable $e) {
    echo 'FAIL: '.$e->getMessage()."\n";
    exit(1);
}
