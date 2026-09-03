<?php

namespace App\Filament\Forms\RichEditor\LegacyFormatting\Actions;

use App\Filament\Forms\RichEditor\LegacyFormatting\LegacyFormattingPlugin;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Width;

/**
 * Modal behind the toolbar's "Size" button. Mirrors Filament's own TextColorAction, including
 * the `extendMarkRange` step that makes the change apply to the whole run when the caret is
 * sitting inside styled text rather than a selection.
 */
class FontSizeAction
{
    public static function make(): Action
    {
        return Action::make('fontSize')
            ->label('Size')
            ->modalHeading('Font size')
            ->modalWidth(Width::Medium)
            ->modalSubmitActionLabel('Apply')
            ->fillForm(fn (array $arguments): array => [
                'fontSize' => $arguments['fontSize'] ?? null,
            ])
            ->schema([
                Select::make('fontSize')
                    ->label('Size')
                    ->options(LegacyFormattingPlugin::FONT_SIZES)
                    ->native(false)
                    ->placeholder('Default'),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                $value = $data['fontSize'] ?? null;

                // With the caret inside styled text rather than a selection, widen to the
                // whole run so the change applies to it, matching Filament's own tools.
                $isCaretOnly = ($arguments['editorSelection']['head'] ?? null)
                    === ($arguments['editorSelection']['anchor'] ?? null);

                $component->runCommands(
                    [
                        ...($isCaretOnly ? [EditorCommand::make(
                            'extendMarkRange',
                            arguments: ['legacyStyle'],
                        )] : []),
                        // setLegacyStyle merges into the existing mark and removes it
                        // entirely once every declaration is cleared.
                        EditorCommand::make('setLegacyStyle', arguments: [[
                            'fontSize' => blank($value) ? null : $value,
                        ]]),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
