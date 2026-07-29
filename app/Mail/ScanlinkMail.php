<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Generic ScanLink transactional email: a subject + a Blade view + data.
 * Used for renewal and expiry notification families (legacy mailsend.php /
 * profileexpiry.php) so each message is just a view template.
 */
class ScanlinkMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $mailData
     */
    public function __construct(
        public string $subjectLine,
        public string $viewName,
        public array $mailData = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: $this->viewName, with: $this->mailData);
    }
}
