<?php

namespace App\Console\Commands;

use App\Mail\AccountRenewalNotification;
use App\Models\ClientUser;
use App\Models\Profile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendExpiryNotifications extends Command
{
    protected $signature = 'scanlink:send-expiry-notifications';

    protected $description = 'Send code expiry reminder emails (30 and 3 days before expiry)';

    public function handle(): int
    {
        $sent = 0;

        foreach ([30, 3] as $daysAhead) {
            $profiles = Profile::query()
                ->active()
                ->where('free_code', false)
                ->whereNotNull('expired_at')
                ->whereDate('expired_at', now()->addDays($daysAhead)->toDateString())
                ->with(['owner', 'client.primaryUser'])
                ->get();

            foreach ($profiles as $profile) {
                $member = $profile->owner ?? $profile->client?->primaryUser;

                if (! $member instanceof ClientUser) {
                    continue;
                }

                if ($member->is_sub_user && ! $member->notice) {
                    continue;
                }

                $email = $member->email ?: $profile->client?->email;

                if (! filled($email)) {
                    continue;
                }

                Mail::to($email)->send(new AccountRenewalNotification($member));
                $sent++;
            }
        }

        $this->info("Sent {$sent} expiry notification(s).");

        return self::SUCCESS;
    }
}
