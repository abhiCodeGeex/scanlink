<?php

namespace App\Filament\Resources\Profiles\Pages\Concerns;

use App\Services\ProfileMediaService;
use App\Services\ProfileQrService;

trait SyncsProfileAssets
{
    protected function syncProfileAssets(): void
    {
        $profile = $this->record->refresh();
        $state = $this->form->getState();

        try {
            app(ProfileMediaService::class)->syncUploads($profile, $state);
        } catch (\Throwable $exception) {
            report($exception);

            if (method_exists($this, 'notifyAssetSyncFailure')) {
                $this->notifyAssetSyncFailure($exception, 'media uploads');
            }
        }

        try {
            app(ProfileQrService::class)->generateFor($profile->refresh());
        } catch (\Throwable $exception) {
            report($exception);

            if (method_exists($this, 'notifyAssetSyncFailure')) {
                $this->notifyAssetSyncFailure($exception, 'QR code');
            }
        }
    }
}
