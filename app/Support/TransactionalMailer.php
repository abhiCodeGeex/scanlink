<?php

namespace App\Support;

use App\Mail\ScanlinkMail;
use Illuminate\Support\Facades\Mail;

/**
 * Sends a transactional email at most once per recipient per request.
 *
 * Purchase and renewal both fan out to several recipients, and the same address could be
 * reached more than once — a client buying with their own reseller code, or a renewal
 * spanning several reseller groups. Nobody should receive the same message twice.
 *
 * Registered as a singleton, so the record of what has been sent lasts exactly as long as
 * the request that is sending it.
 *
 * This is a backstop, not a licence to call it twice: two sends that differ in content are
 * two different messages and both go out. Where one transaction should produce one message,
 * the caller still has to say so.
 */
class TransactionalMailer
{
    /** @var array<string, true> */
    protected array $sent = [];

    /**
     * @return bool whether the message was handed to the mailer
     */
    public function sendOnce(string $email, ScanlinkMail $mail): bool
    {
        $email = strtolower(trim($email));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $key = $this->fingerprint($email, $mail);

        if (isset($this->sent[$key])) {
            return false;
        }

        try {
            Mail::to($email)->send($mail);
        } catch (\Throwable $e) {
            // Never fail a purchase or renewal because outbound mail is unavailable — and do
            // not record the attempt, so a retry is still possible.
            report($e);

            return false;
        }

        $this->sent[$key] = true;

        return true;
    }

    /** How many messages actually went out. Used by the tests. */
    public function sentCount(): int
    {
        return count($this->sent);
    }

    protected function fingerprint(string $email, ScanlinkMail $mail): string
    {
        return $email.'|'.$mail->subjectLine.'|'.$mail->viewName.'|'.md5(serialize($mail->mailData));
    }
}
