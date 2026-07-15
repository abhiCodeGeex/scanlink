<?php

namespace App\Filament\Resources\Profiles\Pages\Concerns;

use App\Services\CodeProfileRenewalService;
use App\Services\ProfileQrService;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

trait HasProfileQrActions
{
    protected function canDownloadQr(): bool
    {
        return true;
    }

    /**
     * @return array<int, Action>
     */
    protected function profileQrHeaderActions(): array
    {
        return [
            Action::make('downloadQr')
                ->label('Download QR')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (): bool => $this->canDownloadQr())
                ->action(fn (): mixed => app(ProfileQrService::class)->downloadQrImage($this->record)),
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn (): bool => $this->canDownloadQr())
                ->action(fn (): mixed => app(ProfileQrService::class)->downloadPdf($this->record)),
            Action::make('qrColor')
                ->label('QR colour')
                ->icon('heroicon-o-swatch')
                ->visible(fn (): bool => $this->record->typeSlug() === 'code'
                    || ($this->record->code_type !== null))
                ->fillForm(fn (): array => [
                    'color_code' => $this->record->color_code ?: '#000000',
                ])
                ->form([
                    ColorPicker::make('color_code')
                        ->label('Colour Selector')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    app(ProfileQrService::class)->applyColorAndRegenerate(
                        $this->record,
                        $data['color_code'],
                    );

                    $this->record->refresh();
                    $this->record->load('qrImage');

                    Notification::make()
                        ->title('QR colour updated')
                        ->success()
                        ->send();
                }),
            Action::make('renewCode')
                ->label('Renew code')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Renew this code?')
                ->modalDescription(fn (): string => 'Extends expiry by 1 year and creates a renew code order.')
                ->visible(fn (): bool => $this->record->typeSlug() === 'code')
                ->action(function (): void {
                    $order = app(CodeProfileRenewalService::class)->renew([$this->record]);

                    $this->record->refresh();

                    Notification::make()
                        ->title('Code renewed')
                        ->body(new HtmlString(
                            'Order #'.$order->id
                            .' — expires '.$this->record->expired_at?->format('d/m/Y')
                        ))
                        ->success()
                        ->send();
                }),
        ];
    }
}
