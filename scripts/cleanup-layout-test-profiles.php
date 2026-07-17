<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$query = App\Models\Profile::query()->where('code_profile_name', 'like', 'layout-test-%');
$count = $query->count();
echo "test_rows={$count}".PHP_EOL;

// Hard-delete only our smoke-test rows (rolled-back transaction failed silently).
$ids = $query->pluck('id');
foreach ($ids as $id) {
    App\Models\Profile::query()->whereKey($id)->forceDelete();
}

echo 'remaining='.App\Models\Profile::query()->where('code_profile_name', 'like', 'layout-test-%')->count().PHP_EOL;
