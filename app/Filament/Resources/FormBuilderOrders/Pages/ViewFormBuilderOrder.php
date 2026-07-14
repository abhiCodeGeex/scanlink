<?php

namespace App\Filament\Resources\FormBuilderOrders\Pages;

use App\Enums\CodeOrderStatus;
use App\Filament\Resources\FormBuilderOrders\FormBuilderOrderResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;

class ViewFormBuilderOrder extends ViewRecord
{
    protected static string $resource = FormBuilderOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changeStatus')
                ->label('Change Order Status')
                ->schema([
                    Select::make('status')
                        ->label('Change Order Status to')
                        ->options(CodeOrderStatus::changeOptions())
                        ->default(fn (): int => $this->record->status->value)
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data): void {
                    $this->record->update(['status' => (int) $data['status']]);
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
