<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contacts')) {
            return;
        }

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_profile');
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->index('id_profile');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        Schema::dropIfExists('contacts');
    }
};
