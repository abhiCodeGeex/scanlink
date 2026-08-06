<?php

namespace App\Support;

use App\Models\FormBuilderQuestion;
use Illuminate\Support\Facades\Storage;

class FormBuilderMedia
{
    /** @var list<string> */
    private const LEGACY_FOLDERS = [
        'images/form_builder_uploaded_images',
        'images/form_builder_uploaded_docs',
        'images/formbuilder_upload',
        'form-builder/images',
        'form-builder/docs',
    ];

    public static function url(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $normalized = PublicMediaPath::normalize($path);

        if ($normalized !== '' && self::storageExists($normalized)) {
            return PublicMediaPath::url($normalized);
        }

        if ($normalized !== '' && self::publicExists($normalized)) {
            return asset($normalized);
        }

        if (! str_contains($path, '/')) {
            foreach (self::LEGACY_FOLDERS as $folder) {
                $candidate = $folder.'/'.$path;

                if (self::storageExists($candidate)) {
                    return PublicMediaPath::url($candidate);
                }

                if (self::publicExists($candidate)) {
                    return asset($candidate);
                }
            }
        }

        return PublicMediaPath::url($normalized !== '' ? $normalized : $path);
    }

    public static function alignValue(?string $align): string
    {
        return match (strtolower(trim((string) $align))) {
            '1', 'center', 'centre' => 'center',
            '2', 'right' => 'right',
            default => 'left',
        };
    }

    public static function alignCss(?string $align): string
    {
        return self::alignValue($align);
    }

    /**
     * @return list<string>
     */
    public static function splitCsv(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $parts = preg_split('/(?:,|:::)/', $value) ?: [];

        return array_values(array_filter(
            array_map(static fn (string $part): string => trim($part), $parts),
            static fn (string $part): bool => $part !== '',
        ));
    }

    public static function resolveDocumentHref(FormBuilderQuestion $q): ?string
    {
        $link = trim((string) ($q->button_link_url ?? ''));

        if ($link !== '') {
            if (str_starts_with($link, 'http://') || str_starts_with($link, 'https://')) {
                return $link;
            }

            $url = self::url($link);

            if ($url !== null) {
                return $url;
            }
        }

        $files = self::splitCsv((string) ($q->question_text ?? ''));

        if ($files !== []) {
            return self::url($files[0]);
        }

        return null;
    }

    /**
     * @return list<array{title: string, href: string|null}>
     */
    public static function documentChoices(FormBuilderQuestion $q): array
    {
        $titles = self::splitCsv((string) ($q->doc_title ?? ''));
        $paths = self::splitCsv((string) ($q->question_text ?? ''));
        $count = max(count($titles), count($paths));
        $choices = [];

        for ($i = 0; $i < $count; $i++) {
            $path = $paths[$i] ?? '';
            $title = $titles[$i] ?? ($path !== '' ? basename($path) : 'Document');

            if ($path === '' && $title === '') {
                continue;
            }

            $choices[] = [
                'title' => $title,
                'href' => $path !== '' ? self::url($path) : null,
            ];
        }

        return $choices;
    }

    /**
     * Choice tools (Multiple Choices / Check Box / Drop Down) store options in
     * question_text as "a:::b:::c" and also as option rows. Never show the raw
     * ::: string as the question label on the scan page.
     *
     * @return list<string>
     */
    public static function choiceOptions(FormBuilderQuestion $q): array
    {
        $fromRows = $q->relationLoaded('options')
            ? $q->options
            : $q->options()->get();

        $names = $fromRows
            ->where('question_option_type_id', 0)
            ->pluck('option_name')
            ->map(static fn ($n): string => trim((string) $n))
            ->filter()
            ->values()
            ->all();

        if ($names !== []) {
            return $names;
        }

        // Fallback: all option rows (some imports omit question_option_type_id).
        $names = $fromRows
            ->pluck('option_name')
            ->map(static fn ($n): string => trim((string) $n))
            ->filter()
            ->values()
            ->all();

        if ($names !== []) {
            return $names;
        }

        return self::splitCsv((string) ($q->question_text ?? ''));
    }

    public static function choiceLabel(FormBuilderQuestion $q): string
    {
        $text = trim(strip_tags((string) ($q->question_text ?? '')));

        if ($text === '' || str_contains($text, ':::')) {
            return '';
        }

        // If the stored text is only a comma-joined copy of the options, hide it.
        $options = self::choiceOptions($q);
        if ($options !== []) {
            $joinedComma = implode(',', $options);
            $joinedSlash = implode(' / ', $options);
            if ($text === $joinedComma || $text === $joinedSlash) {
                return '';
            }
        }

        return $text;
    }

    private static function storageExists(string $path): bool
    {
        try {
            return Storage::disk('public')->exists($path);
        } catch (\Throwable) {
            return false;
        }
    }

    private static function publicExists(string $path): bool
    {
        $full = public_path($path);

        return is_file($full);
    }
}
