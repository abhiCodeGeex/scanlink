<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legacy ScanLink stores portal accounts in `users`.
     * We use `client_users` locally to avoid clashing with Laravel admin `users`.
     */
    public function up(): void
    {
        if (Schema::hasTable('client_users')) {
            return;
        }

        Schema::create('client_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedTinyInteger('role')->default(5);
            $table->boolean('status')->default(true);
            $table->boolean('video_upload')->default(true);
            $table->boolean('checklist_option')->default(false);
            $table->boolean('customqr_option')->default(false);
            $table->boolean('is_password_change')->default(true);
            $table->dateTime('expire_at')->nullable();
            $table->string('client_reseller_code')->nullable();
            $table->boolean('is_sub_user')->default(false);
            $table->boolean('access_addcode')->default(false);
            $table->boolean('access_edit')->default(false);
            $table->boolean('access_delete')->default(false);
            $table->boolean('show_code_profile_id_to_acc_user')->default(false);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('town')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->timestamps();

            $table->index(['client_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_users');
    }
};
