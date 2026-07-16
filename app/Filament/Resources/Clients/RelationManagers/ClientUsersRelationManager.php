<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Clients\Tables\ClientUsersTable;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Attributes\On;

class ClientUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'subUsers';

    protected static ?string $title = 'Users';

    protected static bool $isLazy = false;

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
                    // Legacy: password only updates when a new value is entered.
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

    #[On('client-users-table-refresh')]
    public function refreshUsersTable(): void
    {
        if (method_exists($this, 'flushCachedTableRecords')) {
            $this->flushCachedTableRecords();

            return;
        }

        $this->resetTable();
    }
}
