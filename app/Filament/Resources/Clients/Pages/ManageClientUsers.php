<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Tables\ClientUsersTable;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ManageClientUsers extends ManageRelatedRecords
{
    protected static string $resource = ClientResource::class;

    protected static string $relationship = 'subUsers';

    protected static ?string $navigationLabel = 'Manage User';

    protected static ?string $title = 'Manage User';

    public function getTitle(): string | Htmlable
    {
        return 'Manage User';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    public function table(Table $table): Table
    {
        return ClientUsersTable::configure($table, includeStatusColumns: true)
            ->headerActions([
                ClientUsersTable::createAction(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
