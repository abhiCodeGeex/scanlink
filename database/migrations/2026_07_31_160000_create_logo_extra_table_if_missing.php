<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('logo_extra')) {
            Schema::create('logo_extra', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('profile_id')->nullable()->index();
                $table->string('logo_name')->default('');
                $table->boolean('is_temp')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('picture_extra')) {
            Schema::create('picture_extra', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('profile_id')->nullable()->index();
                $table->string('txt_footer')->default('');
                $table->string('picture_name')->default('');
                $table->boolean('is_temp')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Data-safe: this table may predate Laravel on imported databases.
    }
};
