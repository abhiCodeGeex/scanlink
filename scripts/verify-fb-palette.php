<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo App\Models\FormBuilderQuestionType::count()." types\n";
foreach (app(App\Services\FormBuilderService::class)->paletteGroups() as $group => $items) {
    echo strtoupper($group)."\n";
    foreach ($items as $type) {
        echo '  '.$type->question_type_id.': '.$type->label()."\n";
    }
}
