<?php

namespace App\Support;

/**
 * The three CKEditor toolbars the legacy app defines in `ckeditor/config.js`, expressed as
 * Filament RichEditor button groups. Kept in one place so the six editors that use them cannot
 * drift apart the way they had (all six previously shared one hard-coded 8-button list with no
 * colour, alignment, font or size).
 *
 * Legacy → Filament mapping notes:
 *  - "Font", "Size" and "TextColor" come from App\Filament\Forms\RichEditor\LegacyFormatting;
 *    Filament has no built-in equivalent for the first two, and its own `textColor` writes a
 *    format the public scan page cannot style.
 *  - "Format" becomes paragraph + h1-h3. Filament renders one button per heading level rather
 *    than a dropdown, so the full h1-h6 range would crowd the toolbar; h4-h6 are unused in the
 *    live data.
 *  - "Styles" is dropped: legacy ships CKEditor's stock, uncustomised stylesSet and nothing in
 *    the app overrides it.
 *  - bulletList / orderedList / undo / redo are not in the legacy toolbars, but they were in
 *    the previous Filament toolbar and removing shipped functionality would be a regression.
 */
class LegacyEditorToolbars
{
    /**
     * Every editor using these presets must also register the plugin that provides
     * fontFamily / fontSize / legacyTextColor.
     */
    public const PLUGIN = \App\Filament\Forms\RichEditor\LegacyFormatting\LegacyFormattingPlugin::class;

    /**
     * `config.toolbar_MyToolbar` — the default, used by every `class="ckeditor"` textarea:
     * Styles, Format, Font, FontSize, TextColor | Bold, Italic, Underline | Link, Unlink |
     * JustifyLeft, JustifyCenter, JustifyRight.
     *
     * Applies to `description` (asset, misc, exhibit) and `description2` (exhibit).
     *
     * @return array<int, array<int, string>>
     */
    public static function myToolbar(): array
    {
        return [
            ['paragraph', 'h1', 'h2', 'h3'],
            ['fontFamily', 'fontSize', 'legacyTextColor'],
            ['bold', 'italic', 'underline'],
            ['link'],
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['bulletList', 'orderedList'],
            ['undo', 'redo'],
        ];
    }

    /**
     * `config.toolbar_custom` — Bold, Italic, Underline | TextColor | Link, Unlink |
     * Justify… | Font, FontSize.
     *
     * Applies to `voc_email_sign_line1` (VOC email signature).
     *
     * @return array<int, array<int, string>>
     */
    public static function custom(): array
    {
        return [
            ['bold', 'italic', 'underline'],
            ['legacyTextColor'],
            ['link'],
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['fontFamily', 'fontSize'],
            ['bulletList', 'orderedList'],
            ['undo', 'redo'],
        ];
    }

    /**
     * `config.toolbar_custom1` — FontSize, TextColor | Bold, Italic, Underline | Justify….
     * Note there is deliberately no Link here, matching legacy.
     *
     * Applies to `voc_title_bar_text` (VOC title bar).
     *
     * @return array<int, array<int, string>>
     */
    public static function custom1(): array
    {
        return [
            ['fontSize', 'legacyTextColor'],
            ['bold', 'italic', 'underline'],
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['bulletList', 'orderedList'],
            ['undo', 'redo'],
        ];
    }
}
