<?php

namespace App\Console\Commands;

use App\Mail\ScanlinkMail;
use App\Models\Profile;
use App\Support\PublicMediaPath;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Legacy minion task documentexpiry.php: for each VOC profile with a document whose
 * expiry_date is 30 days away (renewal reminder) or today (expiry notice), email every
 * configured notification recipient (voc_recipients) using the profile's
 * voc_email_url / voc_email_text / voc_email_sign_line1.
 */
class SendVocDocumentExpiryNotifications extends Command
{
    protected $signature = 'scanlink:send-voc-document-expiry
                           {--since= : Ignore anything that expired before this date (Y-m-d). Use on the first run so the switch-on does not mail years of historic expiries.}
                           {--dry-run : Report what would be sent without sending it.}';

    protected $description = 'Email VOC document renewal reminders (30 days before expiry) and expiry notices (on expiry) to each profile\'s notification recipients';

    public function handle(): int
    {
        $sent = 0;
        $dryRun = (bool) $this->option('dry-run');
        $since = $this->option('since') ? Carbon::parse((string) $this->option('since'))->toDateString() : null;
        $today = now()->toDateString();

        // Windows, not exact dates. Matching expiry_date == today+30 exactly meant one missed
        // scheduler run silently dropped that day's cohort for good; a window is caught up on
        // the next run, and voc_document_notifications stops it re-sending after that.
        //
        // [kind => [from, to]]
        $windows = [
            // Due within the next 30 days and not yet expired.
            'reminder' => [$today, now()->addDays(30)->toDateString()],
            // Expired: on the day, or any earlier day that was never notified.
            'expired' => [$since ?? '1970-01-01', $today],
        ];

        foreach ($windows as $kind => [$from, $to]) {
            $expired = $kind === 'expired';

            // Untyped: whereHas() hands this an Eloquent Builder, with() hands it a HasMany.
            $matches = fn ($q) => $q
                ->whereDate('expiry_date', '>=', $from)
                ->whereDate('expiry_date', '<=', $to)
                ->when($since !== null, fn ($qq) => $qq->whereDate('expiry_date', '>=', $since))
                ->whereNotExists(function ($sub) use ($kind): void {
                    $sub->selectRaw('1')
                        ->from('voc_document_notifications as n')
                        ->whereColumn('n.voc_document_id', 'voc_documents.voc_document_id')
                        // Normalise both sides: the model casts expiry_date to a date, so it
                        // is stored as "Y-m-d 00:00:00" while the marker holds "Y-m-d". A
                        // plain column comparison never matches and every run re-sends.
                        ->whereRaw('date(n.expiry_date) = date(voc_documents.expiry_date)')
                        ->where('n.kind', $kind);
                });

            $profiles = Profile::query()
                ->active()
                ->whereHas('equipmentType', fn (Builder $q): Builder => $q->where('slag', 'voc'))
                ->whereHas('vocDocuments', $matches)
                ->with([
                    'vocRecipients',
                    'logos',
                    'vocDocuments' => $matches,
                ])
                ->get();

            foreach ($profiles as $profile) {
                /** @var EloquentCollection<int, \App\Models\VocDocument> $docs */
                $docs = $profile->vocDocuments;

                if ($docs->isEmpty()) {
                    continue;
                }

                // The date shown in the mail is the earliest one in this batch.
                $target = (string) $docs
                    ->map(fn ($doc): string => Carbon::parse($doc->expiry_date)->toDateString())
                    ->sort()
                    ->first();

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

                if ($dryRun) {
                    $this->line(sprintf(
                        '[dry-run] %s: profile #%d, %d document(s), %d recipient(s)',
                        $kind, $profile->id, $docs->count(), $recipients->count(),
                    ));

                    continue;
                }

                $delivered = false;
                foreach ($recipients as $email) {
                    try {
                        Mail::to($email)->send(new ScanlinkMail($subject, 'emails.voc.document-expiry', $data));
                        $delivered = true;
                        $sent++;
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }

                // Only mark once something actually went out, so a mail outage retries tomorrow
                // instead of silently swallowing the notice.
                if ($delivered) {
                    $this->markNotified($docs, $kind);
                }
            }
        }

        $this->info($dryRun
            ? 'Dry run complete — nothing sent.'
            : "Sent {$sent} VOC document notification(s).");

        return self::SUCCESS;
    }

    /**
     * @param  EloquentCollection<int, \App\Models\VocDocument>  $docs
     */
    protected function markNotified(EloquentCollection $docs, string $kind): void
    {
        $now = now();

        foreach ($docs as $doc) {
            DB::table('voc_document_notifications')->updateOrInsert(
                [
                    'voc_document_id' => (int) $doc->voc_document_id,
                    'kind' => $kind,
                    'expiry_date' => Carbon::parse($doc->expiry_date)->toDateString(),
                ],
                ['sent_at' => $now],
            );
        }
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
