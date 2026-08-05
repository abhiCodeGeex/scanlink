<?php

namespace App\Filament\Resources\Profiles\Pages\Concerns;

use App\Services\AnalyticsApiService;
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

        // Legacy parity: register the profile URL with Galatech and store analytic_key
        // (scan-analytics key) on first save. Never blocks the save if the API is down.
        try {
            $profile = $profile->refresh();
            app(AnalyticsApiService::class)->ensureAnalyticKey(
                $profile,
                app(ProfileQrService::class)->profileUrl($profile),
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
