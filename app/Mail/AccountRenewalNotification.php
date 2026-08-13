<?php

namespace App\Mail;

use App\Models\ClientUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountRenewalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ClientUser $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re-new account notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: view('mail.account-renewal', [
                'user' => $this->user,
            ])->render(),
        );
    }
}
