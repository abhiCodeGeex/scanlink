<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Solutions Enquiry Form submission — parity with the legacy welcome/enquiry page.
 */
class EnquiryMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<int, string>  $interests
     */
    public function __construct(
        public string $companyName,
        public string $contactName,
        public string $email,
        public string $tel,
        public string $address,
        public string $industryType,
        public string $companySize,
        public string $briefDescription,
        public array $interests,
        public string $comments,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->email, $this->contactName ?: $this->companyName)],
            subject: 'ScanLink Solutions Enquiry - '.($this->companyName ?: $this->contactName),
        );
    }

    public function content(): Content
    {
        $rows = [
            'Company Name' => $this->companyName,
            'Contact Name' => $this->contactName,
            'Email' => $this->email,
            'Telephone' => $this->tel,
            'Address' => $this->address,
            'Industry Type' => $this->industryType,
            'Company Size' => $this->companySize,
            'Brief Description' => $this->briefDescription,
            'Interested in' => $this->interests ? implode(', ', $this->interests) : '-',
            'Comments' => $this->comments,
        ];

        $html = '<html><head><title>ScanLink Solutions Enquiry</title></head><body>'
            .'<p>A new Solutions Enquiry has been submitted via the website.</p>'
            .'<table cellpadding="4" cellspacing="0">';

        foreach ($rows as $label => $value) {
            $html .= '<tr><th align="right" valign="top">'.e($label).' : </th><td>'.nl2br(e((string) $value)).'</td></tr>';
        }

        $html .= '</table></body></html>';

        return new Content(htmlString: $html);
    }
}
