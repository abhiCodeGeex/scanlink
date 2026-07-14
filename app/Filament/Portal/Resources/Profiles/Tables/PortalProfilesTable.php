<?php

namespace App\Filament\Portal\Resources\Profiles\Tables;

use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PortalProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Profile No.')
                    ->sortable(),
                TextColumn::make('equipmentType.name')
                    ->label('Type')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expired_at')
                    ->label('Expires')
                    ->dateTime('d/m/Y')
                    ->placeholder('-'),
            ])
            ->filters([
                Filter::make('name')
                    ->label('Filter')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Filter'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['name'] ?? null),
                        fn (Builder $q): Builder => $q->where('name', 'like', '%'.$data['name'].'%'),
                    )),
                SelectFilter::make('type_id')
                    ->label('Type')
                    ->relationship('equipmentType', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->url(fn (Profile $record): string => ProfileResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->label('Archive')
                    ->requiresConfirmation()
                    ->modalHeading('Archive profile')
                    ->action(fn (Profile $record) => $record->update(['deleted' => true])),
            ]);
    }
}
