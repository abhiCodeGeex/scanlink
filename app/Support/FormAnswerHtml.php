<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class FormAnswerHtml
{
    /**
     * Render a stored form-answer string as safe HTML.
     *
     * Legacy parity: uploaded-file answers (types 17 File Upload, 21 Document Button,
     * 22 SWMS attachments, 23 Document Menu) were served as clickable links; the new
     * app stored the file paths but printed them as plain text. Here, path tokens
     * (`form-uploads/...`) become links to the public disk, and the submit handler's
     * " | "-joined meta segments (participant / SWMS / signature sub-fields) render on
     * their own lines. Everything else is escaped text with newlines preserved.
     */
    public static function text(string $raw): HtmlString
    {
        $raw = trim($raw);

        if ($raw === '') {
            return new HtmlString('—');
        }

        $lines = [];

        foreach (preg_split('/\s\|\s/', $raw) as $segment) {
            $segment = trim((string) $segment);

            if ($segment === '') {
                continue;
            }

            // Drop the "File:" label the submit handler prepends to upload segments.
            $segment = (string) preg_replace('/^File:\s*/i', '', $segment);

            $tokens = array_values(array_filter(
                array_map('trim', explode(',', $segment)),
                fn (string $t): bool => $t !== '',
            ));

            if ($tokens !== [] && collect($tokens)->every(fn (string $t): bool => self::isFilePath($t))) {
                $lines[] = implode(' &nbsp; ', array_map(fn (string $t): string => self::link($t), $tokens));
            } else {
                $lines[] = nl2br(e($segment));
            }
        }

        return new HtmlString(implode('<br>', $lines));
    }

    /**
     * Extract stored upload paths from an answer string (for file cleanup on delete).
     *
     * @return list<string>
     */
    public static function extractFilePaths(string $raw): array
    {
        preg_match_all('#form-uploads/[^\s,|]+#', $raw, $matches);

        return array_values(array_unique($matches[0] ?? []));
    }

    protected static function isFilePath(string $token): bool
    {
        return Str::contains($token, 'form-uploads/');
    }

    protected static function link(string $path): string
    {
        return '<a href="'.e(asset('storage/'.ltrim($path, '/'))).'" target="_blank" rel="noopener">'
            .e(basename($path)).'</a>';
    }
}
