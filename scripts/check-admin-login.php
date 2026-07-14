<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ok = Illuminate\Support\Facades\Auth::attempt([
    'email' => 'admin@scanlink.com',
    'password' => 'Admin@12345',
]);

echo $ok ? "login-ok\n" : "login-fail\n";
