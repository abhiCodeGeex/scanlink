<?php

namespace App\Filament\Resources\ResellerCodes\Pages;

use App\Filament\Resources\ResellerCodes\ResellerCodeResource;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ListResellerCodes extends ListRecords
{
    protected static string $resource = ResellerCodeResource::class;

    protected static ?string $title = 'Reseller Codes';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addResellerCode')
                ->label('Add New')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->visible(fn (): bool => auth()->user()?->admin_role?->canWrite() ?? false)
                ->modalHeading('Add reseller code')
                ->modalDescription('Select the client that will own this reseller code.')
                ->modalSubmitActionLabel('Add reseller code')
                ->modalCancelActionLabel('Cancel')
                ->schema([
                    Select::make('client_id')
                        ->label('Client')
                        ->placeholder('Select a client')
                        ->options(fn (): array => Client::query()
                            ->where(function (Builder $query): void {
                                $query
                                    ->whereNull('reseller_code')
                                    ->orWhere('reseller_code', '');
                            })
                            ->orderBy('client_name')
                            ->get(['id', 'client_name', 'email'])
                            ->mapWithKeys(fn (Client $client): array => [
                                $client->getKey() => $client->client_name
                                    .(filled($client->email) ? ' — '.$client->email : ''),
                            ])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required(),
                    TextInput::make('reseller_code')
                        ->label('Reseller code')
                        ->placeholder('Enter reseller code')
                        ->maxLength(255)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $clientId = (int) ($data['client_id'] ?? 0);
                    $code = trim((string) ($data['reseller_code'] ?? ''));

                    if ($code === '') {
                        throw ValidationException::withMessages([
                            'mountedActions.0.data.reseller_code' => 'The reseller code field is required.',
                        ]);
                    }

                    DB::transaction(function () use ($clientId, $code): void {
                        $client = Client::query()
                            ->lockForUpdate()
                            ->find($clientId);

                        if (! $client) {
                            throw ValidationException::withMessages([
                                'mountedActions.0.data.client_id' => 'The selected client is invalid.',
                            ]);
                        }

                        if (filled($client->reseller_code)) {
                            throw ValidationException::withMessages([
                                'mountedActions.0.data.client_id' => 'This client already has a reseller code.',
                            ]);
                        }

                        if (Client::query()->where('reseller_code', $code)->exists()) {
                            throw ValidationException::withMessages([
                                'mountedActions.0.data.reseller_code' => 'This reseller code is already in use.',
                            ]);
                        }

                        $client->update([
                            'reseller_code' => $code,
                            'reseller_code_active' => true,
                        ]);
                    });

                    Notification::make()
                        ->title('Reseller code added')
                        ->body($code.' was assigned successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
