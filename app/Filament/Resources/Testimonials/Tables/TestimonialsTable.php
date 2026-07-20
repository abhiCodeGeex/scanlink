<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Filament\Support\DateRangeTableFilter;
use App\Filament\Support\SearchTableFilter;
use App\Filament\Support\TableFilterDefaults;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return TableFilterDefaults::apply($table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SearchTableFilter::make(['title']),
                DateRangeTableFilter::make('created_at', 'Date range'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete this testimonial?'),
            ]));
    }
}
