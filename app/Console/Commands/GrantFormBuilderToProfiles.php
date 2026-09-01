<?php

namespace App\Console\Commands;

use App\Models\Profile;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Grant Form Builder entitlement (profiles.form_active) in bulk.
 *
 * form_active is the PAID entitlement — Profile::formBuilderEntitled() is
 * `form_active || type in (survey, voc)`, and there is a whole purchase flow behind it
 * (PurchaseFormBuilder / FormBuilderOrderSummary / form_builder_orders). Running this hands
 * that add-on to profiles that never bought it, so it is deliberately explicit, reversible,
 * and refuses to touch anything without --force.
 *
 * form_is_enable is NOT set unless --enable is passed. That flag is the per-profile "Enable"
 * toggle, and turning it on makes the form appear on the PUBLIC scan page for every profile
 * affected — a visitor-facing change, not just an entitlement one.
 */
class GrantFormBuilderToProfiles extends Command
{
    protected $signature = 'scanlink:grant-form-builder
                            {--dry-run : Report what would change and write nothing}
                            {--force : Actually apply the change (required for a real run)}
                            {--enable : Also set form_is_enable=1 — makes the form public on every affected profile}
                            {--type=* : Limit to these equipment type slugs (repeatable)}
                            {--rollback= : Path of a snapshot written by an earlier run; restores the prior values}';

    protected $description = 'Grant Form Builder entitlement (form_active) to existing profiles, reversibly';

    protected string $disk = 'local';

    public function handle(): int
    {
        if ($path = $this->option('rollback')) {
            return $this->rollback((string) $path);
        }

        $query = Profile::query()->active()->where('form_active', 0);

        $types = array_filter((array) $this->option('type'));
        if ($types !== []) {
            $query->whereHas('equipmentType', fn (Builder $q): Builder => $q->whereIn('slag', $types));
        }

        $targets = (clone $query)->get(['id', 'form_active', 'form_is_enable']);

        if ($targets->isEmpty()) {
            $this->info('Nothing to do — no profiles match, or they are already entitled.');

            return self::SUCCESS;
        }

        $this->line('');
        $this->line('Profiles without Form Builder entitlement that match: <options=bold>'.$targets->count().'</>');
        $this->line('Would set:  form_active = 1'.($this->option('enable') ? '  AND  form_is_enable = 1' : ''));

        if (! $this->option('enable')) {
            $this->line('<comment>form_is_enable is left alone — each client still turns the form on themselves.</comment>');
        } else {
            $this->line('<comment>--enable given: the form becomes visible on the PUBLIC scan page for all of these.</comment>');
        }

        if ($this->option('dry-run') || ! $this->option('force')) {
            $this->line('');
            $this->warn($this->option('dry-run')
                ? 'Dry run — nothing written.'
                : 'Refusing to write without --force. Re-run with --force once the count above looks right.');

            return self::SUCCESS;
        }

        // Snapshot first: this is a bulk write to live data and must be undoable.
        $snapshot = $this->writeSnapshot($targets);
        $this->line('Snapshot written: <options=bold>'.$snapshot.'</>');

        $columns = ['form_active' => 1];
        if ($this->option('enable')) {
            $columns['form_is_enable'] = 1;
        }

        $updated = 0;
        $targets->chunk(500)->each(function ($chunk) use ($columns, &$updated): void {
            $updated += Profile::query()->whereIn('id', $chunk->pluck('id'))->update($columns);
        });

        $this->info("Granted Form Builder to {$updated} profile(s).");
        $this->line('Undo with:  php artisan scanlink:grant-form-builder --rollback='.$snapshot);

        return self::SUCCESS;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Profile>  $targets
     */
    protected function writeSnapshot($targets): string
    {
        $path = 'form-builder-grants/'.now()->format('Ymd-His').'.json';

        Storage::disk($this->disk)->put($path, json_encode([
            'created_at' => now()->toIso8601String(),
            'columns' => $this->option('enable') ? ['form_active', 'form_is_enable'] : ['form_active'],
            'profiles' => $targets->map(fn (Profile $p): array => [
                'id' => (int) $p->id,
                'form_active' => (int) $p->getRawOriginal('form_active'),
                'form_is_enable' => (int) $p->getRawOriginal('form_is_enable'),
            ])->values()->all(),
        ], JSON_PRETTY_PRINT));

        return $path;
    }

    protected function rollback(string $path): int
    {
        if (! Storage::disk($this->disk)->exists($path)) {
            $this->error("No snapshot at {$path}");

            return self::FAILURE;
        }

        $data = json_decode((string) Storage::disk($this->disk)->get($path), true);
        $rows = $data['profiles'] ?? [];
        $columns = $data['columns'] ?? ['form_active'];

        if ($rows === []) {
            $this->warn('Snapshot is empty — nothing to restore.');

            return self::SUCCESS;
        }

        if (! $this->option('force')) {
            $this->warn('Would restore '.count($rows).' profile(s) from '.$path.'. Re-run with --force.');

            return self::SUCCESS;
        }

        $restored = 0;
        DB::transaction(function () use ($rows, $columns, &$restored): void {
            foreach ($rows as $row) {
                $set = [];
                foreach ($columns as $c) {
                    $set[$c] = (int) ($row[$c] ?? 0);
                }
                $restored += Profile::query()->whereKey((int) $row['id'])->update($set);
            }
        });

        $this->info("Restored {$restored} profile(s) from {$path}.");

        return self::SUCCESS;
    }
}
