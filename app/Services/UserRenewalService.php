<?php

namespace App\Services;

use App\Mail\AccountRenewalNotification;
use App\Models\ClientUser;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class UserRenewalService
{
    public function renew(ClientUser $user): ClientUser
    {
        $base = $user->expire_at && $user->expire_at->isFuture()
            ? $user->expire_at
            : now();

        $user->update([
            'expire_at' => $base->copy()->addYear(),
            'notice' => false,
        ]);

        $this->sendRenewalEmail($user->fresh());

        return $user->fresh();
    }

    public function updateExpiry(ClientUser $user, string $expireAt): ClientUser
    {
        $user->update([
            'expire_at' => $expireAt,
            'notice' => false,
        ]);

        $this->sendRenewalEmail($user->fresh());

        return $user->fresh();
    }

    public function sendRenewalEmail(ClientUser $user): void
    {
        $fromEmail = Setting::valueFor('contact_email') ?? config('mail.from.address');

        Mail::to($user->email)
            ->send((new AccountRenewalNotification($user))
                ->from($fromEmail, 'ScanLink Solutions'));
    }
}
