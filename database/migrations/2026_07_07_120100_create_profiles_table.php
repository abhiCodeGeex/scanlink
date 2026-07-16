<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('client_users')->nullOnDelete();
            $table->foreignId('type_id')->constrained('equipment_types');
            $table->string('name');
            $table->string('code_profile_name')->nullable();
            $table->string('identification')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('shorturl')->nullable();
            $table->string('url')->nullable();
            $table->boolean('protect')->default(false);
            $table->string('password')->nullable();
            $table->string('code_type')->nullable();
            $table->boolean('deleted')->default(false);
            $table->boolean('update_or_not')->default(true);
            $table->unsignedBigInteger('code_purchase_id')->nullable();
            $table->unsignedBigInteger('form_id')->nullable();
            $table->boolean('form_active')->default(false);
            $table->boolean('form_is_enable')->default(false);
            $table->boolean('free_code')->default(false);
            $table->boolean('is_reseller_code')->default(false);
            $table->dateTime('expired_at')->nullable();
            $table->date('activation_start_date')->nullable();
            $table->date('activation_end_date')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'type_id', 'deleted']);
            $table->index('code_profile_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
