<?php

namespace App\Filament\Resources\CodePurchases\Tables;

use App\Enums\CodeOrderStatus;
use App\Filament\Support\DateRangeTableFilter;
use App\Filament\Support\SearchTableFilter;
use App\Filament\Support\TableFilterDefaults;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CodePurchasesTable
{
    public static function configure(Table $table): Table
    {
        return TableFilterDefaults::apply($table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Code ID')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->state(fn ($record): string => $record->fullName())
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('status')
                    ->label('Order Type')
                    ->badge()
                    ->formatStateUsing(fn (CodeOrderStatus $state): string => $state->label())
                    ->color(fn (CodeOrderStatus $state): string => $state->color()),
                TextColumn::make('no_of_codes')
                    ->label('No. Codes')
                    ->sortable(),
            ])
            ->filters([
                SearchTableFilter::make(['first_name', 'last_name']),
                SelectFilter::make('status')
                    ->label('View Orders by Status')
                    ->options(array_merge(
                        ['all' => 'All'],
                        CodeOrderStatus::filterOptions(),
                    ))
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? 'all';

                        if ($value === 'all' || $value === null || $value === '') {
                            return $query;
                        }

                        return $query->where('status', $value);
                    })
                    ->default('all'),
                DateRangeTableFilter::make('created_at', 'Date range'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]));
    }
}
