<?php

namespace App\Filament\Resources\Profiles\Tables;

use App\Filament\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use App\Services\CodeProfileRenewalService;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class ProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Profile No.')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Note')
                    ->limit(40),
                TextColumn::make('contacts.name_company')
                    ->label('Contact Name/Company')
                    ->listWithLineBreaks()
                    ->limitList(1),
                TextColumn::make('contacts.telephone')
                    ->label('Telephone')
                    ->listWithLineBreaks()
                    ->limitList(1),
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
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->action(fn (Profile $record) => $record->update(['deleted' => true])),
            ])
            ->bulkActions([
                BulkAction::make('renewCodes')
                    ->label('Renew selected codes')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records): void {
                        $codeProfiles = $records
                            ->loadMissing('equipmentType')
                            ->filter(fn (Profile $profile): bool => $profile->typeSlug() === 'code');

                        if ($codeProfiles->isEmpty()) {
                            Notification::make()
                                ->title('Please select code to be renew.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $order = app(CodeProfileRenewalService::class)->renew($codeProfiles);

                        Notification::make()
                            ->title('Codes renewed')
                            ->body(new HtmlString(
                                $codeProfiles->count().' code(s) renewed — order #'.$order->id
                            ))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
