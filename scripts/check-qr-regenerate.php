<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = App\Models\Profile::find(6563);

if (! $p) {
    fwrite(STDERR, "NO_PROFILE\n");
    exit(1);
}

$path = storage_path('app/public/qrcode/CSQRIMG6563.png');

try {
    app(App\Services\ProfileQrService::class)->generateFor($p);
    echo 'QR_OK writable='.(is_writable($path) ? 'yes' : 'no')."\n";
    echo 'owner='.posix_getpwuid(fileowner($path))['name']."\n";
} catch (Throwable $e) {
    echo 'FAIL: '.$e->getMessage()."\n";
    exit(1);
}
