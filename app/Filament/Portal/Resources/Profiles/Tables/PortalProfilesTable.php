<?php

namespace App\Filament\Portal\Resources\Profiles\Tables;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Pages\CumulativeAnalytics;
use App\Filament\Portal\Pages\FormSubmissions;
use App\Filament\Portal\Pages\OrderLabel;
use App\Filament\Portal\Pages\ScanAnalytics;
use App\Filament\Portal\Pages\VisitorLog;
use App\Filament\Portal\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Filament\Support\SearchTableFilter;
use App\Filament\Support\TableFilterDefaults;
use App\Models\Profile;
use App\Filament\Portal\Pages\RenewCodeSummary;
use App\Services\ProfileQrService;
use App\Support\LegacyEquipmentTypeLabels;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PortalProfilesTable
{
    public static function configure(Table $table): Table
    {
        return TableFilterDefaults::apply($table
            ->defaultSort('id', 'desc')
            ->searchable()
            ->searchPlaceholder('Search')
            // Apply filters immediately so state stays in sync when paginating.
            ->deferFilters(false)
            ->persistFiltersInSession()
            ->deferColumnManager(false)
            ->persistColumnsInSession()
            ->recordClasses(fn (Profile $record): string => $record->expiryStatusClass())
            // Legacy: free codes without renewal_required cannot be selected.
            ->checkIfRecordIsSelectableUsing(fn (Profile $record): bool => $record->isExpiryManaged())
            ->columns([
                TextColumn::make('id')
                    ->label('Profile No.')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->alignCenter(),
                TextColumn::make('code_profile_name')
                    ->label('Code Profile Name')
                    ->sortable()
                    ->searchable(['name', 'code_profile_name', 'identification', 'form_title', 'name2', 'address'])
                    ->getStateUsing(fn (Profile $record): string => filled(trim((string) $record->code_profile_name))
                        ? (string) $record->code_profile_name
                        : $record->displayLabel())
                    ->toggleable()
                    ->wrap(),
                // Only when viewing all templates (no type tab selected) on Master Code List.
                TextColumn::make('equipmentType.name')
                    ->label('Template Type')
                    ->getStateUsing(fn (Profile $record): string => $record->equipmentType
                        ? LegacyEquipmentTypeLabels::labelFor($record->equipmentType)
                        : '—')
                    ->visible(function (HasTable $livewire): bool {
                        if (! $livewire instanceof ListProfiles) {
                            return false;
                        }

                        $tab = $livewire->activeTab;

                        return blank($tab) || $tab === 'all';
                    })
                    ->toggleable()
                    ->wrap(),
                TextColumn::make('address')
                    ->label('Address')
                    ->toggleable()
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('expired_at')
                    ->label('Code expiry date')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter()
                    ->formatStateUsing(fn (Profile $record): string => $record->expiryDateLabel()),
            ])
            ->filters([
                SearchTableFilter::make(
                    ['name', 'code_profile_name', 'identification', 'address'],
                    label: 'Search',
                ),
            ])
            ->bulkActions([
                BulkAction::make('multipleCodeAnalytics')
                    ->label('Multiple Code Analytics')
                    ->icon('heroicon-o-chart-pie')
                    ->color('success')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        if ($records->isEmpty()) {
                            Notification::make()
                                ->title('No code profiles have been selected')
                                ->danger()
                                ->send();

                            return null;
                        }

                        // Legacy getCumulativeProfile: require more than one profile on the list.
                        if ($records->count() < 2) {
                            Notification::make()
                                ->title('Please select more than one profile')
                                ->danger()
                                ->send();

                            return null;
                        }

                        $expired = $records->contains(
                            fn (Profile $profile): bool => $profile->expiryStatusClass() === 'sl-row-expired'
                        );

                        if ($expired) {
                            Notification::make()
                                ->title("Please don't select expired profile")
                                ->danger()
                                ->send();

                            return null;
                        }

                        return redirect()->to(
                            CumulativeAnalytics::getUrl(['profiles' => $records->pluck('id')->implode(',')])
                        );
                    }),
                BulkAction::make('renewSelectedCodes')
                    ->label('Renew Selected Codes')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->deselectRecordsAfterCompletion()
                    // Legacy mastercode renew is available to primary and sub-users.
                    ->action(function (Collection $records) {
                        // Legacy: free_code=0 OR renewal_required=1.
                        $renewable = $records->filter(
                            fn (Profile $profile): bool => $profile->isExpiryManaged()
                        );

                        if ($renewable->isEmpty()) {
                            Notification::make()
                                ->title('Please select the code to be renew.')
                                ->danger()
                                ->send();

                            return null;
                        }

                        return redirect()->to(
                            RenewCodeSummary::stageRenew(
                                $renewable,
                                ProfileResource::getUrl('index', panel: 'portal'),
                            )
                        );
                    }),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->iconButton()
                    ->tooltip('Edit')
                    ->icon('heroicon-o-pencil-square')
                    // Legacy expried.php: Edit is blocked on expired codes (message-only).
                    ->url(fn (Profile $record): ?string => $record->isExpired()
                        ? null
                        : ProfileResource::getUrl('edit', ['record' => $record]))
                    ->action(fn (Profile $record) => self::blockIfExpired($record))
                    ->visible(fn (): bool => InteractsWithClientMembership::memberCanEditCode(
                        InteractsWithClientMembership::portalMembership(),
                    )),
                Action::make('scanAnalytics')
                    ->label('Scanalytics')
                    ->iconButton()
                    ->tooltip('Scanalytics')
                    ->icon('heroicon-o-chart-bar')
                    // Legacy: Scanalytics is blocked on expired codes.
                    ->url(fn (Profile $record): ?string => $record->isExpired()
                        ? null
                        : ScanAnalytics::getUrl().'?profile='.$record->id)
                    ->action(fn (Profile $record) => self::blockIfExpired($record))
                    ->visible(fn (): bool => InteractsWithClientMembership::memberCanAccessAnalytics(
                        InteractsWithClientMembership::portalMembership(),
                    )),
                Action::make('formSubmissions')
                    ->label('Form Submissions')
                    ->iconButton()
                    ->tooltip('Form Submissions')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->url(fn (Profile $record): string => FormSubmissions::getUrl(['profile' => $record->id]))
                    ->visible(fn (): bool => InteractsWithClientMembership::memberCanAccessFormSubmissions(
                        InteractsWithClientMembership::portalMembership(),
                    )),
                Action::make('downloadQr')
                    ->label('Download QR/DM Code')
                    ->iconButton()
                    ->tooltip('Download QR/DM Code')
                    ->icon('heroicon-o-arrow-down-tray')
                    // Legacy: Download is blocked on expired codes.
                    ->action(function (Profile $record): mixed {
                        if (self::blockIfExpired($record)) {
                            return null;
                        }

                        return app(ProfileQrService::class)->downloadQrImage($record);
                    })
                    ->visible(fn (): bool => InteractsWithClientMembership::memberCanDownload(
                        InteractsWithClientMembership::portalMembership(),
                    )),
                Action::make('orderLabels')
                    ->label('Order Labels')
                    ->iconButton()
                    ->tooltip('Order Labels')
                    ->icon('heroicon-o-tag')
                    // Legacy: Order Labels is blocked on expired codes.
                    ->url(fn (Profile $record): ?string => $record->isExpired()
                        ? null
                        : OrderLabel::getUrl(['profile' => $record->id]))
                    ->action(fn (Profile $record) => self::blockIfExpired($record))
                    ->visible(fn (): bool => InteractsWithClientMembership::memberCanOrderLabel(
                        InteractsWithClientMembership::portalMembership(),
                    )),
                Action::make('visitorLog')
                    ->label('View visitor log')
                    ->iconButton()
                    ->tooltip('View visitor log')
                    ->icon('heroicon-o-table-cells')
                    ->url(fn (Profile $record): string => VisitorLog::getUrl(['profile' => $record->id]))
                    ->visible(fn (): bool => InteractsWithClientMembership::memberCanAccessVisitorLog(
                        InteractsWithClientMembership::portalMembership(),
                    )),
                DeleteAction::make()
                    ->label('Delete')
                    ->iconButton()
                    ->tooltip('Delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Are you sure you want to delete this code?')
                    // Legacy: Delete is blocked on expired codes.
                    ->action(function (Profile $record): void {
                        if (self::blockIfExpired($record)) {
                            return;
                        }

                        $record->update(['deleted' => true]);
                    })
                    ->visible(fn (): bool => InteractsWithClientMembership::memberCanDeleteCode(
                        InteractsWithClientMembership::portalMembership(),
                    )),
            ])
            ->emptyStateHeading('No Profile Found..!')
            ->emptyStateDescription('Use “Add a New Code” to create a code profile.'));
    }

    /**
     * Legacy expried.php guard: expired codes cannot be edited, viewed in scanalytics,
     * downloaded, ordered as labels, or deleted. Returns true (and notifies) when blocked.
     */
    private static function blockIfExpired(Profile $record): bool
    {
        if (! $record->isExpired()) {
            return false;
        }

        Notification::make()
            ->title('You can not perform this action on an expired code profile.')
            ->danger()
            ->send();

        return true;
    }
}
