<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ContactCaptchaService
{
    public const SESSION_KEY = 'contact_captcha_hash';

    /**
     * Generate a 4-character alpha captcha (legacy Captcha_Alpha style) and store its hash.
     *
     * @return resource|\GdImage
     */
    public function image()
    {
        $code = $this->generateCode(4);
        Session::put(self::SESSION_KEY, sha1(Str::upper($code)));

        $width = 150;
        $height = 50;
        $image = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($image, 240, 248, 255);
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        for ($i = 0; $i < 8; $i++) {
            $noise = imagecolorallocate(
                $image,
                random_int(80, 200),
                random_int(80, 200),
                random_int(80, 200)
            );
            imageellipse(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(10, 60),
                random_int(10, 40),
                $noise
            );
        }

        $chars = str_split($code);
        $count = count($chars);
        $slot = (int) floor($width / max($count, 1));

        foreach ($chars as $index => $char) {
            $color = imagecolorallocate(
                $image,
                random_int(20, 100),
                random_int(20, 100),
                random_int(20, 100)
            );
            $x = (int) ($index * $slot + random_int(4, 10));
            $y = random_int(12, 28);
            imagestring($image, 5, $x, $y, $char, $color);
        }

        for ($i = 0; $i < 40; $i++) {
            $dot = imagecolorallocate(
                $image,
                random_int(100, 220),
                random_int(100, 220),
                random_int(100, 220)
            );
            imagesetpixel($image, random_int(0, $width - 1), random_int(0, $height - 1), $dot);
        }

        return $image;
    }

    /**
     * Validate and consume captcha (one-shot — for contact forms).
     */
    public function valid(?string $answer): bool
    {
        if (! $this->matches($answer)) {
            return false;
        }

        $this->consume();

        return true;
    }

    /**
     * Check captcha without consuming it (safe for multi-field validation).
     */
    public function matches(?string $answer): bool
    {
        $hash = Session::get(self::SESSION_KEY);

        if (! is_string($hash) || $hash === '' || ! filled($answer)) {
            return false;
        }

        return hash_equals($hash, sha1(Str::upper(trim($answer))));
    }

    public function consume(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    protected function generateCode(int $length): string
    {
        // Avoid ambiguous characters (legacy "distinct" alphabet style).
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $code;
    }
}
