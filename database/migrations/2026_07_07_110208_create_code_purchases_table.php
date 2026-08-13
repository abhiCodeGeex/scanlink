<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('code_purchase')) {
            return;
        }

        Schema::create('code_purchase', function (Blueprint $table) {
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
            $table->string('transaction_id')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->boolean('enable')->default(true);
            $table->dateTime('exipry_date')->nullable();
            $table->boolean('is_reseller_pricing_code')->default(false);
            $table->unsignedBigInteger('reseller_client_id')->nullable();
            $table->boolean('free_code')->default(false);
            $table->dateTime('ordered_on')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_purchase');
    }
};
