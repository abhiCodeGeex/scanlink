<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('client_users')->nullOnDelete();
            $table->foreignId('profile_id')->nullable()->constrained('profiles')->nullOnDelete();
            $table->unsignedInteger('qty_small')->default(0);
            $table->unsignedInteger('qty_large')->default(0);
            $table->decimal('price_small', 10, 2)->default(0);
            $table->decimal('price_large', 10, 2)->default(0);
            $table->string('status', 50)->default('New');
            $table->string('transaction_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('country')->nullable();
            $table->string('email')->nullable();
            $table->string('contact', 50)->nullable();
            $table->dateTime('ordered_on')->nullable();
            $table->timestamps();

            $table->index(['status', 'ordered_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
