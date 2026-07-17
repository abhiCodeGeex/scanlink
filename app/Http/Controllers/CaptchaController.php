<?php

namespace App\Http\Controllers;

use App\Services\ContactCaptchaService;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    public function __invoke(ContactCaptchaService $captcha): Response
    {
        $image = $captcha->image();

        ob_start();
        imagepng($image);
        imagedestroy($image);
        $png = (string) ob_get_clean();

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
