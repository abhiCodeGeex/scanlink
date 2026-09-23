<?php

namespace App\Console\Commands;

use App\Models\Picture;
use App\Models\VocProfileImage;
use Illuminate\Console\Command;

/**
 * Moves VOCC card photos from the legacy `voc_profile_image` table into `picture`.
 *
 * The rebuilt editor's Profile Picture list is the `picture` table, so a photo that came across
 * in the import was shown on the scanned card but was missing from the editor: the card holder
 * could add a new photo but could not see or remove the one already on their card. Both tables
 * hold the same thing — a file under storage (images/picture/...) — so the row simply belongs in
 * the one the whole application reads.
 */
class MigrateVocProfileImages extends Command
{
    protected $signature = 'scanlink:migrate-voc-profile-images
                            {--dry-run : List what would move without writing}';

    protected $description = 'Move legacy VOCC profile photos into the picture table so the editor can show and remove them';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $moved = 0;
        $skipped = 0;

        // Drafts (is_temp = 1) belong to an abandoned legacy edit; they were never on a card.
        VocProfileImage::query()
            ->where('is_temp', '0')
            ->orderBy('voc_profile_image_id')
            ->each(function (VocProfileImage $legacy) use ($dryRun, &$moved, &$skipped): void {
                $name = trim((string) $legacy->image_name);

                if ($name === '') {
                    $skipped++;

                    return;
                }

                $already = Picture::query()
                    ->where('profile_id', $legacy->profile_id)
                    ->where('picture_name', $name)
                    ->exists();

                if ($already) {
                    $this->line("profile {$legacy->profile_id}: already in picture — {$name}");

                    if (! $dryRun) {
                        $legacy->delete();
                    }

                    $skipped++;

                    return;
                }

                $this->line("profile {$legacy->profile_id}: {$name}");

                if (! $dryRun) {
                    Picture::query()->create([
                        'client_id' => $legacy->client_id,
                        'user_id' => $legacy->user_id,
                        'profile_id' => $legacy->profile_id,
                        'picture_name' => $name,
                        'is_temp' => false,
                    ]);

                    $legacy->delete();
                }

                $moved++;
            });

        $this->newLine();
        $this->info(($dryRun ? '[dry-run] ' : '').$moved.' photo(s) moved, '.$skipped.' skipped.');

        return self::SUCCESS;
    }
}
