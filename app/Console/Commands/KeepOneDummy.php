<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Shrink local DB to a coherent one-row-per-data-table sample for testing.
 * Lookup tables (equipment_types, settings, migrations) are left intact.
 */
class KeepOneDummy extends Command
{
    protected $signature = 'scanlink:keep-one-dummy
                            {--force : Run without confirmation}';

    protected $description = 'Keep one related sample row set per business table (local test DB only)';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('This deletes most local rows. Continue?')) {
            return self::SUCCESS;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            $clientId = DB::table('clients')->orderBy('id')->value('id');

            if (! $clientId) {
                $this->error('No clients found. Seed or import first.');

                return self::FAILURE;
            }

            $profileId = DB::table('profiles')->where('client_id', $clientId)->orderBy('id')->value('id')
                ?? DB::table('profiles')->orderBy('id')->value('id');

            $codePurchaseId = DB::table('code_purchase')->where('client_id', $clientId)->orderBy('id')->value('id')
                ?? DB::table('code_purchase')->orderBy('id')->value('id');

            $orderId = DB::table('orders')->orderBy('id')->value('id');
            $fbOrderId = DB::table('form_builder_orders')->orderBy('id')->value('id');
            $userId = DB::table('users')->orderBy('id')->value('id');
            $clientUserId = DB::table('client_users')->where('client_id', $clientId)->orderBy('id')->value('id');

            $this->keepIds('clients', [$clientId]);
            $this->keepIds('client_users', array_filter([$clientUserId]));
            $this->keepIds('profiles', array_filter([$profileId]));
            $this->keepIds('users', array_filter([$userId]));

            if ($codePurchaseId) {
                $this->keepIds('code_purchase', [$codePurchaseId]);
                $this->keepFirstMatching('code_purchase_detail', 'code_purchase_id', $codePurchaseId);
            } else {
                $this->emptyTable('code_purchase');
                $this->emptyTable('code_purchase_detail');
            }

            if ($orderId) {
                $this->keepIds('orders', [$orderId]);
            } else {
                $this->emptyTable('orders');
            }

            if ($fbOrderId) {
                $this->keepIds('form_builder_orders', [$fbOrderId]);
                $this->keepFirstMatching('form_builder_order_detail', 'form_builder_order_id', $fbOrderId);
            } else {
                $this->emptyTable('form_builder_orders');
                $this->emptyTable('form_builder_order_detail');
            }

            foreach (['logo', 'picture', 'documents', 'video', 'weblink', 'profile_contact', 'checklist_item', 'qrimage'] as $table) {
                if ($profileId) {
                    $this->keepFirstMatching($table, 'profile_id', $profileId);
                } else {
                    $this->emptyTable($table);
                }
            }

            $this->keepFirstRow('testimonial');
            $this->keepFirstRow('gallery');
            $this->keepFirstRow('code_prising');
            $this->keepFirstRow('reseller_pricing');

            if (Schema::hasTable('admin')) {
                $this->keepFirstRow('admin');
            }

            $this->info('Kept all equipment_types and settings (lookup / config data).');
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->newLine();
        $this->call('scanlink:verify-import');
        $this->info('Local DB trimmed to one sample set.');

        return self::SUCCESS;
    }

    /**
     * @param  array<int, int|string>  $ids
     */
    protected function keepIds(string $table, array $ids): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $ids = array_values(array_filter($ids));

        if ($ids === []) {
            DB::table($table)->delete();
            $this->line("{$table}: 0");

            return;
        }

        DB::table($table)->whereNotIn('id', $ids)->delete();
        $this->line("{$table}: ".DB::table($table)->count());
    }

    protected function keepFirstMatching(string $table, string $column, mixed $value): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $id = DB::table($table)->where($column, $value)->orderBy('id')->value('id');

        if (! $id) {
            DB::table($table)->delete();
            $this->line("{$table}: 0");

            return;
        }

        DB::table($table)->where('id', '!=', $id)->delete();
        $this->line("{$table}: 1");
    }

    protected function keepFirstRow(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $id = DB::table($table)->orderBy('id')->value('id');

        if (! $id) {
            $this->line("{$table}: 0");

            return;
        }

        DB::table($table)->where('id', '!=', $id)->delete();
        $this->line("{$table}: 1");
    }

    protected function emptyTable(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        DB::table($table)->delete();
        $this->line("{$table}: 0");
    }
}
