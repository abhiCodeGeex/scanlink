<?php

declare(strict_types=1);

use App\Filament\Portal\Pages\CodeBalance;
use App\Filament\Portal\Pages\CumulativeAnalytics;
use App\Filament\Portal\Pages\EditAccount;
use App\Filament\Portal\Pages\ForcePasswordChange;
use App\Filament\Portal\Pages\FormBuilder;
use App\Filament\Portal\Pages\FormLibrary;
use App\Filament\Portal\Pages\FormSubmissions;
use App\Filament\Portal\Pages\ManageParticipants;
use App\Filament\Portal\Pages\MultipleCodeRenewal;
use App\Filament\Portal\Pages\OrderLabel;
use App\Filament\Portal\Pages\PortalDashboard;
use App\Filament\Portal\Pages\PurchaseCodes;
use App\Filament\Portal\Pages\PurchaseFormBuilder;
use App\Filament\Portal\Pages\ScanAnalytics;
use App\Filament\Portal\Pages\TeamUsers;
use App\Filament\Portal\Pages\VisitorLog;
use App\Filament\Portal\Pages\VocDashboard;
use App\Models\Client;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use App\Models\Weblink;
use App\Services\FormBuilderService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Route;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "ScanLink portal parity smoke check\n";
echo str_repeat('=', 40)."\n\n";

echo "Database counts\n";
echo '  clients: '.Client::query()->count()."\n";
echo '  profiles: '.Profile::query()->count()."\n";
echo '  questions: '.FormBuilderQuestion::query()->count()."\n";
echo '  answers: '.FormBuilderAnswer::query()->count()."\n";
echo '  weblinks: '.Weblink::query()->count()."\n";

if (class_exists(FormBuilderService::class)) {
    $palette = app(FormBuilderService::class)->paletteGroups();
    $typeCount = $palette['question']->count() + $palette['format']->count() + $palette['answer']->count();
    echo '  form_builder_types (palette): '.$typeCount.($typeCount > 8 ? ' OK' : ' LOW')."\n";
}

echo "\n";

$portalPages = [
    PortalDashboard::class,
    EditAccount::class,
    TeamUsers::class,
    CodeBalance::class,
    PurchaseCodes::class,
    MultipleCodeRenewal::class,
    ScanAnalytics::class,
    CumulativeAnalytics::class,
    VisitorLog::class,
    FormBuilder::class,
    FormLibrary::class,
    FormSubmissions::class,
    ManageParticipants::class,
    PurchaseFormBuilder::class,
    OrderLabel::class,
    VocDashboard::class,
    ForcePasswordChange::class,
];

echo "Portal page classes\n";
foreach ($portalPages as $pageClass) {
    $exists = class_exists($pageClass);
    echo '  '.class_basename($pageClass).': '.($exists ? 'OK' : 'MISSING')."\n";
}

echo "\nRoutes\n";
$routesToCheck = [
    'scan.show' => false,
    'marketing.home' => false,
    'marketing.contact' => false,
    'marketing.how-to' => false,
];

foreach (Route::getRoutes() as $route) {
    $name = $route->getName();

    if ($name !== null && array_key_exists($name, $routesToCheck)) {
        $routesToCheck[$name] = true;
    }
}

foreach ($routesToCheck as $name => $registered) {
    echo "  {$name}: ".($registered ? 'OK' : 'MISSING')."\n";
}

echo "\nDone.\n";
