<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = Illuminate\Support\Facades\DB::select(
    'SELECT update_or_not, COUNT(*) AS c FROM profiles WHERE deleted = 0 GROUP BY update_or_not ORDER BY c DESC'
);
foreach ($rows as $r) {
    echo 'update_or_not='.var_export($r->update_or_not, true).' count='.$r->c.PHP_EOL;
}

$active = App\Models\Profile::query()
    ->where('client_id', 2)
    ->where('deleted', false)
    ->where('update_or_not', false)
    ->where('expired_at', '>', now())
    ->count();
echo "active_slots_client2={$active}".PHP_EOL;
