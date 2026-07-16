<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class SearchTableFilter
{
    /**
     * @param  array<int, string>|string  $columns
     */
    public static function make(
        array|string $columns,
        string $name = 'search',
        string $label = 'Search',
        ?Closure $queryCallback = null,
    ): Filter {
        $columns = Arr::wrap($columns);

        return Filter::make($name)
            ->label($label)
            ->schema([
                TextInput::make('search')
                    ->label($label)
                    ->placeholder('Search'),
            ])
            ->query(function (Builder $query, array $data) use ($columns, $queryCallback): Builder {
                $value = $data['search'] ?? null;

                if (blank($value)) {
                    return $query;
                }

                if ($queryCallback instanceof Closure) {
                    return $queryCallback($query, (string) $value);
                }

                if ($columns === []) {
                    return $query;
                }

                $like = '%'.$value.'%';

                // Direct constraints only — nested where() closures break Filament relation-manager queries.
                if (count($columns) === 1) {
                    return $query->where($columns[0], 'like', $like);
                }

                $table = $query->getModel()?->getTable();

                if ($table === null) {
                    return $query->where($columns[0], 'like', $like);
                }

                $sql = '('.collect($columns)
                    ->map(fn (string $column): string => "`{$table}`.`{$column}` LIKE ?")
                    ->implode(' OR ').')';

                return $query->whereRaw($sql, array_fill(0, count($columns), $like));
            })
            ->indicateUsing(function (array $data) use ($label): array {
                if (blank($data['search'] ?? null)) {
                    return [];
                }

                return [
                    Indicator::make($label.': '.$data['search'])
                        ->removeField('search'),
                ];
            });
    }
}
