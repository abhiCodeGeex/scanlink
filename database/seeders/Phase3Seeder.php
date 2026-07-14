<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class Phase3Seeder extends Seeder
{
    public function run(): void
    {
        $this->seedEquipmentTypes();

        if (Profile::query()->exists()) {
            return;
        }

        $acme = Client::query()->where('url', 'acme-inspections')->first();

        if (! $acme) {
            return;
        }

        $primaryUser = $acme->primaryUser;

        $assetType = EquipmentType::query()->where('slag', 'asset')->firstOrFail();
        $productType = EquipmentType::query()->where('slag', 'product')->firstOrFail();

        Profile::query()->create([
            'client_id' => $acme->id,
            'user_id' => $primaryUser?->id,
            'type_id' => $assetType->id,
            'name' => 'Warehouse Forklift A12',
            'code_profile_name' => 'acme-forklift-a12',
            'identification' => 'FL-A12',
            'address' => 'Dock 3, Sydney Warehouse',
            'description' => 'Monthly inspection asset profile',
            'code_type' => '0',
            'expired_at' => now()->addMonths(8),
            'activation_start_date' => now()->subMonths(4)->toDateString(),
        ]);

        Profile::query()->create([
            'client_id' => $acme->id,
            'user_id' => $primaryUser?->id,
            'type_id' => $productType->id,
            'name' => 'Safety Data Sheet - Solvent X',
            'code_profile_name' => 'acme-sds-solvent-x',
            'description' => 'Product information profile',
            'code_type' => '0',
            'form_active' => true,
            'expired_at' => now()->addYear(),
        ]);

        Profile::query()->create([
            'client_id' => $acme->id,
            'user_id' => $primaryUser?->id,
            'type_id' => $assetType->id,
            'name' => 'Archived Ladder Set',
            'code_profile_name' => 'acme-ladder-archived',
            'code_type' => '0',
            'deleted' => true,
            'expired_at' => now()->subMonth(),
        ]);
    }

    private function seedEquipmentTypes(): void
    {
        $types = [
            ['name' => 'Asset', 'slag' => 'asset'],
            ['name' => 'Product', 'slag' => 'product'],
            ['name' => 'People', 'slag' => 'people'],
            ['name' => 'Plant', 'slag' => 'plant'],
            ['name' => 'Location', 'slag' => 'location'],
            ['name' => 'Procedure', 'slag' => 'procedure'],
            ['name' => 'Misc', 'slag' => 'misc'],
            ['name' => 'Custom QR', 'slag' => 'customqr'],
            ['name' => 'Exhibit', 'slag' => 'exhibit'],
            ['name' => 'Code', 'slag' => 'code'],
        ];

        foreach ($types as $type) {
            EquipmentType::query()->updateOrCreate(
                ['slag' => $type['slag']],
                ['name' => $type['name']],
            );
        }
    }
}
