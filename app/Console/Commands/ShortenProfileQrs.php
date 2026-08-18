<?php

namespace App\Console\Commands;

use App\Models\Profile;
use App\Services\ProfileQrService;
use Illuminate\Console\Command;

/**
 * Backfill TinyURL short links + regenerate QR images for profiles whose code was
 * generated before URL shortening was configured (blank `shorturl` → long-URL QR).
 *
 * The editor now shortens on view/save (HasLegacyProfileEditorLayout), so this is only
 * for fixing the existing backlog in bulk. Safe to re-run: profiles already carrying a
 * short link are skipped unless --force.
 */
class ShortenProfileQrs extends Command
{
    protected $signature = 'scanlink:shorten-qr
                            {--limit=0 : Max profiles to process (0 = no limit)}
                            {--force : Re-shorten even profiles that already have a shorturl}
                            {--dry-run : List what would change without calling TinyURL or writing}';

    protected $description = 'Backfill TinyURL short links and regenerate QR images for existing profiles';

    public function handle(ProfileQrService $qr): int
    {
        if (blank(config('scanlink.short_url_api_token'))) {
            $this->error('No SCANLINK_SHORT_URL_API_TOKEN configured — shortening would just fall back to the long URL.');
            $this->line('Set it in .env, run `php artisan config:clear`, then re-run.');

            return self::FAILURE;
        }

        // Only profiles with a real scan destination (linked client that has a URL) can be
        // shortened; the rest legitimately keep the raw/long URL.
        $query = Profile::query()
            ->whereHas('client', fn ($q) => $q->whereNotNull('url')->where('url', '!=', ''));

        if (! $this->option('force')) {
            $query->where(fn ($q) => $q->whereNull('shorturl')->orWhere('shorturl', ''));
        }

        // NB: chunkById() ignores ->limit(), so cap by counting processed rows and stopping
        // the chunk walk (return false) once we reach --limit.
        $limit = (int) $this->option('limit');
        $matching = (clone $query)->count();
        $total = $limit > 0 ? min($limit, $matching) : $matching;

        if ($total === 0) {
            $this->info('Nothing to do — no matching profiles.');

            return self::SUCCESS;
        }

        $this->info(($this->option('dry-run') ? '[dry-run] ' : '')."Processing {$total} profile(s) ...");
        $bar = $this->output->createProgressBar($total);

        $shortened = 0;
        $failed = 0;
        $processed = 0;

        $query->orderBy('id')->chunkById(100, function ($profiles) use ($qr, &$shortened, &$failed, &$processed, $limit, $bar) {
            foreach ($profiles as $profile) {
                if ($limit > 0 && $processed >= $limit) {
                    return false; // reached --limit; stop walking chunks
                }

                $processed++;

                if ($this->option('dry-run')) {
                    $bar->advance();

                    continue;
                }

                try {
                    if ($this->option('force')) {
                        // Clear the cached link so resolveQrData() shortens afresh.
                        $profile->update(['shorturl' => null]);
                    }

                    // generateFor() => resolveQrData() shortens + caches the link AND rewrites
                    // the PNG to encode it.
                    $qr->generateFor($profile->fresh());

                    filled($profile->fresh()->shorturl) ? $shortened++ : $failed++;
                } catch (\Throwable $e) {
                    report($e);
                    $failed++;
                }

                $bar->advance();
            }

            return $limit === 0 || $processed < $limit;
        });

        $bar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->info("[dry-run] {$total} profile(s) would be (re)shortened.");

            return self::SUCCESS;
        }

        $this->info("Done — {$shortened} shortened, {$failed} failed (API/network).");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
