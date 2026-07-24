<?php

use App\Enums\AdminRole;
use App\Enums\UserType;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

Filament::setCurrentPanel(Filament::getPanel('admin'));

$user = User::query()->whereNotNull('admin_role')->orderBy('id')->first()
    ?? User::query()->updateOrCreate(
        ['email' => 'nav-check-admin@scanlink.local'],
        [
            'name' => 'Nav Check',
            'password' => 'Admin@12345',
            'user_type' => UserType::Admin,
            'admin_role' => AdminRole::SuperAdmin,
        ]
    );

Auth::login($user);
Filament::auth()->login($user);

$items = collect(Filament::getCurrentOrDefaultPanel()->getNavigation())
    ->flatMap(function ($group) {
        if (method_exists($group, 'getItems')) {
            return collect($group->getItems())->map(fn (NavigationItem $i) => [
                'group' => method_exists($group, 'getLabel') ? $group->getLabel() : null,
                'label' => $i->getLabel(),
                'url' => $i->getUrl(),
            ]);
        }

        return [];
    })
    ->values();

// Also scan nested groups from getNavigation which may be NavigationGroup objects
$labels = [];
$urls = [];

$walk = function ($nodes) use (&$walk, &$labels, &$urls): void {
    foreach ($nodes as $node) {
        if ($node instanceof NavigationItem) {
            $labels[] = $node->getLabel();
            $urls[] = (string) $node->getUrl();
        } elseif (is_object($node) && method_exists($node, 'getItems')) {
            $walk($node->getItems());
        } elseif (is_array($node)) {
            $walk($node);
        }
    }
};

$walk(Filament::getCurrentOrDefaultPanel()->getNavigation());

echo 'labels: '.implode(' | ', array_unique($labels)).PHP_EOL;
echo (in_array('Profile', $labels, true) ? '[OK] Profile nav item registered' : '[FAIL] Profile nav item missing').PHP_EOL;
echo (in_array('Change Password', $labels, true) ? '[FAIL] Change Password still registered' : '[OK] Change Password not registered').PHP_EOL;
echo (collect($urls)->contains(fn ($u) => str_contains($u, '/admin/profile')) ? '[OK] Profile URL in navigation' : '[FAIL] Profile URL missing').PHP_EOL;
