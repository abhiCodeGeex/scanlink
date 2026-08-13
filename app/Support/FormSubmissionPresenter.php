<?php

namespace App\Support;

use App\Models\FormBuilderQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

/**
 * Formats form-builder answers for email / PDF / print so recipients see clean
 * labels and structured values instead of raw "::: " blobs and "User Input".
 */
class FormSubmissionPresenter
{
    private const DISPLAY_ONLY_TYPES = [2, 10, 11, 12, 13, 14, 20, 21];

    private const COVID_FIELD_LABELS = [
        'Visitor name',
        'Phone',
        'Date',
        'Time',
        'Venue name',
        'Venue address',
        'Location type',
        'Vehicle / other',
    ];

    /**
     * @param  Collection<int, FormBuilderQuestion>|iterable<FormBuilderQuestion>  $questions
     * @param  array<int|string, string>  $savedAnswers  question_id => answer
     * @return list<array{
     *     label: string,
     *     type_id: int,
     *     kind: 'text'|'signature'|'list'|'fields',
     *     value: string,
     *     items: list<string>,
     *     fields: list<array{label: string, value: string}>,
     *     signature_src: string,
     *     signature_meta: string,
     * }>
     */
    public static function rows(iterable $questions, array $savedAnswers): array
    {
        $byId = $questions instanceof Collection
            ? $questions
            : collect($questions);

        if ($byId->isNotEmpty() && ! is_int($byId->keys()->first())) {
            $byId = $byId->keyBy(fn (FormBuilderQuestion $q): int => (int) $q->question_id);
        }

        $rows = [];

        foreach ($savedAnswers as $questionId => $answer) {
            $question = $byId->get((int) $questionId);
            $typeId = (int) ($question?->question_type_id ?? 0);

            if (in_array($typeId, self::DISPLAY_ONLY_TYPES, true)) {
                continue;
            }

            $raw = trim((string) $answer);

            if ($raw === '') {
                continue;
            }

            $rows[] = self::presentOne($question, $raw);
        }

        return $rows;
    }

    /**
     * @return array{
     *     label: string,
     *     type_id: int,
     *     kind: 'text'|'signature'|'list'|'fields',
     *     value: string,
     *     items: list<string>,
     *     fields: list<array{label: string, value: string}>,
     *     signature_src: string,
     *     signature_meta: string,
     * }
     */
    public static function presentOne(?FormBuilderQuestion $question, string $raw): array
    {
        $typeId = (int) ($question?->question_type_id ?? 0);
        $label = self::label($question);
        $raw = trim($raw);

        $base = [
            'label' => $label,
            'type_id' => $typeId,
            'kind' => 'text',
            'value' => '',
            'items' => [],
            'fields' => [],
            'signature_src' => '',
            'signature_meta' => '',
        ];

        if (str_starts_with($raw, 'data:image')) {
            [$sigSrc, $sigMeta] = array_pad(explode(' | ', $raw, 2), 2, '');

            return array_merge($base, [
                'kind' => 'signature',
                'signature_src' => trim((string) $sigSrc),
                'signature_meta' => trim((string) $sigMeta),
            ]);
        }

        if ($typeId === 25) {
            return array_merge($base, [
                'kind' => 'fields',
                'fields' => self::covidFields($raw),
            ]);
        }

        if ($typeId === 7) {
            $items = array_values(array_filter(array_map(
                static fn (string $part): string => trim($part),
                preg_split('/;\s*/', $raw) ?: [],
            )));

            if (count($items) > 1) {
                return array_merge($base, [
                    'kind' => 'list',
                    'items' => $items,
                ]);
            }

            return array_merge($base, [
                'kind' => 'text',
                'value' => $raw,
            ]);
        }

        // Multi-select / packed answers: "a:::b:::c"
        if (str_contains($raw, ':::')) {
            $parts = array_values(array_filter(array_map(
                static fn (string $part): string => trim($part),
                explode(':::', $raw),
            )));

            // "Key: value" segments → labelled fields
            $kvFields = [];
            $allKv = $parts !== [] && collect($parts)->every(
                static fn (string $p): bool => (bool) preg_match('/^[^:]{1,40}:\s+.+/', $p)
            );

            if ($allKv) {
                foreach ($parts as $part) {
                    [$k, $v] = array_pad(explode(':', $part, 2), 2, '');
                    $kvFields[] = [
                        'label' => trim($k),
                        'value' => trim($v),
                    ];
                }

                return array_merge($base, [
                    'kind' => 'fields',
                    'fields' => $kvFields,
                ]);
            }

            if (count($parts) > 1) {
                return array_merge($base, [
                    'kind' => 'list',
                    'items' => $parts,
                ]);
            }

            return array_merge($base, [
                'kind' => 'text',
                'value' => $parts[0] ?? $raw,
            ]);
        }

        // Meta segments joined with " | " (participant extras, uploads, etc.)
        if (str_contains($raw, ' | ')) {
            $segments = array_values(array_filter(array_map(
                static fn (string $part): string => trim($part),
                preg_split('/\s\|\s/', $raw) ?: [],
            )));

            if (count($segments) > 1) {
                return array_merge($base, [
                    'kind' => 'list',
                    'items' => $segments,
                ]);
            }
        }

        return array_merge($base, [
            'kind' => 'text',
            'value' => $raw,
        ]);
    }

    public static function label(?FormBuilderQuestion $question): string
    {
        if (! $question) {
            return 'Response';
        }

        $logTitle = trim(strip_tags((string) ($question->log_columntitle ?? '')));
        if ($logTitle !== '') {
            return $logTitle;
        }

        $typeId = (int) $question->question_type_id;

        if (in_array($typeId, [3, 4, 5], true)) {
            $choiceLabel = FormBuilderMedia::choiceLabel($question);
            if ($choiceLabel !== '') {
                return $choiceLabel;
            }
        }

        $text = trim(strip_tags((string) ($question->question_text ?? '')));

        // Packed choice text ("Label:::A:::B" or "A:::B:::C") — never show raw ":::".
        if ($text !== '' && str_contains($text, ':::')) {
            $segments = array_values(array_filter(array_map(
                static fn (string $part): string => trim($part),
                explode(':::', $text),
            )));

            if (in_array($typeId, [3, 4, 5], true) && $segments !== []) {
                $options = FormBuilderMedia::choiceOptions($question);
                $first = $segments[0];
                // Prefer a leading prompt when it is not itself one of the options.
                if ($first !== '' && $options !== [] && ! in_array($first, $options, true)) {
                    return $first;
                }
            }

            return self::typeFallbackLabel($typeId);
        }

        if ($text !== '') {
            return $text;
        }

        return self::typeFallbackLabel($typeId);
    }

    public static function typeFallbackLabel(int $typeId): string
    {
        return match ($typeId) {
            1 => 'Text field',
            3 => 'Multiple choice',
            4 => 'Checkbox',
            5 => 'Dropdown',
            6 => 'Number scale',
            7 => 'Grid',
            8 => 'Date',
            9 => 'Time',
            15 => 'Comments',
            16 => 'Signature',
            17 => 'Upload',
            18 => 'Participant',
            19 => 'Location',
            22 => 'SWMS hazard / risk',
            23 => 'Document',
            24 => 'Additional recipient',
            25 => 'Check-in',
            default => 'Response',
        };
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    public static function covidFields(string $raw): array
    {
        $parts = array_pad(explode(':::', $raw), 8, '');
        $fields = [];

        foreach (self::COVID_FIELD_LABELS as $index => $fieldLabel) {
            $value = trim((string) ($parts[$index] ?? ''));
            if ($value === '' && $index === 7) {
                continue;
            }
            $fields[] = [
                'label' => $fieldLabel,
                'value' => $value !== '' ? $value : '—',
            ];
        }

        return $fields;
    }

    /**
     * Safe HTML for a presented answer value (text / list items / fields).
     */
    public static function answerHtml(array $row): HtmlString
    {
        return match ($row['kind']) {
            'signature' => self::signatureHtml($row),
            'list' => new HtmlString(
                '<ul style="margin:0;padding-left:18px;">'
                .collect($row['items'])->map(
                    static fn (string $item): string => '<li style="margin:0 0 4px;">'
                        .FormAnswerHtml::text($item)->toHtml()
                        .'</li>'
                )->implode('')
                .'</ul>'
            ),
            'fields' => new HtmlString(
                '<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;">'
                .collect($row['fields'])->map(static function (array $field): string {
                    return '<tr>'
                        .'<td style="padding:4px 12px 4px 0;color:#6b7280;font-size:13px;width:38%;vertical-align:top;">'
                        .e($field['label'])
                        .'</td>'
                        .'<td style="padding:4px 0;color:#111827;font-size:14px;vertical-align:top;">'
                        .FormAnswerHtml::text($field['value'])->toHtml()
                        .'</td>'
                        .'</tr>';
                })->implode('')
                .'</table>'
            ),
            default => FormAnswerHtml::text((string) ($row['value'] ?? '')),
        };
    }

    /**
     * Plain-ish HTML suitable for TCPDF (no flex; simple tags).
     */
    public static function answerPdfHtml(array $row): string
    {
        return match ($row['kind']) {
            'signature' => self::signaturePdfHtml($row),
            'list' => '<ul>'
                .collect($row['items'])->map(
                    static fn (string $item): string => '<li>'.nl2br(e($item)).'</li>'
                )->implode('')
                .'</ul>',
            'fields' => '<table cellpadding="3" cellspacing="0" border="0" width="100%">'
                .collect($row['fields'])->map(static function (array $field): string {
                    return '<tr>'
                        .'<td width="40%" style="color:#4b5563;"><b>'.e($field['label']).'</b></td>'
                        .'<td width="60%">'.nl2br(e($field['value'])).'</td>'
                        .'</tr>';
                })->implode('')
                .'</table>',
            default => ($v = trim((string) ($row['value'] ?? ''))) === ''
                ? '&nbsp;'
                : nl2br(e($v)),
        };
    }

    protected static function signatureHtml(array $row): HtmlString
    {
        $html = '<img src="'.e($row['signature_src']).'" alt="Signature" style="max-width:280px;height:auto;border:1px solid #e5e7eb;border-radius:4px;background:#fff;">';

        if (filled($row['signature_meta'])) {
            $html .= '<div style="margin-top:6px;color:#4b5563;font-size:13px;">'
                .FormAnswerHtml::text($row['signature_meta'])->toHtml()
                .'</div>';
        }

        return new HtmlString($html);
    }

    protected static function signaturePdfHtml(array $row): string
    {
        $src = (string) $row['signature_src'];
        $html = '';

        if (preg_match('#^data:image/[a-z0-9.+-]+;base64,(.+)$#i', $src, $imgMatch)
            && ($imgBin = base64_decode($imgMatch[1], true)) !== false
            && @imagecreatefromstring($imgBin) !== false) {
            $html = '<img src="'.htmlspecialchars($src, ENT_QUOTES).'" width="200">';
        } elseif (str_starts_with($src, 'data:image/')) {
            $html = '[signature image]';
        } else {
            $html = e($src);
        }

        if (filled($row['signature_meta'])) {
            $html .= '<br>'.nl2br(e($row['signature_meta']));
        }

        return $html;
    }
}
