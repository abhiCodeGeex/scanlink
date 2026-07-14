<?php

namespace App\Filament\Resources\CodePurchases\Pages;

use App\Enums\CodeOrderStatus;
use App\Enums\AdminRole;
use App\Filament\Resources\CodePurchases\CodePurchaseResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;

class ViewCodePurchase extends ViewRecord
{
    protected static string $resource = CodePurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changeStatus')
                ->label('Change Order Status')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => auth()->user()?->admin_role instanceof AdminRole
                    && auth()->user()->admin_role->canWrite())
                ->schema([
                    Select::make('status')
                        ->label('Change Order Status to')
                        ->options(CodeOrderStatus::changeOptions())
                        ->default(fn (): int => $this->record->status->value)
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => (int) $data['status'],
                    ]);

                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
