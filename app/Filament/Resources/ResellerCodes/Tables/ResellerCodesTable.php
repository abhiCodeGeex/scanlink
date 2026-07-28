<?php

namespace App\Filament\Resources\ResellerCodes\Tables;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Support\SearchTableFilter;
use App\Filament\Support\TableFilterDefaults;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class ResellerCodesTable
{
    public static function configure(Table $table): Table
    {
        return TableFilterDefaults::apply($table
            ->defaultSort('client_name')
            ->columns([
                TextColumn::make('reseller_code')
                    ->label('Reseller code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('client_name')
                    ->label('Client (owner)')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Client $record): ?string => $record->email ?: null)
                    ->url(fn (Client $record): string => ClientResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('telephone')
                    ->label('Phone')
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('contact_person')
                    ->label('Contact person')
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('reseller_email')
                    ->label('Reseller email')
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('reseller_code_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (Client $record): string => $record->isResellerCodeActive() ? 'Active' : 'Inactive')
                    ->color(fn (Client $record): string => $record->isResellerCodeActive() ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SearchTableFilter::make(
                    ['reseller_code', 'client_name', 'email', 'telephone', 'contact_person'],
                    label: 'Search',
                ),
                SelectFilter::make('reseller_code_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        if ($value === null || $value === '' || ! Schema::hasColumn('clients', 'reseller_code_active')) {
                            return $query;
                        }

                        return $query->where('reseller_code_active', $value);
                    }),
            ])
            ->recordActions([
                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Activate reseller code')
                    ->modalDescription(fn (Client $record): string => 'Activate code "'.$record->reseller_code.'" for client '.$record->client_name.'? It will be usable for purchase and registration.')
                    ->visible(fn (Client $record): bool => filled($record->reseller_code) && ! $record->isResellerCodeActive())
                    ->action(function (Client $record): void {
                        if (! Schema::hasColumn('clients', 'reseller_code_active')) {
                            Notification::make()
                                ->title('Activate column missing — run migrations.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update(['reseller_code_active' => true]);

                        Notification::make()
                            ->title('Reseller code activated')
                            ->body($record->reseller_code.' is now applicable throughout the application.')
                            ->success()
                            ->send();
                    }),
                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Deactivate reseller code')
                    ->modalDescription(fn (Client $record): string => 'Deactivate code "'.$record->reseller_code.'"? It will no longer be accepted for purchase or registration.')
                    ->visible(fn (Client $record): bool => filled($record->reseller_code) && $record->isResellerCodeActive())
                    ->action(function (Client $record): void {
                        if (! Schema::hasColumn('clients', 'reseller_code_active')) {
                            Notification::make()
                                ->title('Activate column missing — run migrations.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update(['reseller_code_active' => false]);

                        Notification::make()
                            ->title('Reseller code deactivated')
                            ->body($record->reseller_code.' is no longer applicable.')
                            ->success()
                            ->send();
                    }),
                Action::make('editClient')
                    ->label('Client')
                    ->icon('heroicon-o-building-office-2')
                    ->color('gray')
                    ->url(fn (Client $record): string => ClientResource::getUrl('edit', ['record' => $record])),
            ]));
    }
}
