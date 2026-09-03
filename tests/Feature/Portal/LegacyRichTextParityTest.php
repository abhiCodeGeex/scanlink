<?php

namespace Tests\Feature\Portal;

use App\Filament\Forms\RichEditor\LegacyFormatting\LegacyFormattingPlugin;
use App\Filament\Forms\RichEditor\LegacyFormatting\Marks\LegacyStyleMark;
use App\Support\LegacyEditorToolbars;
use App\Support\PortalProfilePreview;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The legacy Font / Size / Colour controls (ckeditor/config.js:9-15) have no Filament
 * equivalent, and Filament's own `textColor` only understands its own `class="color"` markup.
 * Without the LegacyFormatting plugin, opening and saving any of the 403 profiles whose
 * `description` still holds CKEditor markup silently strips those three declarations.
 *
 * These cases pin the shapes that actually occur in the live import.
 */
class LegacyRichTextParityTest extends TestCase
{
    private function render(string $html): string
    {
        return RichContentRenderer::make()
            ->plugins([LegacyFormattingPlugin::make()])
            ->getEditor()
            ->setContent($html)
            ->getHtml();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function legacyMarkupProvider(): array
    {
        return [
            // Every shape observed in profiles.description across the live import.
            'nested spans' => ['<p><span style="font-family:verdana,geneva,sans-serif"><span style="color:rgb(128, 128, 128)"><span style="font-size:14px">Hi</span></span></span></p>'],
            'combined declarations in one span' => ['<p><span style="font-family:georgia,serif; font-size:13px">Hi</span></p>'],
            'nested through an inline tag' => ['<p><span style="color:#2F4F4F"><span style="font-size:20px"><strong><span style="font-size:18px">Hi</span></strong></span></span></p>'],
            'colour written as rgb()' => ['<p><span style="color:rgb(20, 24, 35)">Hi</span></p>'],
            'alignment alongside styling' => ['<p style="text-align:center"><span style="font-size:20px"><span style="color:#003366">Hi</span></span></p>'],
        ];
    }

    #[DataProvider('legacyMarkupProvider')]
    public function test_legacy_font_size_and_colour_survive_a_round_trip(string $html): void
    {
        $rendered = $this->render($html);

        foreach (['font-family', 'font-size', 'color'] as $declaration) {
            if (! str_contains($html, $declaration)) {
                continue;
            }

            $this->assertStringContainsString(
                $declaration,
                $rendered,
                "[{$declaration}] was stripped from: {$html}",
            );
        }

        $this->assertStringContainsString('Hi', $rendered);
    }

    public function test_without_the_plugin_the_declarations_are_stripped(): void
    {
        // Guards the reason the plugin exists: if Filament ever parses these natively this
        // test fails and the plugin can be reconsidered.
        $html = '<p><span style="font-family:georgia,serif; font-size:13px; color:#003366">Hi</span></p>';

        $rendered = RichContentRenderer::make()->getEditor()->setContent($html)->getHtml();

        $this->assertStringNotContainsString('font-family', $rendered);
        $this->assertStringNotContainsString('font-size', $rendered);
        $this->assertStringNotContainsString('color', $rendered);
    }

    public function test_values_are_rendered_as_legacy_inline_styles(): void
    {
        // Not Filament's class="color" + CSS-variable format: the public scan page echoes the
        // column raw and loads none of Filament's CSS, so that shape would render unstyled.
        $rendered = $this->render('<p><span style="color:#003366">Hi</span></p>');

        $this->assertStringContainsString('style="color:#003366"', $rendered);
        $this->assertStringNotContainsString('class="color"', $rendered);
        $this->assertStringNotContainsString('--color', $rendered);
    }

    public function test_nested_spans_collapse_into_a_single_mark(): void
    {
        // Two marks of one type over the same text compete, and editing then drops whichever
        // the editor did not read — that is how changing the size used to clear the colour.
        $rendered = $this->render(
            '<p><span style="font-family:georgia,serif"><span style="color:#003366">Hi</span></span></p>'
        );

        $this->assertSame(1, substr_count($rendered, '<span'), "expected one span, got: {$rendered}");
        $this->assertStringContainsString('font-family:georgia,serif', $rendered);
        $this->assertStringContainsString('color:#003366', $rendered);
    }

    public function test_background_and_border_colour_are_not_mistaken_for_text_colour(): void
    {
        $this->assertSame([], LegacyStyleMark::read('background-color:#fff; border-color:#000'));
        $this->assertSame(['color' => '#003366'], LegacyStyleMark::read('color:#003366'));
    }

    public function test_each_editor_gets_its_legacy_toolbar(): void
    {
        // MyToolbar (description, description2) and custom (VOC signature) include Link;
        // custom1 (VOC title bar) deliberately does not — matching ckeditor/config.js:25-31.
        foreach ([LegacyEditorToolbars::myToolbar(), LegacyEditorToolbars::custom()] as $preset) {
            $buttons = array_merge(...$preset);

            foreach (['fontFamily', 'fontSize', 'legacyTextColor', 'bold', 'italic', 'underline', 'link', 'alignStart', 'alignCenter', 'alignEnd'] as $expected) {
                $this->assertContains($expected, $buttons);
            }
        }

        $custom1 = array_merge(...LegacyEditorToolbars::custom1());

        $this->assertContains('fontSize', $custom1);
        $this->assertContains('legacyTextColor', $custom1);
        $this->assertNotContains('link', $custom1);
    }

    public function test_phone_preview_renders_the_new_marks_and_real_heading_levels(): void
    {
        // The preview has its own hand-written TipTap->HTML converter; anything it does not
        // know is dropped, so every toolbar addition has to be taught to it as well.
        $html = PortalProfilePreview::tipTapToHtml([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 2, 'textAlign' => 'center'],
                    'content' => [[
                        'type' => 'text',
                        'text' => 'Hi',
                        'marks' => [[
                            'type' => 'legacyStyle',
                            'attrs' => ['fontFamily' => 'georgia,serif', 'fontSize' => '20px', 'color' => '#003366'],
                        ]],
                    ]],
                ],
            ],
        ]);

        $this->assertStringContainsString('<h2', $html);
        $this->assertStringContainsString('text-align:center', $html);
        $this->assertStringContainsString('font-family:georgia,serif', $html);
        $this->assertStringContainsString('font-size:20px', $html);
        $this->assertStringContainsString('color:#003366', $html);
    }
}
