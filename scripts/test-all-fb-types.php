<?php

/**
 * Full Form Builder smoke test: save + render every palette type, uploads, scan page.
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Portal\FormBuilderUploadController;
use App\Http\Controllers\Portal\LegacyFormBuilderController;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use App\Models\User;
use App\Services\FormBuilderService;
use App\Support\FormBuilderMedia;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$user = User::query()->find(2);
if (! $user) {
    echo "NO_USER\n";
    exit(1);
}
auth()->login($user);

$profile = Profile::query()->find(13);
if (! $profile) {
    echo "NO_PROFILE\n";
    exit(1);
}

$profile->forceFill([
    'form_is_enable' => true,
    'form_active' => true,
    'form_title' => $profile->form_title ?: 'Test Form',
])->save();

// Clean previous test questions for a predictable scan page.
FormBuilderQuestion::query()->where('profile_id', $profile->id)->delete();

$svc = app(FormBuilderService::class);
$controller = app(LegacyFormBuilderController::class);
$uploadController = app(FormBuilderUploadController::class);

$fail = 0;
$pass = 0;

$mark = function (string $label, bool $ok, string $detail = '') use (&$fail, &$pass): void {
    if ($ok) {
        $pass++;
        echo "PASS {$label}".($detail !== '' ? " — {$detail}" : '')."\n";
    } else {
        $fail++;
        echo "FAIL {$label}".($detail !== '' ? " — {$detail}" : '')."\n";
    }
};

// --- Upload endpoints (forms) ---
foreach ([
    '/test/temp_image_upload' => 'imageForm',
    '/test/temp_doc_upload' => 'docForm',
    '/test/temp_doc_multi_upload' => 'docMultiForm',
] as $path => $method) {
    $req = Request::create($path, 'GET', ['profile_id' => $profile->id, 'random_id' => '999']);
    try {
        $view = $uploadController->{$method}($req);
        $html = $view->render();
        $mark("upload form {$path}", str_contains($html, 'userfile') && str_contains($html, 'csrf'));
    } catch (Throwable $e) {
        $mark("upload form {$path}", false, $e->getMessage());
    }
}

// --- Real image upload ---
@mkdir(storage_path('app/public/form-builder/images'), 0775, true);
@mkdir(storage_path('app/public/form-builder/docs'), 0775, true);
@mkdir(public_path('images/form_builder_uploaded_images'), 0775, true);
@mkdir(public_path('images/formbuilder_upload'), 0775, true);

$pngPath = storage_path('app/fb-test.png');
file_put_contents($pngPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
$imgUpload = UploadedFile::fake()->image('hello.png', 20, 20);
$imgReq = Request::create('/portal/legacy-form-builder/upload-image', 'POST', [
    'profile_id' => $profile->id,
    'textbox_id' => 'textbox999',
], files: ['userfile' => $imgUpload]);
session()->start();
try {
    $imgResp = $uploadController->storeImage($imgReq);
    $imgBody = $imgResp->getContent();
    $imgName = (string) session('form_builder_image_temp');
    $mark('storeImage', $imgResp->getStatusCode() === 200 && $imgName !== '' && str_contains($imgBody, 'Uploaded'), "file={$imgName}");
    $mark('image public copy', is_file(public_path('images/form_builder_uploaded_images/'.$imgName)));
    $mark('image media url', (bool) FormBuilderMedia::url($imgName), (string) FormBuilderMedia::url($imgName));
} catch (Throwable $e) {
    $mark('storeImage', false, $e->getMessage());
    $imgName = '';
}

// --- Real doc upload ---
$pdfPath = storage_path('app/fb-test.pdf');
file_put_contents($pdfPath, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n");
$docUpload = new UploadedFile($pdfPath, 'policy.pdf', 'application/pdf', null, true);
$docReq = Request::create('/portal/legacy-form-builder/upload-doc', 'POST', [
    'profile_id' => $profile->id,
    'random_id' => '888',
    'textbox_id' => 'textbox888',
    'multiple' => '0',
], files: ['userfile' => $docUpload]);
try {
    $docResp = $uploadController->storeDoc($docReq);
    $docName = (string) session('form_builder_doc_temp');
    $mark('storeDoc', $docResp->getStatusCode() === 200 && $docName !== '', "file={$docName}");
    $mark('doc public copy', is_file(public_path('images/formbuilder_upload/'.$docName)));
    $mark('doc media url', (bool) FormBuilderMedia::url($docName), (string) FormBuilderMedia::url($docName));
} catch (Throwable $e) {
    $mark('storeDoc', false, $e->getMessage());
    $docName = '';
}

$types = [
    1 => ['user_input' => 'FB Text Field'],
    2 => ['user_input' => '<p>FB <b>Static Text</b></p>'],
    3 => ['user_input' => 'Yes:::No:::Maybe'],
    4 => ['user_input' => 'Alpha:::Beta'],
    5 => ['user_input' => 'One:::Two'],
    6 => ['user_input' => 'Scale', 'scale_from' => '1', 'scale_to' => '5'],
    7 => ['user_input' => 'Grid', 'row_text' => 'Row1,Row2', 'column_text' => 'Col1,Col2'],
    8 => ['user_input' => 'Pick a date'],
    9 => ['user_input' => 'Pick a time'],
    11 => ['user_input' => $imgName ?: 'sample.png', 'image_title' => 'Hello image', 'image_align' => 'center'],
    13 => ['user_input' => '—', 'mandatory_field' => 'false'],
    14 => ['user_input' => '', 'mandatory_field' => 'false'],
    15 => ['user_input' => 'Comments label'],
    16 => ['user_input' => 'Sign here', 'include_name' => '1', 'include_email' => '1'],
    17 => ['user_input' => 'Upload file'],
    18 => ['user_input' => 'Participant', 'participant_include_signature' => '1'],
    19 => ['user_input' => 'Your location'],
    20 => ['user_input' => 'Visit site', 'button_link_url' => 'https://example.com', 'button_colour' => '007A01'],
    21 => ['user_input' => $docName ?: 'sample.pdf', 'doc_title' => 'Policy PDF', 'button_colour' => '007A01'],
    22 => ['user_input' => 'SWMS Hazard / Risk'],
    23 => ['user_input' => ($docName ?: 'a.pdf').',b.pdf', 'doc_title' => 'Doc A,Doc B'],
    24 => ['user_input' => 'Extra recipient email'],
    25 => ['user_input' => '<p>Check in please</p>', 'covid_bg_color' => 'ffffff', 'covid_text_color' => '000000', 'is_logchecked' => '1', 'mandatory_field' => 'false'],
];

$created = [];
echo "\n=== RENDER ALL TYPES ===\n";
foreach ($types as $typeId => $data) {
    $req = Request::create('/portal/legacy-form-builder/render-element', 'POST', array_merge([
        'profile_id' => $profile->id,
        'question_type_id' => $typeId,
        'question_id' => 0,
        'form_name' => 'Test Form',
        'email_arr' => 'test@example.com',
        'enable_form' => '1',
        'mandatory_field' => 'true',
        'include_name' => '0',
        'include_employer' => '0',
        'include_email' => '0',
        'include_phone' => '0',
        'participant_include_signature' => '0',
        'participant_include_employer' => '0',
        'scale_from' => '',
        'scale_to' => '',
        'row_text' => '',
        'column_text' => '',
        'image_title' => '',
        'image_align' => 'left',
        'button_link_url' => '',
        'button_colour' => '007A01',
        'doc_title' => '',
        'is_logchecked' => '0',
        'log_columntitle' => '',
        'covid_bg_color' => '',
        'covid_text_color' => '',
        'user_input' => '',
    ], $data));

    try {
        $resp = $controller->renderElement($req);
        $html = $resp->getContent();
        $ok = $resp->getStatusCode() === 200 && strlen($html) > 20 && ! str_contains($html, 'Exception');
        if (preg_match('/question_id_span[^>]*>\s*(\d+)/', $html, $m)) {
            $created[$typeId] = (int) $m[1];
        } elseif (preg_match('/class="question_id_span">(\d+)/', $html, $m)) {
            $created[$typeId] = (int) $m[1];
        }
        $mark("type {$typeId} render", $ok, 'len='.strlen($html).(isset($created[$typeId]) ? ' id='.$created[$typeId] : ''));
    } catch (Throwable $e) {
        $mark("type {$typeId} render", false, $e->getMessage());
    }
}

// Image should have been taken from session on first type-11 render if user_input empty — verify DB
if (isset($created[11])) {
    $q11 = FormBuilderQuestion::query()->find($created[11]);
    $mark('type 11 saved filename', $q11 && $q11->question_text !== '', (string) ($q11->question_text ?? ''));
}

// Builder page itself
$indexReq = Request::create('/portal/legacy-form-builder', 'GET', ['profile_id' => $profile->id]);
try {
    $indexResp = $controller->index($indexReq);
    $indexHtml = method_exists($indexResp, 'render') ? $indexResp->render() : $indexResp->getContent();
    $mark('builder page', str_contains($indexHtml, 'Text Field') || str_contains($indexHtml, 'question_type'), 'len='.strlen($indexHtml));
    $mark('builder has image iframe', str_contains($indexHtml, '/test/temp_image_upload') || str_contains($indexHtml, 'temp_image_upload'));
} catch (Throwable $e) {
    $mark('builder page', false, $e->getMessage());
}

// Scan page
$profile->loadMissing('client');
$clientUrl = $profile->client->url ?? 'acme-inspections';
$scanReq = Request::create('/'.$clientUrl.'/'.$profile->id.'?ask_for_location=no&portal_preview=1', 'GET');
$scanResp = $app->handle($scanReq);
$scanHtml = $scanResp->getContent();
$mark('scan status', $scanResp->getStatusCode() === 200, 'status='.$scanResp->getStatusCode());

$needles = [
    'FB Text Field',
    'Static Text',
    'Yes',
    'Pick a date',
    'Hello image',
    'Comments label',
    'Sign here',
    'Upload file',
    'Participant',
    'Your location',
    'Visit site',
    'Policy PDF',
    'SWMS',
    'Doc A',
    'Extra recipient email',
    'Check in please',
];
echo "\n=== SCAN NEEDLES ===\n";
foreach ($needles as $needle) {
    $mark("scan has '{$needle}'", str_contains($scanHtml, $needle));
}

echo "\n=== SUMMARY ===\n";
echo "pass={$pass} fail={$fail}\n";
exit($fail > 0 ? 1 : 0);
