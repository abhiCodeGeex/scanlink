<?php

namespace Tests\Feature\Portal;

use App\Mail\ScanlinkMail;
use App\Support\TransactionalMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Purchase and renewal both fan out to buyer, reseller and admin. The admin was receiving one
 * "Scanlink Purchase Code" for the buyer and a second for the reseller on the same order — and
 * on renewal, one more for every reseller group in the selection.
 *
 * These cover the guard that makes a repeat impossible regardless of call site. The structural
 * side (one admin message per transaction) is covered by
 * {@see \Tests\Feature\Portal\PurchaseRenewEmailFanoutTest}.
 */
class NoDuplicateTransactionalEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function mailer(): TransactionalMailer
    {
        return app(TransactionalMailer::class);
    }

    protected function message(string $subject = 'Scanlink Purchase Code', array $data = ['a' => 1]): ScanlinkMail
    {
        return new ScanlinkMail($subject, 'emails.admin-code', $data);
    }

    #[Test]
    public function the_same_message_to_the_same_person_goes_once(): void
    {
        Mail::fake();

        $this->assertTrue($this->mailer()->sendOnce('admin@example.com', $this->message()));
        $this->assertFalse($this->mailer()->sendOnce('admin@example.com', $this->message()));

        Mail::assertSent(ScanlinkMail::class, 1);
    }

    #[Test]
    public function the_address_is_compared_case_and_space_insensitively(): void
    {
        Mail::fake();

        $this->mailer()->sendOnce('Admin@Example.com', $this->message());
        $this->mailer()->sendOnce('  admin@example.com  ', $this->message());

        Mail::assertSent(ScanlinkMail::class, 1);
    }

    #[Test]
    public function genuinely_different_messages_still_go(): void
    {
        Mail::fake();

        // Different recipient.
        $this->mailer()->sendOnce('admin@example.com', $this->message());
        $this->mailer()->sendOnce('buyer@example.com', $this->message());
        // Same recipient, different content — a second order, not a duplicate.
        $this->mailer()->sendOnce('admin@example.com', $this->message(data: ['a' => 2]));
        // Same recipient, different subject.
        $this->mailer()->sendOnce('admin@example.com', $this->message('Scanlink Renew Code'));

        Mail::assertSent(ScanlinkMail::class, 4);
    }

    #[Test]
    public function an_unusable_address_is_skipped_quietly(): void
    {
        Mail::fake();

        $this->assertFalse($this->mailer()->sendOnce('', $this->message()));
        $this->assertFalse($this->mailer()->sendOnce('not-an-email', $this->message()));

        Mail::assertNothingSent();
        $this->assertSame(0, $this->mailer()->sentCount());
    }

    #[Test]
    public function a_failed_send_is_swallowed_and_stays_retryable(): void
    {
        // A transport failure must not abort a purchase, and must not be recorded as sent —
        // otherwise a retry inside the same request would be silently dropped.
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('smtp down'));

        $this->assertFalse($this->mailer()->sendOnce('admin@example.com', $this->message()));
        $this->assertSame(0, $this->mailer()->sentCount());
    }
}
