<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Tables\Table;

class TableFilterDefaults
{
    public static function apply(Table $table): Table
    {
        return $table
            ->filtersApplyAction(
                fn (Action $action): Action => $action
                    ->label('Apply filters')
                    ->color('primary')
                    ->extraAttributes(['data-scanlink-apply-filters' => 'true']),
            )
            ->filtersTriggerAction(
                fn (Action $action): Action => $action
                    ->modalCancelActionLabel('Cancel'),
            );
    }
}
