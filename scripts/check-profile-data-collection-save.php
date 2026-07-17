<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Profile;

$profile = Profile::query()->find(6563);

if (! $profile) {
    fwrite(STDERR, "NO_PROFILE\n");
    exit(1);
}

try {
    $profile->fill([
        'data_collection_btn_text' => null,
        'data_collection_btn_color' => null,
    ]);
    $profile->save();
    echo "SAVE_OK btn_text=".var_export($profile->data_collection_btn_text, true)."\n";
} catch (Throwable $e) {
    echo "SAVE_FAIL: {$e->getMessage()}\n";
    exit(1);
}
