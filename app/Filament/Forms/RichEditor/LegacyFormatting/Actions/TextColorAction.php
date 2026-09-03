<?php

namespace App\Filament\Forms\RichEditor\LegacyFormatting\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\ColorPicker;
use Filament\Support\Enums\Width;

/**
 * Modal behind the toolbar's colour swatch. Mirrors Filament's own TextColorAction, including
 * the `extendMarkRange` step that makes the change apply to the whole run when the caret is
 * sitting inside styled text rather than a selection.
 */
class TextColorAction
{
    public static function make(): Action
    {
        return Action::make('legacyTextColor')
            ->label('Text colour')
            ->modalHeading('Text colour')
            ->modalWidth(Width::Medium)
            ->modalSubmitActionLabel('Apply')
            ->fillForm(fn (array $arguments): array => [
                'color' => $arguments['color'] ?? null,
            ])
            ->schema([
                ColorPicker::make('color')
                    ->label('Colour')
                    ->placeholder('Default'),
            ])
            ->action(function (array $arguments, array $data, RichEditor $component): void {
                $value = $data['color'] ?? null;

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
                            'color' => blank($value) ? null : $value,
                        ]]),
                    ],
                    editorSelection: $arguments['editorSelection'],
                );
            });
    }
}
