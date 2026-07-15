<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('client_name')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withCount('subUsers'))
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
                Filter::make('regi_date')
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
                                fn (Builder $builder): Builder => $builder->whereDate('regi_date', '>=', $data['from']),
                            )
                            ->when(
                                filled($data['until'] ?? null),
                                fn (Builder $builder): Builder => $builder->whereDate('regi_date', '<=', $data['until']),
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
                // Legacy row options: Users list, Edit, Block/Unblock, Delete.
                Action::make('users')
                    ->label(fn (Client $record): string => 'Users ('.(int) ($record->sub_users_count ?? $record->subUsers()->count()).')')
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
