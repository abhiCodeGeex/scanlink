<?php

namespace App\Console\Commands;

use App\Mail\ScanlinkMail;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Support\SystemNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

/**
 * Legacy minion task profileexpiry.php: per user, per bucket (30 / 3 / 0 days before
 * expiry) send code-specific reminders. When a user has >2 codes in a bucket, one
 * consolidated email is sent instead of one per code.
 */
class SendExpiryNotifications extends Command
{
    protected $signature = 'scanlink:send-expiry-notifications';

    protected $description = 'Send code expiry reminder emails (30 & 3 days before expiry, and on expiry day)';

    public function handle(): int
    {
        $sent = 0;
        $loginUrl = rtrim((string) config('scanlink.portal_url'), '/').'/portal';
        $supportEmail = (string) config('scanlink.support_email');
        $supportPhone = (string) config('scanlink.support_phone');

        // Legacy fires 30-day, 3-day, and day-of-expiry (0) notices.
        foreach ([30, 3, 0] as $daysAhead) {
            $expired = $daysAhead === 0;
            $expiryDate = now()->addDays($daysAhead);

            $profiles = Profile::query()
                ->active()
                // Legacy includes free codes flagged renewal_required; excludes other free codes.
                ->expiryManaged()
                ->whereNotNull('expired_at')
                ->whereDate('expired_at', $expiryDate->toDateString())
                ->with(['owner', 'client.primaryUser', 'client'])
                ->get();

            // Group by recipient user (legacy loops per user_id).
            $byRecipient = $profiles->groupBy(
                fn (Profile $p): int => (int) ($this->recipientUser($p)?->getKey() ?? 0)
            );

            foreach ($byRecipient as $userId => $userProfiles) {
                if ((int) $userId <= 0) {
                    continue;
                }

                $member = $this->recipientUser($userProfiles->first());

                if (! $member instanceof ClientUser) {
                    continue;
                }

                // Sub-users only notified when they have the notice flag (legacy parity).
                if ($member->is_sub_user && ! $member->notice) {
                    continue;
                }

                $email = $member->email ?: $userProfiles->first()->client?->email;

                if (! filled($email)) {
                    continue;
                }

                $sent += $this->sendForUser(
                    $email,
                    $member,
                    $userProfiles,
                    $daysAhead,
                    $expired,
                    $expiryDate->format('d/m/Y'),
                    $loginUrl,
                    $supportEmail,
                    $supportPhone,
                );
            }
        }

        $this->info("Sent {$sent} expiry notification(s).");

        return self::SUCCESS;
    }

    /**
     * @param  Collection<int, Profile>  $userProfiles
     */
    protected function sendForUser(
        string $email,
        ClientUser $member,
        Collection $userProfiles,
        int $days,
        bool $expired,
        string $expiryDate,
        string $loginUrl,
        string $supportEmail,
        string $supportPhone,
    ): int {
        $first = (string) ($member->first_name ?? '');
        $last = (string) ($member->last_name ?? '');

        // Bell notification for the code owner (same user the expiry email targets).
        $bucketCount = $userProfiles->count();
        SystemNotifier::toMember(
            $member,
            $expired ? 'Code(s) expired' : 'Code(s) expiring soon',
            $expired
                ? $bucketCount.' code(s) have expired ('.$expiryDate.'). Renew to keep them active.'
                : $bucketCount.' code(s) expire in '.$days.' day(s) — '.$expiryDate.'.',
            'heroicon-o-exclamation-triangle',
            $expired ? 'danger' : 'warning',
        );

        // >2 codes in this bucket → one consolidated email (legacy "_multiple").
        if ($userProfiles->count() > 2) {
            $subject = $expired
                ? 'ScanLink Code Expired Notification '
                : 'ScanLink Code Expiry Notification ';

            $this->trySend($email, new ScanlinkMail($subject, 'emails.expiry.multiple', [
                'firstName' => $first,
                'lastName' => $last,
                'profileIds' => $userProfiles->map(fn (Profile $p): int => (int) $p->id)->all(),
                'expiryDate' => $expiryDate,
                'days' => $days,
                'expired' => $expired,
                'loginUrl' => $loginUrl,
                'supportEmail' => $supportEmail,
                'supportPhone' => $supportPhone,
            ]));

            return 1;
        }

        // ≤2 codes → one email per code.
        $count = 0;

        foreach ($userProfiles as $profile) {
            $subject = $expired
                ? 'ScanLink Code Expired Notification - Code Profile Number: '.$profile->id
                : 'ScanLink Code Expiry Notification - Code Profile Number: '.$profile->id;

            $this->trySend($email, new ScanlinkMail($subject, 'emails.expiry.single', [
                'firstName' => $first,
                'lastName' => $last,
                'profileId' => (int) $profile->id,
                'name' => (string) ($profile->code_profile_name ?: $profile->name ?: ''),
                'expiryDate' => $expiryDate,
                'days' => $days,
                'expired' => $expired,
                'loginUrl' => $loginUrl,
                'supportEmail' => $supportEmail,
                'supportPhone' => $supportPhone,
            ]));

            $count++;
        }

        return $count;
    }

    protected function recipientUser(?Profile $profile): ?ClientUser
    {
        if (! $profile) {
            return null;
        }

        $member = $profile->owner ?? $profile->client?->primaryUser;

        return $member instanceof ClientUser ? $member : null;
    }

    protected function trySend(string $email, ScanlinkMail $mail): void
    {
        try {
            Mail::to($email)->send($mail);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
