<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Short links we mint ourselves, so a printed QR depends on nothing outside this app.
 *
 * Kept apart from profiles.shorturl on purpose. That column already holds a mixture of
 * goo.gl and tinyurl.com values inherited from the old app, and mixing our own codes in
 * would make it impossible to tell afterwards which links still depend on a third party.
 * shorturl stays the display value; this table is the record of what we issued.
 *
 * No foreign key: the live RDS app user has no REFERENCES privilege, so declaring one
 * would fail the migration on deploy. The index carries the lookup either way.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('profile_short_links')) {
            return;
        }

        Schema::create('profile_short_links', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('profile_id')->unique();
            // The code is what a scanner actually sends us, so it is the hot lookup.
            $table->string('code', 16)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_short_links');
    }
};
