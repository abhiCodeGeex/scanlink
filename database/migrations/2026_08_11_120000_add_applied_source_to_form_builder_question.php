<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Track which "existing form" a copied question came from, so the portal Form
 * Builder can show applied forms as removable chips (e.g. "profile:6772" /
 * "library:34"). NULL = a manually-built question. Additive + guarded.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('form_builder_question')) {
            return;
        }

        if (! Schema::hasColumn('form_builder_question', 'applied_source')) {
            Schema::table('form_builder_question', function (Blueprint $table): void {
                $table->string('applied_source', 64)->nullable()->after('form_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('form_builder_question') && Schema::hasColumn('form_builder_question', 'applied_source')) {
            Schema::table('form_builder_question', function (Blueprint $table): void {
                $table->dropColumn('applied_source');
            });
        }
    }
};
