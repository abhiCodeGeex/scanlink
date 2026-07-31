<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Legacy registration welcome (mailsend.php registration_mail_client).
 */
class RegistrationWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address', 'info@scanlink.com.au'),
                'ScanLink',
            ),
            subject: 'Welcome to ScanLink',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.registration-welcome',
        );
    }
}
