<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientWelcomeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ScanLink client account has been created',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: view('mail.client-welcome', [
                'client' => $this->client,
                'plainPassword' => $this->plainPassword,
                'portalUrl' => url('/'),
            ])->render(),
        );
    }
}
