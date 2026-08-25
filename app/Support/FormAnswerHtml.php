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

            // Signature / drawn-image sub-fields are stored inline as a data URI (optionally
            // "Signature: data:image…"). Render the image, not the raw base64 blob.
            if (preg_match('/^(?:([^:]{1,40}):\s*)?(data:image\/[a-z0-9.+-]+;base64,[A-Za-z0-9+\/=\s]+)$/i', $segment, $m)) {
                $label = trim((string) ($m[1] ?? ''));
                $src = (string) preg_replace('/\s+/', '', (string) $m[2]);
                $lines[] = ($label !== '' ? '<strong>'.e($label).':</strong><br>' : '')
                    .'<img src="'.e($src).'" alt="'.e($label !== '' ? $label : 'Signature').'" '
                    .'style="max-width:280px;height:auto;border:1px solid #e5e7eb;border-radius:4px;background:#fff;">';

                continue;
            }

            // Drop the "File:" label the submit handler prepends to upload segments.
            $segment = (string) preg_replace('/^File:\s*/i', '', $segment);

            // Labelled stored-file segments ("Signature: form-uploads/…"): keep the label as
            // text and render the file part (image inline / document link) below it.
            if (preg_match('/^([^:]{1,40}):\s*(form-uploads\/.+)$/i', $segment, $lm)) {
                $fileTokens = array_values(array_filter(array_map('trim', explode(',', $lm[2]))));
                if ($fileTokens !== [] && collect($fileTokens)->every(fn (string $t): bool => self::isFilePath($t))) {
                    $lines[] = '<strong>'.e(trim($lm[1])).':</strong><br>'
                        .implode(' &nbsp; ', array_map(fn (string $t): string => self::link($t), $fileTokens));

                    continue;
                }
            }

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
        return Str::contains($token, 'form-uploads/') || self::builderFilePath($token) !== null;
    }

    /**
     * Resolve a bare form-builder upload filename (fb_doc_* / fb_img_* — stored under
     * form-builder/docs|images on the public disk) to its disk-relative path, or null.
     */
    public static function builderFilePath(string $token): ?string
    {
        if (! preg_match('/^fb_(doc|img)_[A-Za-z0-9_.-]+\.[A-Za-z0-9]{2,5}$/', $token)) {
            return null;
        }

        foreach (['form-builder/docs/', 'form-builder/images/'] as $dir) {
            try {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($dir.$token)) {
                    return $dir.$token;
                }
            } catch (\Throwable) {
                // ignore and try the next location
            }
        }

        return null;
    }

    protected static function link(string $path): string
    {
        // Bare form-builder filenames (fb_doc_* / fb_img_*) resolve to their storage folder.
        $resolved = self::builderFilePath($path);
        $url = asset('storage/'.ltrim($resolved ?? $path, '/'));

        // Show the full submitted data: uploaded IMAGES (photos, stored signatures) render
        // inline; documents stay as clickable links. Absolute URLs so emails render them too.
        if (preg_match('/\.(png|jpe?g|gif|webp)$/i', $path)) {
            return '<a href="'.e($url).'" target="_blank" rel="noopener">'
                .'<img src="'.e($url).'" alt="'.e(basename($path)).'" '
                .'style="max-width:280px;height:auto;border:1px solid #e5e7eb;border-radius:4px;background:#fff;display:block;margin:2px 0;"></a>';
        }

        return '<a href="'.e($url).'" target="_blank" rel="noopener">'.e(basename($path)).'</a>';
    }
}
