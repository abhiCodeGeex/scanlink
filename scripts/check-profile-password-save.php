<?php

/**
 * Simulate portal save with blank password (Filament empty field → null).
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Profile;

$profile = Profile::query()->find(6563);

if (! $profile) {
    fwrite(STDERR, "NO_PROFILE\n");
    exit(1);
}

$before = $profile->password;

try {
    $profile->fill([
        'notes' => $profile->notes,
        'password' => null,
    ]);
    $profile->save();
    $profile->refresh();

    echo "SAVE_OK\n";
    echo 'password_unchanged='.(($profile->password ?? '') === ($before ?? '') ? 'yes' : 'no')."\n";
} catch (Throwable $e) {
    echo "SAVE_FAIL: {$e->getMessage()}\n";
    exit(1);
}
