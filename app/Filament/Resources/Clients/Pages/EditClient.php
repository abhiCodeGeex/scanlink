<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Enums\CodeOrderStatus;
use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditClient extends EditRecord
{
    use HandlesDatabaseSaveFailures;

    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Legacy "Add Reseller code" form on client edit.
            Action::make('addResellerCode')
                ->label('Add Reseller code')
                ->icon('heroicon-o-tag')
                ->schema([
                    TextInput::make('reseller_code')
                        ->label('Reseller code')
                        ->required()
                        ->default(fn (): ?string => $this->record->reseller_code),
                ])
                ->action(function (array $data): void {
                    $code = trim($data['reseller_code']);

                    $exists = Client::query()
                        ->where('reseller_code', $code)
                        ->whereKeyNot($this->record->getKey())
                        ->exists();

                    if ($exists) {
                        Notification::make()
                            ->title('You entered duplicate reseller code.')
                            ->danger()
                            ->send();

                        throw ValidationException::withMessages([
                            'mountedActionsData.0.reseller_code' => 'You entered duplicate reseller code.',
                        ]);
                    }

                    $this->record->update(['reseller_code' => $code]);

                    Notification::make()
                        ->title('Reseller code saved.')
                        ->success()
                        ->send();
                }),

            // Legacy "Add Codes" (free codes) form on client edit.
            Action::make('addFreeCodes')
                ->label('Add free codes')
                ->icon('heroicon-o-gift')
                ->schema([
                    TextInput::make('no_of_codes')
                        ->label('Number of codes')
                        ->numeric()
                        ->minValue(1)
                        ->integer()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $client = $this->record;

                        $client->codePurchases()->create([
                            'email' => $client->email,
                            'first_name' => $client->contact_person,
                            'company_name' => $client->client_name,
                            'billing_address' => $client->address,
                            'phone' => $client->telephone,
                            'no_of_codes' => (int) $data['no_of_codes'],
                            'per_code_amount' => 0,
                            'total_amount' => 0,
                            'status' => CodeOrderStatus::New,
                            'enable' => true,
                            'free_code' => true,
                            'ordered_on' => now(),
                        ]);
                    } catch (\Illuminate\Database\QueryException $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Could not add free codes')
                            ->body('Unable to save. Please check the values and try again.')
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Free codes added.')
                        ->success()
                        ->send();
                }),

            DeleteAction::make()
                ->label('Delete')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Delete this client?')
                ->modalDescription('This soft-deletes the client. Related codes and users are kept. You can restore later from the database if needed.')
                ->successNotificationTitle('Client deleted.'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Live dump: reseller_email is NOT NULL. ConvertEmptyStringsToNull would send null.
        $data['reseller_email'] = filled($data['reseller_email'] ?? null)
            ? $data['reseller_email']
            : '';

        return $data;
    }

    protected function afterSave(): void
    {
        $client = $this->record->refresh();
        $primaryUser = $client->primaryUser;

        if (! $primaryUser) {
            return;
        }

        $primaryUser->update([
            'email' => $client->email,
            'password' => $client->password ?? $primaryUser->password,
        ]);
    }
}
