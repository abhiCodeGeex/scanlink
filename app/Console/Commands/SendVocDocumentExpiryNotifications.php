<?php

namespace App\Console\Commands;

use App\Mail\ScanlinkMail;
use App\Models\Profile;
use App\Support\PublicMediaPath;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Legacy minion task documentexpiry.php: for each VOC profile with a document whose
 * expiry_date is 30 days away (renewal reminder) or today (expiry notice), email every
 * configured notification recipient (voc_recipients) using the profile's
 * voc_email_url / voc_email_text / voc_email_sign_line1.
 */
class SendVocDocumentExpiryNotifications extends Command
{
    protected $signature = 'scanlink:send-voc-document-expiry';

    protected $description = 'Email VOC document renewal reminders (30 days before expiry) and expiry notices (on expiry day) to each profile\'s notification recipients';

    public function handle(): int
    {
        $sent = 0;

        // [days-before-expiry => is-expiry-notice] — legacy fires the 30-day reminder and the day-of notice.
        foreach ([30 => false, 0 => true] as $daysAhead => $expired) {
            $target = now()->addDays($daysAhead)->toDateString();

            $profiles = Profile::query()
                ->active()
                ->whereHas('equipmentType', fn (Builder $q): Builder => $q->where('slag', 'voc'))
                ->whereHas('vocDocuments', fn (Builder $q): Builder => $q->whereDate('expiry_date', $target))
                ->with([
                    'vocRecipients',
                    'logos',
                    'vocDocuments' => fn ($q) => $q->whereDate('expiry_date', $target),
                ])
                ->get();

            foreach ($profiles as $profile) {
                /** @var EloquentCollection<int, \App\Models\VocDocument> $docs */
                $docs = $profile->vocDocuments;

                if ($docs->isEmpty()) {
                    continue;
                }

                $recipients = $profile->vocRecipients
                    ->pluck('email')
                    ->map(fn ($email): string => trim((string) $email))
                    ->filter()
                    ->unique()
                    ->values();

                if ($recipients->isEmpty()) {
                    continue;
                }

                $subject = $expired ? 'VOCC Expiry Notification' : 'VOCC Renewal Reminder';
                $data = $this->buildMailData($profile, $docs, $expired, $target);

                foreach ($recipients as $email) {
                    try {
                        Mail::to($email)->send(new ScanlinkMail($subject, 'emails.voc.document-expiry', $data));
                        $sent++;
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }
        }

        $this->info("Sent {$sent} VOC document notification(s).");

        return self::SUCCESS;
    }

    /**
     * @param  EloquentCollection<int, \App\Models\VocDocument>  $docs
     * @return array<string, mixed>
     */
    protected function buildMailData(Profile $profile, EloquentCollection $docs, bool $expired, string $target): array
    {
        $docNames = $docs
            ->map(fn ($doc): string => e((string) ($doc->name ?: 'Document')).'<br>')
            ->implode('');

        $logoName = (string) ($profile->logos->first()?->logo_name ?? '');

        return [
            'expired' => $expired,
            'vocUsername' => trim(((string) $profile->voc_first_name).' '.((string) $profile->voc_last_name)),
            'docNames' => $docNames,
            'expiryDate' => Carbon::parse($target)->format('d/m/Y'),
            'emailUrl' => trim((string) $profile->voc_email_url) ?: 'http://www.myskills.gov.au/',
            'emailText' => trim((string) $profile->voc_email_text) ?: 'Click here to find a service provider near you',
            'signature' => (string) $profile->voc_email_sign_line1,
            'logoUrl' => $logoName !== '' ? PublicMediaPath::url($logoName) : null,
        ];
    }
}
