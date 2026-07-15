<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\PhysicalOrderStatus;
use App\Models\Order;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

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
                Filter::make('ordered_on')
                    ->label('Date range')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('from')
                            ->label('From')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('until')
                            ->label('To')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['from'] ?? null),
                                fn (Builder $builder): Builder => $builder->whereDate('ordered_on', '>=', $data['from']),
                            )
                            ->when(
                                filled($data['until'] ?? null),
                                fn (Builder $builder): Builder => $builder->whereDate('ordered_on', '<=', $data['until']),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if (filled($data['from'] ?? null)) {
                            $indicators['from'] = 'From '.Carbon::parse($data['from'])->format('d/m/Y');
                        }

                        if (filled($data['until'] ?? null)) {
                            $indicators['until'] = 'To '.Carbon::parse($data['until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
