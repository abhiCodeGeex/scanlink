<?php

namespace App\Support;

class PublicMediaPath
{
    public static function normalize(?string $path): string
    {
        if ($path === null || trim($path) === '') {
            return '';
        }

        return ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
    }

    public static function url(?string $path): ?string
    {
        $normalized = self::normalize($path);

        if ($normalized === '') {
            return null;
        }

        return asset('storage/'.$normalized);
    }
}
