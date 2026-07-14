<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_builder_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('email');
            $table->string('town')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->unsignedInteger('no_of_codes')->default(0);
            $table->decimal('per_code_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedTinyInteger('status')->default(0);
            $table->boolean('enable')->default(true);
            $table->dateTime('exipry_date')->nullable();
            $table->boolean('is_reseller_pricing_code')->default(false);
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });

        Schema::create('form_builder_order_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_builder_order_id')->constrained('form_builder_orders')->cascadeOnDelete();
            $table->foreignId('profile_id')->nullable()->constrained('profiles')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_builder_order_detail');
        Schema::dropIfExists('form_builder_orders');
    }
};
