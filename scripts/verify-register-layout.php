<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$html = (string) $app->handle(Illuminate\Http\Request::create('/portal/register', 'GET'))->getContent();

echo (str_contains($html, 'breadcrumbsMain') ? '[OK] breadcrumbsMain' : '[FAIL] breadcrumbsMain').PHP_EOL;
echo (str_contains($html, 'sl-reg-field') ? '[OK] paired fields' : '[FAIL] paired fields').PHP_EOL;
echo (str_contains($html, 'id="frmregister"') ? '[OK] form grid' : '[FAIL] form grid').PHP_EOL;
echo (str_contains($html, 'Upload') && str_contains($html, 'web links, videos') ? '[OK] step 3 visible' : '[FAIL] step 3 missing').PHP_EOL;

$l = strpos($html, 'id="first_name"');
$r = strpos($html, 'id="last_name"');
echo ($l !== false && $r !== false && $l < $r ? '[OK] first before last' : '[FAIL] field order').PHP_EOL;
