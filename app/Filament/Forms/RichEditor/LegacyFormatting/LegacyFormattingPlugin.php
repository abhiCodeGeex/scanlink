<?php

namespace App\Filament\Forms\RichEditor\LegacyFormatting;

use App\Filament\Forms\RichEditor\LegacyFormatting\Actions\FontFamilyAction;
use App\Filament\Forms\RichEditor\LegacyFormatting\Actions\FontSizeAction;
use App\Filament\Forms\RichEditor\LegacyFormatting\Actions\TextColorAction;
use App\Filament\Forms\RichEditor\LegacyFormatting\Marks\LegacyStyleMark;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Icons\Heroicon;

/**
 * Restores the three legacy CKEditor toolbar controls Filament has no equivalent for:
 * Font, Size and Text Colour (ckeditor/config.js:9-15 in the Kohana app).
 *
 * Everything is stored as the inline styles legacy already writes, so the 403 profiles whose
 * `description` still holds CKEditor markup round-trip unchanged instead of being stripped.
 */
class LegacyFormattingPlugin implements RichContentPlugin
{
    /**
     * Bump when the JS in public/js/scanlink/rich-editor/ changes, so browsers do not serve a
     * cached copy. Filament fetches these URLs with a dynamic import at runtime.
     */
    private const ASSET_VERSION = '7';

    /**
     * Legacy CKEditor 4 font list, minus the faces that do not survive a phone.
     *
     * What gets authored here is rendered on the scanned profile page, which is read almost
     * entirely on mobile. Every entry below ends in a generic family (sans-serif / serif /
     * monospace) that iOS and Android both resolve, so the worst case is a near neighbour
     * rather than a surprise.
     *
     * Deliberately dropped:
     *  - Comic Sans MS — ships on neither iOS nor Android, and its `cursive` fallback has no
     *    reliable mobile face either, so the phone picks whatever it likes.
     *  - Lucida Sans Unicode — absent from both mobile platforms; the menu entry promised a
     *    face the reader would never see.
     *
     * Neither appears in any stored content, so no existing value loses its dropdown entry.
     * Tahoma is kept: it is in live content, and it does fall back cleanly to sans-serif.
     *
     * @var array<string, string>
     */
    public const FONT_FAMILIES = [
        'Arial, Helvetica, sans-serif' => 'Arial',
        'Courier New, Courier, monospace' => 'Courier New',
        'Georgia, serif' => 'Georgia',
        'Tahoma, Geneva, sans-serif' => 'Tahoma',
        'Times New Roman, Times, serif' => 'Times New Roman',
        'Trebuchet MS, Helvetica, sans-serif' => 'Trebuchet MS',
        'Verdana, Geneva, sans-serif' => 'Verdana',
    ];

    /**
     * Legacy CKEditor 4 default size list.
     *
     * @var array<string, string>
     */
    public const FONT_SIZES = [
        '8px' => '8', '9px' => '9', '10px' => '10', '11px' => '11', '12px' => '12',
        '14px' => '14', '16px' => '16', '18px' => '18', '20px' => '20', '22px' => '22',
        '24px' => '24', '26px' => '26', '28px' => '28', '36px' => '36', '48px' => '48',
        '72px' => '72',
    ];

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<\Tiptap\Core\Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            app(LegacyStyleMark::class),
        ];
    }

    /**
     * Filament resolves each entry with `(await import(url)).default` in the browser, so these
     * are URLs rather than names. The modules pull TipTap off window.FilamentRichEditor, which
     * is why they need no bundling.
     *
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [
            asset('js/scanlink/rich-editor/legacy-style.js').'?v='.self::ASSET_VERSION,
        ];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('fontFamily')
                ->label('Font')
                ->icon(Heroicon::Language)
                ->action(arguments: '{ fontFamily: $getEditor().getAttributes(\'legacyStyle\')?.fontFamily ?? null }'),
            RichEditorTool::make('fontSize')
                ->label('Size')
                ->icon(Heroicon::ArrowsUpDown)
                ->action(arguments: '{ fontSize: $getEditor().getAttributes(\'legacyStyle\')?.fontSize ?? null }'),
            RichEditorTool::make('legacyTextColor')
                ->label('Text colour')
                ->icon(Heroicon::Swatch)
                ->action(arguments: '{ color: $getEditor().getAttributes(\'legacyStyle\')?.color ?? null }'),
        ];
    }

    /**
     * @return array<\Filament\Actions\Action>
     */
    public function getEditorActions(): array
    {
        return [
            FontFamilyAction::make(),
            FontSizeAction::make(),
            TextColorAction::make(),
        ];
    }
}
