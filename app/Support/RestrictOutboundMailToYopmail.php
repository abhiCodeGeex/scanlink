<?php

namespace App\Support;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Testing safety net: only deliver mail to @yopmail.com addresses.
 * Non-matching To/Cc/Bcc recipients are stripped; if no To remains, the send is cancelled.
 */
class RestrictOutboundMailToYopmail
{
    public function handle(MessageSending $event): ?bool
    {
        if (! (bool) config('scanlink.mail_restrict_enabled', true)) {
            return null;
        }

        $domain = strtolower((string) config('scanlink.mail_restrict_domain', 'yopmail.com'));
        $message = $event->message;

        $blocked = [];
        $allowedTo = $this->filterAddresses($message->getTo(), $domain, $blocked);
        $allowedCc = $this->filterAddresses($message->getCc(), $domain, $blocked);
        $allowedBcc = $this->filterAddresses($message->getBcc(), $domain, $blocked);

        if ($blocked !== []) {
            Log::warning('Outbound mail blocked (non-yopmail recipients)', [
                'blocked' => $blocked,
                'allowed_to' => array_map(fn (Address $a): string => $a->getAddress(), $allowedTo),
                'subject' => $message->getSubject(),
            ]);
        }

        $this->replaceHeader($message, 'To', $allowedTo);
        $this->replaceHeader($message, 'Cc', $allowedCc);
        $this->replaceHeader($message, 'Bcc', $allowedBcc);

        if ($allowedTo === []) {
            Log::warning('Outbound mail cancelled — no @'.$domain.' To recipients', [
                'subject' => $message->getSubject(),
                'blocked' => $blocked,
            ]);

            return false;
        }

        return null;
    }

    /**
     * @param  list<Address>  $addresses
     * @param  list<string>  $blocked
     * @return list<Address>
     */
    private function filterAddresses(array $addresses, string $domain, array &$blocked): array
    {
        $allowed = [];

        foreach ($addresses as $address) {
            $email = strtolower($address->getAddress());
            $host = substr(strrchr($email, '@') ?: '', 1);

            if ($host === $domain) {
                $allowed[] = $address;
            } else {
                $blocked[] = $address->getAddress();
            }
        }

        return $allowed;
    }

    /**
     * @param  list<Address>  $addresses
     */
    private function replaceHeader(Email $message, string $header, array $addresses): void
    {
        if ($addresses === []) {
            $message->getHeaders()->remove($header);

            return;
        }

        match ($header) {
            'To' => $message->to(...$addresses),
            'Cc' => $message->cc(...$addresses),
            'Bcc' => $message->bcc(...$addresses),
            default => null,
        };
    }
}
