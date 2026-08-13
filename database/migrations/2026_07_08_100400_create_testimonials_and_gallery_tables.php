<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('testimonial')) {
            Schema::create('testimonial', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('video');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('gallery')) {
            Schema::create('gallery', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('approve')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery');
        Schema::dropIfExists('testimonial');
    }
};
