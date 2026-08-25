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
        $used = [];

        // Render in the SAME order the form showed its questions (question_order) — the
        // answer map appends meta-only items (SWMS / signatures / uploads) at the end,
        // which put them out of sequence in the email and PDF.
        $ordered = $byId->sortBy(fn (FormBuilderQuestion $q): int => (int) $q->question_order)->values();

        foreach ($ordered as $question) {
            $qid = (int) $question->question_id;
            $answer = $savedAnswers[$qid] ?? $savedAnswers[(string) $qid] ?? null;
            $raw = trim((string) ($answer ?? ''));

            // Document Button (21): the element's document IS the content — the visitor never
            // "answers" it, so render its clickable link in the report/email/PDF.
            if ((int) $question->question_type_id === 21 && $raw === '') {
                $used[$qid] = true;
                $href = \App\Support\FormBuilderMedia::resolveDocumentHref($question);

                if (filled($href)) {
                    $rows[] = [
                        'label' => trim((string) ($question->doc_title ?: 'Document')),
                        'type_id' => 21,
                        'kind' => 'file_link',
                        'value' => '',
                        'items' => [],
                        'fields' => [],
                        'instances' => [],
                        'signature_src' => '',
                        'signature_meta' => '',
                        'href' => (string) $href,
                        'name' => basename((string) (parse_url((string) $href, PHP_URL_PATH) ?: 'document')),
                    ];
                }

                continue;
            }

            if ($answer === null) {
                continue;
            }

            $used[$qid] = true;

            if ($raw === '') {
                continue;
            }

            // Display-only types normally carry no answer — but when one DOES (e.g. a
            // Document Button click stored the file), render it: everything the visitor
            // filled must appear in the email/PDF.
            if (in_array((int) $question->question_type_id, self::DISPLAY_ONLY_TYPES, true) && $raw === '') {
                continue;
            }

            $rows[] = self::presentOne($question, $raw);
        }

        // Answers whose question no longer exists (deleted after submission) — keep at the end.
        foreach ($savedAnswers as $questionId => $answer) {
            if (isset($used[(int) $questionId])) {
                continue;
            }

            $raw = trim((string) $answer);

            if ($raw === '') {
                continue;
            }

            $rows[] = self::presentOne($byId->get((int) $questionId), $raw);
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
            'instances' => [],
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

        // SWMS (type 22): each hazard row is stored as its own "@@SWMS@@"-delimited instance
        // of "@@F@@"-delimited "slug=value" fields. Present as grouped instances so the
        // email / print / PDF can divide the rows visually.
        if ($typeId === 22 && (
            str_contains($raw, '@@F@@')
            || str_contains($raw, '@@SWMS@@')
            || preg_match('/^(task|hazards|risk_before|control|risk_after|photo)=/', $raw)
        )) {
            return array_merge($base, [
                'kind' => 'swms',
                'instances' => self::swmsInstances($raw),
            ]);
        }

        // Repeatable signature element (type 16): each entry is stored as an "@@ROW@@"-delimited
        // instance of "@@F@@"-delimited "slug=value" fields (name/employer/email/phone/signature).
        // (Old single-signature answers start with "data:image" and are handled above.)
        if ($typeId === 16 && (
            str_contains($raw, '@@ROW@@')
            || str_contains($raw, '@@F@@')
            || preg_match('/^(name|employer|email|phone|signature)=/', $raw)
        )) {
            return array_merge($base, [
                'kind' => 'sigrows',
                'instances' => self::signatureRows($raw),
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
     * Parse a stored SWMS (type 22) answer into a list of hazard-row instances, each a list
     * of {label, value, is_file} fields.
     *
     * @return list<list<array{label: string, value: string, is_file: bool}>>
     */
    public static function swmsInstances(string $raw): array
    {
        $labels = [
            'task' => 'Task / Activity',
            'hazards' => 'Potential Hazards',
            'risk_before' => 'Risk Score (Before)',
            'control' => 'Control Measures',
            'risk_after' => 'Risk Score (After)',
            'photo' => 'Photo',
        ];

        $instances = [];

        foreach (explode('@@SWMS@@', $raw) as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            $fields = [];
            foreach (explode('@@F@@', $block) as $segment) {
                $segment = trim($segment);
                if ($segment === '') {
                    continue;
                }

                [$slug, $value] = array_pad(explode('=', $segment, 2), 2, '');
                $slug = trim($slug);
                $value = trim($value);
                if ($value === '') {
                    continue;
                }

                $fields[] = [
                    'label' => $labels[$slug] ?? ucfirst(str_replace('_', ' ', $slug)),
                    'value' => $value,
                    'is_file' => $slug === 'photo',
                ];
            }

            if ($fields !== []) {
                $instances[] = $fields;
            }
        }

        return $instances;
    }

    /**
     * Parse a repeatable signature (type 16) answer into a list of instances, each a list of
     * {label, value, is_signature} fields.
     *
     * @return list<list<array{label: string, value: string, is_signature: bool}>>
     */
    public static function signatureRows(string $raw): array
    {
        $labels = [
            'name' => 'Name',
            'employer' => 'Employer',
            'email' => 'Email',
            'phone' => 'Phone',
            'signature' => 'Signature',
        ];

        $instances = [];

        foreach (explode('@@ROW@@', $raw) as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            $fields = [];
            foreach (explode('@@F@@', $block) as $segment) {
                $segment = trim($segment);
                if ($segment === '') {
                    continue;
                }

                [$slug, $value] = array_pad(explode('=', $segment, 2), 2, '');
                $slug = trim($slug);
                $value = trim($value);
                if ($value === '') {
                    continue;
                }

                $fields[] = [
                    'label' => $labels[$slug] ?? ucfirst(str_replace('_', ' ', $slug)),
                    'value' => $value,
                    'is_signature' => $slug === 'signature',
                ];
            }

            if ($fields !== []) {
                $instances[] = $fields;
            }
        }

        return $instances;
    }

    /**
     * Safe HTML for a presented answer value (text / list items / fields).
     */
    public static function answerHtml(array $row): HtmlString
    {
        return match ($row['kind']) {
            'signature' => self::signatureHtml($row),
            'swms' => self::swmsHtml($row),
            'sigrows' => self::sigHtml($row),
            'file_link' => new HtmlString(
                '<a href="'.e((string) ($row['href'] ?? '#')).'" target="_blank" rel="noopener">'
                .e((string) ($row['name'] ?: 'Document')).'</a>'
            ),
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
            'swms' => self::swmsPdfHtml($row),
            'sigrows' => self::sigPdfHtml($row),
            'file_link' => '<a href="'.e((string) ($row['href'] ?? '#')).'">'
                .e((string) ($row['name'] ?: 'Document')).'</a>',
            'list' => '<ul>'
                .collect($row['items'])->map(
                    static fn (string $item): string => '<li>'.self::pdfText($item).'</li>'
                )->implode('')
                .'</ul>',
            'fields' => '<table cellpadding="3" cellspacing="0" border="0" width="100%">'
                .collect($row['fields'])->map(static function (array $field): string {
                    return '<tr>'
                        .'<td width="40%" style="color:#4b5563;"><b>'.e($field['label']).'</b></td>'
                        .'<td width="60%">'.self::pdfText((string) $field['value']).'</td>'
                        .'</tr>';
                })->implode('')
                .'</table>',
            default => ($v = trim((string) ($row['value'] ?? ''))) === ''
                ? '&nbsp;'
                : self::pdfText($v),
        };
    }

    /**
     * HTML for a SWMS answer: each hazard row rendered as its own titled block, divided by a
     * rule (SWMS #1 — divider — SWMS #2 …). Photo values become clickable file links.
     */
    protected static function swmsHtml(array $row): HtmlString
    {
        $instances = $row['instances'] ?? [];

        if ($instances === []) {
            return new HtmlString('—');
        }

        $html = collect($instances)->map(static function (array $fields, int $i): string {
            $divider = $i > 0
                ? '<div style="border-top:2px solid #d1d5db;margin:14px 0 12px;"></div>'
                : '';

            $heading = '<div style="font-size:13px;font-weight:bold;color:#065f06;margin:0 0 6px;">SWMS #'.($i + 1).'</div>';

            $rows = collect($fields)->map(static function (array $field): string {
                return '<tr>'
                    .'<td style="padding:3px 12px 3px 0;color:#6b7280;font-size:13px;width:42%;vertical-align:top;">'
                    .e($field['label'])
                    .'</td>'
                    .'<td style="padding:3px 0;color:#111827;font-size:14px;vertical-align:top;">'
                    .FormAnswerHtml::text((string) $field['value'])->toHtml()
                    .'</td>'
                    .'</tr>';
            })->implode('');

            return $divider.$heading
                .'<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;">'
                .$rows
                .'</table>';
        })->implode('');

        return new HtmlString($html);
    }

    /**
     * HTML for a repeatable signature answer: each entry rendered as its own block (divided by a
     * rule when there is more than one), with the signature drawn as an image.
     */
    protected static function sigHtml(array $row): HtmlString
    {
        $instances = $row['instances'] ?? [];

        if ($instances === []) {
            return new HtmlString('—');
        }

        $multi = count($instances) > 1;

        $html = collect($instances)->map(static function (array $fields, int $i) use ($multi): string {
            $divider = $i > 0
                ? '<div style="border-top:2px solid #d1d5db;margin:14px 0 12px;"></div>'
                : '';

            $heading = $multi
                ? '<div style="font-size:13px;font-weight:bold;color:#065f06;margin:0 0 6px;">Signature #'.($i + 1).'</div>'
                : '';

            $rows = collect($fields)->map(static function (array $field): string {
                if (! empty($field['is_signature'])) {
                    $value = (string) $field['value'];
                    // Stored signature file (new) or inline data URI (older submissions).
                    $src = str_starts_with($value, 'data:image')
                        ? $value
                        : asset('storage/'.ltrim($value, '/'));

                    return '<div style="margin:4px 0;">'
                        .'<div style="color:#6b7280;font-size:13px;margin:0 0 2px;">'.e($field['label']).'</div>'
                        .'<img src="'.e($src).'" alt="Signature" '
                        .'style="max-width:280px;height:auto;border:1px solid #e5e7eb;border-radius:4px;background:#fff;">'
                        .'</div>';
                }

                return '<div style="margin:2px 0;">'
                    .'<span style="color:#6b7280;font-size:13px;">'.e($field['label']).':</span> '
                    .FormAnswerHtml::text((string) $field['value'])->toHtml()
                    .'</div>';
            })->implode('');

            return $divider.$heading.$rows;
        })->implode('');

        return new HtmlString($html);
    }

    /**
     * TCPDF-safe repeatable-signature markup: blocks divided by a rule, signature embedded inline.
     */
    protected static function sigPdfHtml(array $row): string
    {
        $instances = $row['instances'] ?? [];

        if ($instances === []) {
            return '&nbsp;';
        }

        $multi = count($instances) > 1;

        return collect($instances)->map(static function (array $fields, int $i) use ($multi): string {
            $divider = $i > 0 ? '<hr>' : '';
            $heading = $multi ? '<p><b>Signature #'.($i + 1).'</b></p>' : '';

            $rows = collect($fields)->map(static function (array $field): string {
                if (! empty($field['is_signature'])) {
                    return '<p><b>'.e($field['label']).':</b><br>'.self::pdfText((string) $field['value']).'</p>';
                }

                return '<p><b>'.e($field['label']).':</b> '.nl2br(e((string) $field['value'])).'</p>';
            })->implode('');

            return $divider.$heading.$rows;
        })->implode('');
    }

    /**
     * TCPDF-friendly SWMS markup: titled blocks divided by a rule, simple tags only.
     */
    protected static function swmsPdfHtml(array $row): string
    {
        $instances = $row['instances'] ?? [];

        if ($instances === []) {
            return '&nbsp;';
        }

        return collect($instances)->map(static function (array $fields, int $i): string {
            $divider = $i > 0 ? '<hr>' : '';
            $heading = '<p><b>SWMS #'.($i + 1).'</b></p>';

            $rows = collect($fields)->map(static function (array $field): string {
                $value = $field['is_file']
                    ? self::pdfText((string) $field['value'])
                    : nl2br(e((string) $field['value']));

                return '<tr>'
                    .'<td width="40%" style="color:#4b5563;"><b>'.e($field['label']).'</b></td>'
                    .'<td width="60%">'.$value.'</td>'
                    .'</tr>';
            })->implode('');

            return $divider.$heading
                .'<table cellpadding="3" cellspacing="0" border="0" width="100%">'.$rows.'</table>';
        })->implode('');
    }

    /**
     * TCPDF-safe rendering of one list item / field value: embeds an inline signature/image
     * data URI as an <img> (validated) instead of dumping the raw base64 string.
     */
    protected static function pdfText(string $raw): string
    {
        $raw = trim($raw);

        if (preg_match('/^(?:([^:]{1,40}):\s*)?(data:image\/[a-z0-9.+-]+;base64,[A-Za-z0-9+\/=\s]+)$/i', $raw, $m)) {
            $label = trim((string) ($m[1] ?? ''));
            $src = (string) preg_replace('/\s+/', '', (string) $m[2]);

            if (preg_match('#^data:image/[a-z0-9.+-]+;base64,(.+)$#i', $src, $b)
                && ($bin = base64_decode($b[1], true)) !== false
                && @imagecreatefromstring($bin) !== false) {
                $img = '<img src="'.htmlspecialchars($src, ENT_QUOTES).'" width="200">';
            } else {
                $img = '[signature image]';
            }

            return ($label !== '' ? '<b>'.e($label).':</b><br>' : '').$img;
        }

        // Stored files ("form-uploads/…" paths or bare builder files "fb_doc_*"/"fb_img_*",
        // optionally "Label: file1, file2"): embed IMAGES from disk; documents become links.
        $isFileToken = fn (string $t): bool => str_contains($t, 'form-uploads/')
            || preg_match('/^fb_(doc|img)_[A-Za-z0-9_.-]+\.[A-Za-z0-9]{2,5}$/', $t) === 1;

        $label = '';
        $candidate = $raw;
        if (preg_match('/^([^:\/]{1,40}):\s*(.+)$/s', $raw, $lm)) {
            $label = trim($lm[1]);
            $candidate = trim($lm[2]);
        }

        $tokens = array_values(array_filter(array_map('trim', explode(',', $candidate))));

        if ($tokens === [] || ! collect($tokens)->every($isFileToken)) {
            // Retry without the label split (a filename itself never contains ':').
            $label = '';
            $tokens = array_values(array_filter(array_map('trim', explode(',', $raw))));
        }

        if ($tokens !== [] && collect($tokens)->every($isFileToken)) {
            $parts = array_map(fn (string $t): string => self::pdfFileHtml($t), $tokens);

            return ($label !== '' && strcasecmp($label, 'File') !== 0 ? '<b>'.e($label).':</b><br>' : '')
                .implode('<br>', $parts);
        }

        return nl2br(e($raw));
    }

    /**
     * One stored upload for the PDF: images embedded from the local disk (validated),
     * anything else a clickable link with the filename.
     */
    protected static function pdfFileHtml(string $path): string
    {
        $path = ltrim($path, '/');

        // Bare form-builder filenames (fb_doc_* / fb_img_*) live under form-builder/… .
        $path = FormAnswerHtml::builderFilePath($path) ?? $path;

        if (preg_match('/\.(png|jpe?g|gif|webp)$/i', $path)) {
            try {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    $abs = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
                    if (($info = @getimagesize($abs)) !== false
                        && ($bin = @file_get_contents($abs)) !== false && $bin !== '') {
                        $mime = $info['mime'] ?? 'image/png';

                        return '<img src="data:'.$mime.';base64,'.base64_encode($bin).'" width="200">';
                    }
                }
            } catch (\Throwable) {
                // fall through to the link
            }
        }

        return '<a href="'.e(asset('storage/'.$path)).'">'.e(basename($path)).'</a>';
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
