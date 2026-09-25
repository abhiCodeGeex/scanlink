<?php

use App\Console\Commands\SendAccountExpiryNotices;
use App\Console\Commands\SendExpiryNotifications;
use App\Console\Commands\SendParticipantReminders;
use App\Console\Commands\SendVocDocumentExpiryNotifications;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendExpiryNotifications::class)->dailyAt('06:00');
Schedule::command(SendAccountExpiryNotices::class)->dailyAt('06:15');
Schedule::command(SendParticipantReminders::class)->dailyAt('07:00');
Schedule::command(SendVocDocumentExpiryNotifications::class)->dailyAt('06:30');
