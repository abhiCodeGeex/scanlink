<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Profiles\ProfileResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'profiles';

    protected static ?string $title = 'Code profiles';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->active()->with('equipmentType'))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('equipmentType.name')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('code_profile_name')
                    ->label('Code name'),
                TextColumn::make('expired_at')
                    ->dateTime()
                    ->label('Expires'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => ProfileResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
