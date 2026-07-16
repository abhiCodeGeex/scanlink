<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_purchase_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_purchase_id')->constrained('code_purchase')->cascadeOnDelete();
            $table->unsignedBigInteger('profile_id')->nullable();
            $table->timestamps();

            $table->index('profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_purchase_detail');
    }
};
