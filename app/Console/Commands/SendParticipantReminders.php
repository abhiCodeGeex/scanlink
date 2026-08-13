<?php

namespace App\Console\Commands;

use App\Models\Participant;
use App\Models\ParticipantRecipient;
use App\Models\Profile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendParticipantReminders extends Command
{
    protected $signature = 'scanlink:send-participant-reminders';

    protected $description = 'Email participant recipients about overdue form participants';

    public function handle(): int
    {
        $today = now()->toDateString();
        $sent = 0;

        $profileIds = Participant::query()
            ->where('due_date', $today)
            ->where('is_participated', false)
            ->distinct()
            ->pluck('profile_id');

        foreach ($profileIds as $profileId) {
            $profile = Profile::query()->find($profileId);

            if (! $profile) {
                continue;
            }

            $names = Participant::query()
                ->where('profile_id', $profileId)
                ->where('due_date', $today)
                ->where('is_participated', false)
                ->pluck('name')
                ->all();

            $recipients = ParticipantRecipient::query()
                ->where('profile_id', $profileId)
                ->pluck('email')
                ->filter()
                ->unique();

            foreach ($recipients as $email) {
                Mail::raw(
                    "Participants not yet completed for {$profile->name}:\n\n".implode("\n", $names),
                    fn ($message) => $message
                        ->to($email)
                        ->subject("ScanLink participant reminder — {$profile->name}"),
                );
                $sent++;
            }
        }

        $this->info("Sent {$sent} participant reminder(s).");

        return self::SUCCESS;
    }
}
