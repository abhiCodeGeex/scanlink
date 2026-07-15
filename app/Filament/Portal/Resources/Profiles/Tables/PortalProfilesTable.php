<?php

namespace App\Filament\Portal\Resources\Profiles\Tables;

use App\Filament\Portal\Pages\FormSubmissions;
use App\Filament\Portal\Pages\OrderLabel;
use App\Filament\Portal\Pages\ScanAnalytics;
use App\Filament\Portal\Pages\VisitorLog;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\Profile;
use App\Services\ProfileQrService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->url(fn (Profile $record): string => ProfileResource::getUrl('edit', ['record' => $record])),
                    Action::make('downloadQr')
                        ->label('Download QR')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn (Profile $record): mixed => app(ProfileQrService::class)->downloadQrImage($record)),
                    Action::make('scanAnalytics')
                        ->label('Scan Analytics')
                        ->icon('heroicon-o-chart-bar')
                        ->url(fn (Profile $record): string => ScanAnalytics::getUrl().'?profile='.$record->id),
                    Action::make('visitorLog')
                        ->label('Visitor Log')
                        ->icon('heroicon-o-user-group')
                        ->url(fn (Profile $record): string => VisitorLog::getUrl().'?profile='.$record->id),
                    Action::make('formSubmissions')
                        ->label('Form Submissions')
                        ->icon('heroicon-o-inbox-arrow-down')
                        ->url(fn (Profile $record): string => FormSubmissions::getUrl().'?profile='.$record->id),
                    Action::make('orderLabels')
                        ->label('Order Labels')
                        ->icon('heroicon-o-tag')
                        ->url(fn (Profile $record): string => OrderLabel::getUrl().'?profile='.$record->id),
                    DeleteAction::make()
                        ->label('Archive')
                        ->requiresConfirmation()
                        ->modalHeading('Archive profile')
                        ->action(fn (Profile $record) => $record->update(['deleted' => true])),
                ]),
            ]);
    }
}
