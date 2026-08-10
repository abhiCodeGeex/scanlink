<?php

namespace App\Filament\Resources\Profiles\Pages\Concerns;

trait HasProfileQrActions
{
    /**
     * QR header actions (Download QR / Download PDF / QR colour / Renew code) were
     * removed per request. Returns an empty set so the `...` spread at each call site
     * (portal + admin Create/Edit/View profile pages) stays a harmless no-op.
     *
     * @return array<int, mixed>
     */
    protected function profileQrHeaderActions(): array
    {
        return [];
    }
}
