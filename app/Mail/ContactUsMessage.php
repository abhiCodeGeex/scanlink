<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $comments,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($this->senderEmail, $this->senderName),
            ],
            subject: 'Contact Us - From '.$this->senderName,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<html><head><title>Contact Us - From '.e($this->senderName).'</title></head><body>'
                .'<p>Here are the Contact Us Information</p>'
                .'<table cellpadding="3" cellspacing="0">'
                .'<tr><th align="right">Name : </th><td>'.e($this->senderName).'</td></tr>'
                .'<tr><th align="right">Email : </th><td>'.e($this->senderEmail).'</td></tr>'
                .'<tr><th align="right">Comments : </th><td>'.nl2br(e($this->comments)).'</td></tr>'
                .'</table></body></html>',
        );
    }
}
