<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\PhysicalOrderStatus;
use App\Enums\AdminRole;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->loadMissing('profile');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changeStatus')
                ->label('Change Order Status')
                ->visible(fn (): bool => auth()->user()?->admin_role instanceof AdminRole
                    && auth()->user()->admin_role->canWrite())
                ->schema([
                    Select::make('status')
                        ->label('Change Order Status to')
                        ->options(PhysicalOrderStatus::changeOptions())
                        ->default(fn (): string => $this->record->status->value)
                        ->required(),
                ])
                ->modalSubmitActionLabel('Update status')
                ->action(function (array $data): void {
                    $this->record->update(['status' => $data['status']]);
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
