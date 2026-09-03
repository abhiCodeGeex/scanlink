<?php

namespace App\Filament\Forms\RichEditor\LegacyFormatting\Marks;

use Tiptap\Core\Mark;

/**
 * The legacy CKEditor "Font", "Size" and "Text Colour" controls, as one TipTap mark.
 *
 * One mark rather than three, because legacy writes both shapes and only a combined mark reads
 * both:
 *
 *     <span style="font-family:verdana"><span style="color:#333">…   (nested)
 *     <span style="font-family:georgia,serif; font-size:13px">…      (combined — 15 rows)
 *
 * With three separate marks all matching `span`, only the first applies to a combined span and
 * the other declarations are dropped. That accounted for every row still losing formatting in
 * the full-table sweep.
 *
 * Values are stored and rendered as the plain inline styles legacy already uses, so the 403
 * profiles holding CKEditor markup round-trip byte-compatibly, and the public scan page — which
 * echoes the column raw and loads none of Filament's CSS — renders them without change.
 */
class LegacyStyleMark extends Mark
{
    /**
     * @var string
     */
    public static $name = 'legacyStyle';

    /**
     * Attribute name => CSS declaration.
     *
     * @var array<string, string>
     */
    public const DECLARATIONS = [
        'fontFamily' => 'font-family',
        'fontSize' => 'font-size',
        'color' => 'color',
    ];

    /**
     * Inline tags legacy nests between spans, e.g.
     *   <span style="color:#2F4F4F"><span style="font-size:20px"><strong><span ...>
     * The ancestor walk has to step over these rather than stop at them.
     *
     * @var array<int, string>
     */
    private const INLINE_TAGS = [
        'span', 'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'sub', 'sup', 'a', 'font',
        'small', 'mark',
    ];

    /**
     * @return array<array<string, mixed>>
     */
    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'span',
                'getAttrs' => fn ($DOMNode): bool => ! static::isRedundantWrapperSpan($DOMNode)
                    && filled(static::readWithAncestors($DOMNode)),
            ],
        ];
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function addAttributes(): array
    {
        $attributes = [];

        foreach (array_keys(self::DECLARATIONS) as $name) {
            $attributes[$name] = [
                'parseHTML' => fn ($DOMNode) => static::readWithAncestors($DOMNode)[$name] ?? null,
                // Rendering is handled in renderHTML() so the declarations end up in a single
                // style attribute instead of overwriting one another.
                'renderHTML' => fn ($attributes): array => [],
            ];
        }

        return $attributes;
    }

    /**
     * @param  object  $mark
     * @param  array<string, mixed>  $HTMLAttributes
     * @return array<mixed>
     */
    public function renderHTML($mark, $HTMLAttributes = []): array
    {
        $declarations = [];

        foreach (self::DECLARATIONS as $name => $property) {
            $value = static::attribute($mark->attrs ?? null, $name)
                ?? static::attribute($HTMLAttributes, $name);

            if (filled($value)) {
                $declarations[] = $property.':'.$value;
            }
        }

        $attributes = array_diff_key($HTMLAttributes, self::DECLARATIONS);

        if ($declarations) {
            $existing = trim((string) ($attributes['style'] ?? ''), " ;\t\n");
            $attributes['style'] = implode('; ', array_filter([
                implode('; ', $declarations),
                $existing,
            ]));
        }

        return ['span', $attributes, 0];
    }

    /**
     * Split an inline style into the declarations we care about. Legacy packs several into one
     * attribute (`color: rgb(20, 24, 35); font-family: arial; font-size: 14px`), and `rgb()`
     * contains commas, so each is matched on its own rather than by splitting the string.
     *
     * `color` must not match `background-color` or `border-color`, hence the leading boundary.
     *
     * @return array<string, string>
     */
    public static function read(string $style): array
    {
        $found = [];

        foreach (self::DECLARATIONS as $name => $property) {
            if (preg_match('/(?:^|;)\s*'.preg_quote($property, '/').'\s*:\s*([^;]+)/i', $style, $matches)) {
                $value = trim($matches[1]);

                if ($value !== '') {
                    $found[$name] = $value;
                }
            }
        }

        return $found;
    }

    /**
     * A wrapper span that only exists to hold a styled inner span - no text of its own.
     *
     * Legacy nests these (`<span style="font-family:x"><span style="color:y">text</span></span>`).
     * Both spans would otherwise create a mark over the same text; a mark type can only apply
     * once per range, so the two compete and whichever is read first wins - editing then loses
     * the other's declarations. readWithAncestors() already folds the outer declarations into
     * the inner mark, so the wrapper is redundant.
     *
     * Only skipped when it has no direct text: a span with both its own text and a styled child
     * still needs its own mark, or that text loses its formatting.
     */
    public static function isRedundantWrapperSpan(mixed $DOMNode): bool
    {
        foreach ($DOMNode->childNodes ?? [] as $child) {
            if ($child->nodeType === XML_TEXT_NODE && trim((string) $child->textContent) !== '') {
                return false;
            }
        }

        foreach ($DOMNode->getElementsByTagName('span') as $span) {
            if (filled(static::read((string) $span->getAttribute('style')))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Merge this span's declarations with those of any ancestor spans, inner winning.
     *
     * Legacy writes nested spans as well as combined ones:
     *
     *     <span style="font-family:georgia"><span style="color:#003366">...
     *
     * Only one mark of a given type can apply to a range, so parsing each span in isolation
     * makes the inner one win and discards the outer declarations - change the colour and the
     * font disappears. Merging ancestors means the innermost span carries the full set.
     *
     * @return array<string, string>
     */
    public static function readWithAncestors(mixed $DOMNode): array
    {
        $merged = static::read((string) $DOMNode->getAttribute('style'));

        $parent = $DOMNode->parentNode ?? null;

        while ($parent !== null && in_array(strtolower((string) ($parent->nodeName ?? '')), self::INLINE_TAGS, true)) {
            if (strtolower((string) $parent->nodeName) === 'span') {
                $merged = [
                    ...static::read((string) $parent->getAttribute('style')),
                    ...$merged,
                ];
            }

            $parent = $parent->parentNode ?? null;
        }

        return $merged;
    }

    /**
     * TipTap PHP passes attributes as an array in some paths and an object in others.
     */
    public static function attribute(mixed $attributes, string $key): ?string
    {
        $value = match (true) {
            is_array($attributes) => $attributes[$key] ?? null,
            is_object($attributes) => $attributes->{$key} ?? null,
            default => null,
        };

        return filled($value) ? (string) $value : null;
    }
}
