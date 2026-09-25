<?php

namespace App\Console\Commands;

use App\Mail\ScanlinkMail;
use App\Models\ClientUser;
use App\Support\SystemNotifier;
use App\Support\TransactionalMailer;
use Illuminate\Console\Command;

/**
 * Legacy welcome/sendusernotification: warn a user before their *account* expires.
 *
 * This is the account (client_users.expire_at), not the codes — a different clock from
 * scanlink:send-expiry-notifications, and the one that decides whether the person can sign in
 * at all. Legacy mailed twice: two months out, then one month out, using users.notice as the
 * stage counter so neither notice repeated (welcome.php:74-113).
 *
 * The rebuild carried the expire_at column, the notice column and the renewal email over, but
 * never the warning — accounts simply stopped working on the day with no word beforehand.
 */
class SendAccountExpiryNotices extends Command
{
    protected $signature = 'scanlink:send-account-expiry-notices {--dry-run : Report what would be sent without sending it.}';

    protected $description = 'Email account-expiry warnings two months and one month before a client user account expires';

    /** notice = 1: the two-month warning has gone out. notice = 2: the one-month one has. */
    protected const FIRST_WARNING = 1;

    protected const FINAL_WARNING = 2;

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $sent = 0;

        ClientUser::query()
            ->whereNotNull('expire_at')
            ->where('expire_at', '>', now())
            // Two months out at the far edge; anything beyond is nobody's business yet.
            ->where('expire_at', '<=', now()->addMonths(3))
            ->orderBy('id')
            ->chunkById(200, function ($members) use (&$sent, $dryRun): void {
                foreach ($members as $member) {
                    $stage = $this->stageFor($member);

                    // Already warned at this stage or later. The counter is what stops a daily
                    // schedule mailing the same person every morning for a month.
                    if ($stage === 0 || $stage <= (int) $member->notice) {
                        continue;
                    }

                    if (! filled($member->email)) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line('would warn '.$member->email.' (stage '.$stage.', expires '.$member->expire_at->format('d/m/Y').')');
                        $sent++;

                        continue;
                    }

                    $this->sendWarning($member, $stage);
                    $sent++;
                }
            });

        $this->info(($dryRun ? 'Would send ' : 'Sent ').$sent.' account expiry notice(s).');

        return self::SUCCESS;
    }

    /** Which warning is due: 0 = none yet, 1 = the two-month one, 2 = the one-month one. */
    protected function stageFor(ClientUser $member): int
    {
        // Whole months left, the way legacy's monthsBetween() counted them.
        $months = (int) now()->startOfDay()->diffInMonths($member->expire_at);

        if ($months >= 3) {
            return 0;
        }

        // A missed scheduler run must not cost someone their warning, so this is a window
        // rather than legacy's exact "month == 2" test; the counter still sends each once.
        return $months === 2 ? self::FIRST_WARNING : self::FINAL_WARNING;
    }

    protected function sendWarning(ClientUser $member, int $stage): void
    {
        $expiryDate = $member->expire_at->format('d/m/Y');

        app(TransactionalMailer::class)->sendOnce(
            (string) $member->email,
            new ScanlinkMail('ScanLink re-new account notification.', 'emails.account-expiry', [
                'firstName' => (string) ($member->first_name ?? ''),
                'lastName' => (string) ($member->last_name ?? ''),
                'expiryDate' => $expiryDate,
                'finalNotice' => $stage === self::FINAL_WARNING,
                'supportEmail' => (string) config('scanlink.support_email'),
                'supportPhone' => (string) config('scanlink.support_phone'),
            ]),
        );

        SystemNotifier::toMember(
            $member,
            'Account expiring',
            'Your ScanLink account expires on '.$expiryDate.'. Contact admin to renew it.',
            'heroicon-o-exclamation-triangle',
            $stage === self::FINAL_WARNING ? 'danger' : 'warning',
        );

        // Record the stage even when the mailer swallowed a failure: legacy set the flag before
        // mailing too, and a warning that silently retries daily is worse than one that is late.
        $member->forceFill(['notice' => $stage])->save();
    }
}
