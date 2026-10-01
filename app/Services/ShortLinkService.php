<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\ProfileShortLink;
use Illuminate\Database\UniqueConstraintViolationException;

/**
 * Short links minted and served by this application.
 *
 * The point is to owe nothing to a third party. A code printed on a label has to resolve for
 * as long as the label exists, and with TinyURL that outlives our control of it: the account
 * lapsing, a rate limit, a change of terms, and every printed QR in the field stops working.
 *
 * Two deliberate differences from the service it replaces:
 *
 *  - The destination is worked out at scan time, never stored. A client renaming their URL
 *    would otherwise strand every code already printed; here the code simply follows them.
 *  - Codes are random, not sequential. A counter would let anyone enumerate every client's
 *    codes by incrementing.
 */
class ShortLinkService
{
    /** Base62. Nobody types these - they are scanned - so the full alphabet is fine. */
    protected const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /** How many times to re-roll on the astronomically unlikely collision. */
    protected const ATTEMPTS = 8;

    /** Selected as the driver, and configured well enough to produce a working link? */
    public function enabled(): bool
    {
        return config('shortlinks.driver') === 'local' && filled($this->domain());
    }

    public function domain(): ?string
    {
        $domain = config('shortlinks.domain');

        if (! is_string($domain)) {
            return null;
        }

        $domain = trim($domain);
        // Tolerate someone pasting a full URL into the env var.
        $domain = preg_replace('#^https?://#i', '', $domain) ?? $domain;

        return trim($domain, "/ \t\n\r\0\x0B") ?: null;
    }

    /** The short URL for this profile, minting its code the first time it is asked for. */
    public function urlFor(Profile $profile): string
    {
        return $this->baseUrl().'/'.$this->codeFor($profile);
    }

    public function baseUrl(): string
    {
        return config('shortlinks.scheme', 'https').'://'.$this->domain();
    }

    /**
     * This profile's code, issued once and kept.
     *
     * Re-issuing would break a label already in the field, so an existing row always wins.
     */
    public function codeFor(Profile $profile): string
    {
        $existing = ProfileShortLink::query()
            ->where('profile_id', $profile->id)
            ->value('code');

        if (is_string($existing) && $existing !== '') {
            return $existing;
        }

        for ($attempt = 0; $attempt < self::ATTEMPTS; $attempt++) {
            $code = $this->mint();

            try {
                ProfileShortLink::query()->create([
                    'profile_id' => $profile->id,
                    'code' => $code,
                ]);

                return $code;
            } catch (UniqueConstraintViolationException) {
                // Either the code collided or this profile was given one a moment ago by a
                // parallel request. The unique indexes are the only safe arbiter under
                // concurrency, which is why this catches rather than checking first.
                $existing = ProfileShortLink::query()
                    ->where('profile_id', $profile->id)
                    ->value('code');

                if (is_string($existing) && $existing !== '') {
                    return $existing;
                }
            }
        }

        throw new \RuntimeException('Could not mint a unique short code after '.self::ATTEMPTS.' attempts.');
    }

    /** The profile a scanned code belongs to, or null if we never issued it. */
    public function resolve(string $code): ?Profile
    {
        $code = trim($code);

        if ($code === '') {
            return null;
        }

        $link = ProfileShortLink::query()
            ->where('code', $code)
            ->with('profile.client', 'profile.equipmentType')
            ->first();

        return $link?->profile;
    }

    /**
     * Where a scan of this profile's code should land.
     *
     * Mirrors what the QR would otherwise have encoded: a CustomQR profile points at its own
     * destination, everything else at the scan page.
     */
    public function destinationFor(Profile $profile, ProfileQrService $qr): string
    {
        $profile->loadMissing(['equipmentType', 'client']);

        if ($profile->typeSlug() === 'customqr' && filled($profile->name)) {
            return (string) $profile->name;
        }

        return $qr->profileUrl($profile);
    }

    protected function mint(): string
    {
        $length = max(4, (int) config('shortlinks.length', 7));
        $alphabet = self::ALPHABET;
        $max = strlen($alphabet) - 1;
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, $max)];
        }

        return $code;
    }
}
