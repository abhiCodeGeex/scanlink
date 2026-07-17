<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Profile;
use App\Support\PortalProfilePreview;

$profile = Profile::with('qrImage', 'client')->find(6563);

if (! $profile) {
    fwrite(STDERR, "NO_PROFILE\n");
    exit(1);
}

$publicUrl = $profile->qrImage?->publicUrl();
$badUrl = asset('storage/'.ltrim((string) $profile->qrImage?->qrimg_name, '/'));
$previewUrl = PortalProfilePreview::previewUrl($profile);

echo "publicUrl={$publicUrl}\n";
echo "badUrl={$badUrl}\n";
echo "previewUrl={$previewUrl}\n";
echo 'publicUrl_ok='.(str_contains((string) $publicUrl, '/storage/storage/') ? 'NO' : 'YES')."\n";
echo 'file='.(file_exists(public_path('storage/'.$profile->qrImage->diskPath())) ? 'yes' : 'no')."\n";
echo 'symlink='.(is_link(public_path('storage')) ? 'yes' : 'no')."\n";
