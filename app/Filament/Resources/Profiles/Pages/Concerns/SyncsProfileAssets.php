<?php

namespace App\Filament\Resources\Profiles\Pages\Concerns;

use App\Services\ProfileMediaService;
use App\Services\ProfileQrService;

trait SyncsProfileAssets
{
    protected function syncProfileAssets(): void
    {
        $profile = $this->record->refresh();

        app(ProfileMediaService::class)->syncUploads(
            $profile,
            $this->form->getState(),
        );

        app(ProfileQrService::class)->generateFor($profile);
    }
}
