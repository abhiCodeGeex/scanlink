<?php

namespace App\Filament\Support;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DateRangeTableFilter
{
    public static function make(string $column, string $label = 'Date range'): Filter
    {
        return Filter::make($column)
            ->label($label)
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
            ->query(function (Builder $query, array $data) use ($column): Builder {
                return $query
                    ->when(
                        filled($data['from'] ?? null),
                        fn (Builder $builder): Builder => $builder->whereDate($column, '>=', $data['from']),
                    )
                    ->when(
                        filled($data['until'] ?? null),
                        fn (Builder $builder): Builder => $builder->whereDate($column, '<=', $data['until']),
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
            });
    }
}
