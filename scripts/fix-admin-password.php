<?php

$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3307;dbname=scanlink_laravel',
    'scanlink',
    'scanlink',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

$hash = password_hash('Admin@12345', PASSWORD_BCRYPT);

$update = $pdo->prepare('UPDATE users SET password = ?, name = ? WHERE email = ?');
$update->execute([$hash, 'ScanLink Admin', 'admin@scanlink.com']);

if ($update->rowCount() === 0) {
    $insert = $pdo->prepare(
        'INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())'
    );
    $insert->execute(['ScanLink Admin', 'admin@scanlink.com', $hash]);
    echo "inserted\n";
} else {
    echo "updated\n";
}

$check = $pdo->prepare('SELECT password FROM users WHERE email = ?');
$check->execute(['admin@scanlink.com']);
$row = $check->fetch(PDO::FETCH_ASSOC);

echo password_verify('Admin@12345', $row['password']) ? "verified\n" : "verify-failed\n";
