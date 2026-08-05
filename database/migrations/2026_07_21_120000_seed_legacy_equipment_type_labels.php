<?php

use App\Support\LegacyEquipmentTypeLabels;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        LegacyEquipmentTypeLabels::ensureLegacyTypesExist();
    }

    public function down(): void
    {
        // Keep seeded types; names were corrected to match production legacy.
    }
};
