<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('logo')) {
            Schema::create('logo', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('client_users')->nullOnDelete();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('logo_name');
                $table->boolean('is_temp')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('picture')) {
            Schema::create('picture', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('client_users')->nullOnDelete();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('txt_footer')->nullable();
                $table->string('picture_name');
                $table->boolean('is_temp')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('client_users')->nullOnDelete();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('name')->nullable();
                $table->string('btn_color')->nullable();
                $table->string('doc_name');
                $table->string('txt_align')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_temp')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('video')) {
            Schema::create('video', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('client_users')->nullOnDelete();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->string('video_name');
                $table->boolean('is_extra')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('weblink')) {
            Schema::create('weblink', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('link_button')->nullable();
                $table->string('link_button_text')->nullable();
                $table->string('link_button_url')->nullable();
                $table->string('link_button_color')->nullable();
                $table->string('link_button_align')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('profile_contact')) {
            Schema::create('profile_contact', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('name_company')->nullable();
                $table->string('telephone')->nullable();
                $table->dateTime('datestamp')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('checklist_item')) {
            Schema::create('checklist_item', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->text('checklist_item');
                $table->dateTime('datetime')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('code_purchase_detail', function (Blueprint $table) {
            if (! Schema::hasColumn('code_purchase_detail', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('profile_id');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('code_purchase_detail')) {
            Schema::table('code_purchase_detail', function (Blueprint $table) {
                if (Schema::hasColumn('code_purchase_detail', 'amount')) {
                    $table->dropColumn('amount');
                }
            });
        }

        Schema::dropIfExists('checklist_item');
        Schema::dropIfExists('profile_contact');
        Schema::dropIfExists('weblink');
        Schema::dropIfExists('video');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('picture');
        Schema::dropIfExists('logo');
    }
};
