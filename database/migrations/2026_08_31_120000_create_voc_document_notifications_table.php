<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Records which VOCC document notices have already gone out.
 *
 * The sender used to match an exact date — expiry_date == today, or == today + 30. A missed
 * scheduler run therefore skipped that day's cohort permanently, because the next day's query
 * no longer matched them. Widening it to a window means a missed day is caught up on the next
 * run, and this table is what stops the widened query re-sending every day thereafter.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('voc_document_notifications')) {
            return;
        }

        Schema::create('voc_document_notifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('voc_document_id')->index();
            // 'reminder' (30 days out) or 'expired' (on/after the expiry date).
            $table->string('kind', 16);
            $table->date('expiry_date');
            $table->timestamp('sent_at')->nullable();

            // One notice of each kind per document per expiry date. If the date is edited the
            // document becomes eligible again, which is the behaviour a renewal needs.
            $table->unique(['voc_document_id', 'kind', 'expiry_date'], 'voc_doc_notification_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voc_document_notifications');
    }
};
