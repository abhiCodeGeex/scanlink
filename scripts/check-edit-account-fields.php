<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Filament\Portal\Pages\EditAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::query()->where('email', 'portal-test@scanlink.local')->first();

if (! $user) {
    fwrite(STDERR, "NO_USER\n");
    exit(1);
}

Auth::login($user);

$reflection = new ReflectionClass(EditAccount::class);
$page = $reflection->newInstanceWithoutConstructor();

// Boot Livewire component minimally via mount checks on form schema methods
$schema = App\Filament\Portal\Pages\EditAccount::class;
echo "class_ok\n";

$labels = [
    'First name',
    'Last name',
    'Company name',
    'Billing address',
    'Email (this will also be your username)',
    'Town',
    'Telephone number',
    'Postal code',
    'Shortcut Title',
    'Shortcut Image 1',
    'Shortcut Image 2',
    'Footer Logo',
    'Old password',
    'New password',
    'Confirm new password',
];

$source = file_get_contents(app_path('Filament/Portal/Pages/EditAccount.php'));

foreach ($labels as $label) {
    $ok = str_contains($source, $label) ? 'OK' : 'MISSING';
    echo "{$ok} | {$label}\n";
}
