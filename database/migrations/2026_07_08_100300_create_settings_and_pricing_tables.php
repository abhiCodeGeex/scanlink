<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->text('values')->nullable();
            $table->timestamps();
        });

        Schema::create('code_prising', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('code_min_qty');
            $table->unsignedInteger('code_max_qty');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('reseller_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('reseller_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('code_qty');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_pricing');
        Schema::dropIfExists('code_prising');
        Schema::dropIfExists('settings');
    }
};
