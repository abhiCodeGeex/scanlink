<?php

use App\Enums\AdminRole;
use App\Models\User;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::query()->first() ?? new User;
$user->name = $user->name ?: 'ScanLink Admin';
$user->email = 'admin@scanlink.com';
$user->password = 'Admin@12345';
$user->admin_role = AdminRole::SuperAdmin;
$user->save();

echo "OK admin@scanlink.com\n";
