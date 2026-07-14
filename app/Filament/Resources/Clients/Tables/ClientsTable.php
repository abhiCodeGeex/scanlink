<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('client_name')
            ->columns([
                TextColumn::make('client_name')
                    ->label('Client Name')
                    // Legacy shows a green "R" badge when a reseller code is set.
                    ->badge(fn (Client $record): bool => filled($record->reseller_code))
                    ->formatStateUsing(fn (string $state, Client $record): string => filled($record->reseller_code) ? "R  {$state}" : $state)
                    ->color(fn (Client $record): string => filled($record->reseller_code) ? 'success' : 'gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Address'),
                TextColumn::make('telephone')
                    ->label('Telephone'),
                TextColumn::make('contact_person')
                    ->label('Contact Person'),
                TextColumn::make('regi_date')
                    ->label('Registration Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('URL'),
            ])
            ->filters([
                Filter::make('client_name')
                    ->label('Filter')
                    ->schema([
                        TextInput::make('value')
                            ->label('Filter'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $builder): Builder => $builder->where('client_name', 'like', '%'.$data['value'].'%'),
                    )),
            ])
            ->recordActions([
                // Legacy row options: Users list, Edit, Block/Unblock, Delete.
                Action::make('users')
                    ->label('Users')
                    ->icon('heroicon-o-users')
                    ->color('gray')
                    ->url(fn (Client $record): string => ClientResource::getUrl('users', ['record' => $record])),
                EditAction::make(),
                Action::make('toggleApproval')
                    ->label(fn (Client $record): string => $record->approve ? 'Block' : 'Unblock')
                    ->icon(fn (Client $record): string => $record->approve ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn (Client $record): string => $record->approve ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Client $record): string => $record->approve ? 'Block this client?' : 'Unblock this client?')
                    ->action(fn (Client $record) => $record->update(['approve' => ! $record->approve])),
                DeleteAction::make()
                    ->label('Delete')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Delete this client?')
                    ->modalDescription('This soft-deletes the client. Related data is kept.'),
            ]);
    }
}
