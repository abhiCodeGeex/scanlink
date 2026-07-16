<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\PhysicalOrderStatus;
use App\Filament\Support\DateRangeTableFilter;
use App\Models\Order;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('ordered_on', 'desc')
            ->columns([
                TextColumn::make('profile_id')
                    ->label('Code Number')
                    ->sortable(),
                TextColumn::make('ordered_on')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->state(fn (Order $record): string => $record->fullName())
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('zip')
                    ->label('Zipcode'),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('contact')
                    ->label('Phone no'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (PhysicalOrderStatus $state): string => $state->label())
                    ->color(fn (PhysicalOrderStatus $state): string => $state->color()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('View Orders by Status')
                    ->options(array_merge(
                        ['all' => 'All'],
                        PhysicalOrderStatus::filterOptions(),
                    ))
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? 'all';

                        if ($value === 'all' || $value === null || $value === '') {
                            return $query;
                        }

                        return $query->where('status', $value);
                    })
                    ->default('all'),
                DateRangeTableFilter::make('ordered_on', 'Date range'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
