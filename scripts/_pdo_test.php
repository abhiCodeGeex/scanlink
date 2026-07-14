<?php
ini_set('default_socket_timeout', '5');
try {
    $p = new PDO(
        'mysql:host=127.0.0.1;port=3307;dbname=scanlink_laravel',
        'scanlink',
        'scanlink',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "connected\n";
} catch (Throwable $e) {
    echo "error: " . $e->getMessage() . "\n";
}
