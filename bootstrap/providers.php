<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\ClientPortalPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    ClientPortalPanelProvider::class,
];
