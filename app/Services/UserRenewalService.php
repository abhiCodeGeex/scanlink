<?php

namespace App\Services;

use App\Mail\AccountRenewalNotification;
use App\Models\ClientUser;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class UserRenewalService
{
    public function renew(ClientUser $user): ClientUser
    {
        $user->update([
            'expire_at' => $this->nextExpiryFor($user),
            'notice' => 0,
        ]);

        $this->sendRenewalEmail($user->fresh());

        return $user->fresh();
    }

    public function updateExpiry(ClientUser $user, string $expireAt): ClientUser
    {
        $user->update([
            'expire_at' => $expireAt,
            // Back to stage 0: the warnings start again from the new date.
            'notice' => 0,
        ]);

        $this->sendRenewalEmail($user->fresh());

        return $user->fresh();
    }

    /**
     * A year on from the current expiry, or from today when it has already passed.
     *
     * Legacy's rule (siteadmin user.php:154-159): renewing late does not hand back the time the
     * account spent expired.
     */
    public function nextExpiryFor(ClientUser $user): Carbon
    {
        $base = $user->expire_at && $user->expire_at->isFuture()
            ? $user->expire_at
            : now();

        return $base->copy()->addYear();
    }

    public function sendRenewalEmail(ClientUser $user): void
    {
        if (! filled($user->email)) {
            return;
        }

        $fromEmail = Setting::valueFor('contact_email') ?? config('mail.from.address');

        try {
            Mail::to($user->email)
                ->send((new AccountRenewalNotification($user))
                    ->from($fromEmail, 'ScanLink Solutions'));
        } catch (\Throwable $e) {
            // The renewal is already saved; an unreachable mail server must not undo it or
            // throw the admin an error for work that actually succeeded.
            report($e);
        }
    }
}
