<?php

namespace App\Filament\Resources\ResellerCodes\Tables;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\ResellerCodes\ResellerCodeResource;
use App\Filament\Support\SearchTableFilter;
use App\Filament\Support\TableFilterDefaults;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
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
                    ->weight('bold')
                    // Click the code to view its usage history.
                    ->url(fn (Client $record): string => ResellerCodeResource::getUrl('history', ['record' => $record]))
                    ->color('primary'),
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
                // Single-column activate/deactivate: toggling flips reseller_code_active.
                ToggleColumn::make('reseller_code_active')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(fn (Client $record): bool => blank($record->reseller_code)
                        || ! Schema::hasColumn('clients', 'reseller_code_active')
                        // Read-only admins (e.g. Support role) cannot toggle.
                        || ! (auth()->user()?->admin_role?->canWrite() ?? false))
                    ->afterStateUpdated(function (Client $record, bool $state): void {
                        Notification::make()
                            ->title($state ? 'Reseller code activated' : 'Reseller code deactivated')
                            ->body($record->reseller_code.($state
                                ? ' is now applicable throughout the application.'
                                : ' is no longer applicable.'))
                            ->success()
                            ->send();
                    }),
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
                SelectFilter::make('client')
                    ->label('Client')
                    ->searchable()
                    ->options(fn (): array => Client::query()
                        ->hasResellerCode()
                        ->orderBy('client_name')
                        ->pluck('client_name', 'id')
                        ->all())
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        return filled($value) ? $query->whereKey($value) : $query;
                    }),
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
                Action::make('history')
                    ->label('History')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->url(fn (Client $record): string => ResellerCodeResource::getUrl('history', ['record' => $record])),
            ]));
    }
}
