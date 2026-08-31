<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The legacy VOCC card photo table.
 *
 * It exists in the live import but never had a migration, so a fresh install (and the test
 * database) had no such table at all — which is part of why nothing in the app read it. The
 * shape matches the imported table exactly; guarded so the live database is untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('voc_profile_image')) {
            return;
        }

        Schema::create('voc_profile_image', function (Blueprint $table): void {
            $table->increments('voc_profile_image_id');
            $table->unsignedInteger('client_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('profile_id')->index();
            $table->string('image_name')->default('');
            $table->dateTime('created_at')->nullable();
            // Legacy stages an upload with is_temp=1 until the form is saved.
            $table->unsignedTinyInteger('is_temp')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voc_profile_image');
    }
};
