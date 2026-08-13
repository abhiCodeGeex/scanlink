<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('clients')) {
            return;
        }

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->text('address')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('contact_person')->nullable();
            $table->date('regi_date')->nullable();
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('url')->unique();
            $table->boolean('approve')->default(true);
            // Live MySQL keeps this non-unique: unassigned clients all store ''.
            $table->string('reseller_code')->nullable();
            $table->string('reseller_email')->nullable();
            $table->boolean('is_password_change')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
