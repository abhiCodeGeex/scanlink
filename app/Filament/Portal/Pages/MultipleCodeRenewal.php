<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Concerns\RestrictsToPrimaryClientUser;
use App\Models\Profile;
use App\Services\CodeProfileRenewalService;
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
use Illuminate\Support\HtmlString;

class MultipleCodeRenewal extends Page implements HasTable
{
    use InteractsWithClientMembership;
    use InteractsWithTable;
    use RestrictsToPrimaryClientUser;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?string $navigationLabel = 'Renew Codes';

    protected static ?string $title = 'Multiple Code Renewal';

    protected static ?string $slug = 'renew-codes';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.portal.pages.multiple-code-renewal';

    public static function getNavigationGroup(): ?string
    {
        return 'Codes';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Profile::query()
                ->where('client_id', $this->requireClient()->id)
                ->active()
                ->whereNotNull('expired_at')
                ->where('expired_at', '<=', now()->addDays(60))
                ->whereHas('equipmentType', fn (Builder $query) => $query->where('slag', 'code'))
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
                    ->color(fn (Profile $record): string => $record->isExpired() ? 'danger' : 'warning'),
            ])
            ->bulkActions([
                BulkAction::make('renewSelected')
                    ->label('Renew selected codes')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records): void {
                        if ($records->isEmpty()) {
                            Notification::make()
                                ->title('Please select code to be renew.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $order = app(CodeProfileRenewalService::class)->renew(
                            $records,
                            $this->requireClient()->id,
                        );

                        Notification::make()
                            ->title('Codes renewed')
                            ->body(new HtmlString(
                                $records->count().' code(s) renewed — order #'.$order->id
                            ))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
