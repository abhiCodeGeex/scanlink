<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_info')) {
            Schema::create('user_info', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('user_name')->nullable();
                $table->string('user_email')->nullable();
                $table->string('user_mobile')->nullable();
                $table->dateTime('entry_date')->nullable();
                $table->index('profile_id');
            });
        }

        if (! Schema::hasTable('form_builder_question_types')) {
            Schema::create('form_builder_question_types', function (Blueprint $table) {
                $table->unsignedBigInteger('question_type_id')->primary();
                $table->string('type', 50);
                $table->string('label')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }

        if (! Schema::hasTable('form_builder_question')) {
            Schema::create('form_builder_question', function (Blueprint $table) {
                $table->unsignedBigInteger('question_id')->primary();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->unsignedBigInteger('form_id')->default(0);
                $table->unsignedBigInteger('question_type_id')->nullable();
                $table->text('question_text')->nullable();
                $table->string('image_title')->nullable();
                $table->string('image_align', 20)->nullable();
                $table->unsignedInteger('question_order')->default(0);
                $table->string('button_link_url')->nullable();
                $table->string('button_colour', 50)->nullable();
                $table->string('doc_title')->nullable();
                $table->boolean('include_name')->default(false);
                $table->boolean('include_employer')->default(false);
                $table->boolean('include_email')->default(false);
                $table->boolean('include_phone')->default(false);
                $table->boolean('participant_include_signature')->default(false);
                $table->boolean('participant_include_employer')->default(false);
                $table->boolean('is_mandatory')->default(false);
                $table->boolean('is_logchecked')->default(false);
                $table->string('log_columntitle')->nullable();
                $table->index(['profile_id', 'form_id']);
            });
        }

        if (! Schema::hasTable('form_builder_question_options')) {
            Schema::create('form_builder_question_options', function (Blueprint $table) {
                $table->unsignedBigInteger('option_id')->primary();
                $table->unsignedBigInteger('question_id');
                $table->string('option_name')->nullable();
                $table->unsignedBigInteger('question_option_type_id')->nullable();
                $table->index('question_id');
            });
        }

        if (! Schema::hasTable('form_builder_answers')) {
            Schema::create('form_builder_answers', function (Blueprint $table) {
                $table->unsignedBigInteger('answer_id')->primary();
                $table->unsignedBigInteger('question_id');
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->text('question_answer')->nullable();
                $table->unsignedInteger('row_number')->default(0);
                $table->string('signature_name_text')->nullable();
                $table->string('signature_employer_text')->nullable();
                $table->string('signature_email_text')->nullable();
                $table->string('signature_phone_text')->nullable();
                $table->string('participant_signature_image')->nullable();
                $table->string('participant_employer_text')->nullable();
                $table->string('session_id')->nullable();
                $table->dateTime('date_time')->nullable();
                $table->string('app_user_firstname')->nullable();
                $table->string('app_user_lastname')->nullable();
                $table->string('app_user_email')->nullable();
                $table->string('app_user_mobile')->nullable();
                $table->index(['profile_id', 'session_id']);
            });
        }

        if (! Schema::hasTable('form_builder_recipient')) {
            Schema::create('form_builder_recipient', function (Blueprint $table) {
                $table->unsignedBigInteger('recipient_id')->primary();
                $table->unsignedBigInteger('form_id');
                $table->string('recipient_email');
                $table->index('form_id');
            });
        }

        if (! Schema::hasTable('form_builder_library')) {
            Schema::create('form_builder_library', function (Blueprint $table) {
                $table->unsignedBigInteger('form_builder_library_id')->primary();
                $table->unsignedBigInteger('form_id');
                $table->foreignId('user_id')->nullable()->constrained('client_users')->nullOnDelete();
                $table->string('form_title')->nullable();
                $table->boolean('is_deleted')->default(false);
                $table->boolean('is_deleted_from_library')->default(false);
                $table->index('form_id');
            });
        }

        if (! Schema::hasTable('participant')) {
            Schema::create('participant', function (Blueprint $table) {
                $table->unsignedBigInteger('participant_id')->primary();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('name');
                $table->string('employer_cmp')->nullable();
                $table->date('due_date')->nullable();
                $table->date('participated_date')->nullable();
                $table->boolean('is_participated')->default(false);
                $table->index(['profile_id', 'due_date', 'is_participated']);
            });
        }

        if (! Schema::hasTable('participant_recipient')) {
            Schema::create('participant_recipient', function (Blueprint $table) {
                $table->unsignedBigInteger('participant_recipient_id')->primary();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('email');
                $table->index('profile_id');
            });
        }

        if (! Schema::hasTable('voc_users')) {
            Schema::create('voc_users', function (Blueprint $table) {
                $table->unsignedBigInteger('voc_user_id')->primary();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('email');
                $table->string('password')->nullable();
                $table->unsignedBigInteger('auth_user_id')->nullable()->index();
                $table->index('profile_id');
            });
        }

        if (! Schema::hasTable('voc_documents')) {
            Schema::create('voc_documents', function (Blueprint $table) {
                $table->unsignedBigInteger('voc_document_id')->primary();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('name')->nullable();
                $table->date('expiry_date')->nullable();
                $table->string('file_name')->nullable();
                $table->index('profile_id');
            });
        }

        if (! Schema::hasTable('voc_recipients')) {
            Schema::create('voc_recipients', function (Blueprint $table) {
                $table->unsignedBigInteger('voc_recipient_id')->primary();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('email');
                $table->index('profile_id');
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'voc_recipients',
            'voc_documents',
            'voc_users',
            'participant_recipient',
            'participant',
            'form_builder_library',
            'form_builder_recipient',
            'form_builder_answers',
            'form_builder_question_options',
            'form_builder_question',
            'form_builder_question_types',
            'user_info',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }
    }
};
