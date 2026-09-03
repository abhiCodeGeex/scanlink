<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Models\Profile;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MultipleCodeRenewal extends Page implements HasTable
{
    use InteractsWithClientMembership;
    use InteractsWithTable;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?string $navigationLabel = 'Multiple Code Renewal';

    protected static ?string $title = 'Multiple Code Renewal';

    protected static ?string $slug = 'renew-codes';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.portal.pages.multiple-code-renewal';

    public static function getNavigationGroup(): ?string
    {
        return 'My Account';
    }

    public function table(Table $table): Table
    {
        return $table
            // Legacy: codes expired or expiring within 30 days, excluding free codes
            // that are not flagged for renewal.
            ->query(fn (): Builder => Profile::query()
                ->where('client_id', $this->requireClient()->id)
                ->active()
                ->expiryManaged()
                ->whereNotNull('expired_at')
                ->where('expired_at', '<=', now()->addDays(30))
                ->with('equipmentType'))
            ->columns([
                TextColumn::make('id')
                    ->label('Profile No.')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('expired_at')
                    ->label('Expires')
                    ->dateTime('d/m/Y')
                    ->badge()
                    // Legacy 3-tier: red = expired, gray = within 3 days, orange = within 30 days.
                    ->color(fn (Profile $record): string => match (true) {
                        $record->isExpired() => 'danger',
                        $record->expired_at?->lte(now()->addDays(3)) => 'gray',
                        default => 'warning',
                    }),
            ])
            ->bulkActions([
                BulkAction::make('renewSelected')
                    ->label('Renew selected codes')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        if ($records->isEmpty()) {
                            Notification::make()
                                ->title('Please select code to be renew.')
                                ->danger()
                                ->send();

                            return null;
                        }

                        return redirect()->to(
                            RenewCodeSummary::stageRenew(
                                $records,
                                MultipleCodeRenewal::getUrl(panel: 'portal'),
                            )
                        );
                    }),
            ]);
    }
}
