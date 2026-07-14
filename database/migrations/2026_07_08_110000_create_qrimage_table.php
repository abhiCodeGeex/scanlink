<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('qrimage')) {
            return;
        }

        Schema::create('qrimage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->string('qrimg_name');
            $table->timestamps();

            $table->unique('profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qrimage');
    }
};
