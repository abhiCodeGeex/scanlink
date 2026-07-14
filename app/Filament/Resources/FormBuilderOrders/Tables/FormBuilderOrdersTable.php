<?php

namespace App\Filament\Resources\FormBuilderOrders\Tables;

use App\Enums\CodeOrderStatus;
use App\Models\FormBuilderOrder;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FormBuilderOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->state(fn (FormBuilderOrder $record): string => $record->fullName())
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('postal_code')->label('Zipcode'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('phone')->label('Phone no'),
                TextColumn::make('status')
                    ->label('Order Type')
                    ->badge()
                    ->formatStateUsing(fn (CodeOrderStatus $state): string => $state->label())
                    ->color(fn (CodeOrderStatus $state): string => $state->color()),
                TextColumn::make('profile_id')
                    ->label('Profile ID')
                    ->state(fn (FormBuilderOrder $record): string => (string) ($record->details->first()?->profile_id ?? '-')),
            ])
            ->filters([
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
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
