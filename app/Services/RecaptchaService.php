<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google reCAPTCHA v3 for the contact forms.
 *
 * v3 shows the visitor nothing. The page asks Google for a token as the form is sent, and this
 * checks it server side: the token has to be valid, minted for the action we expect, and scored
 * at or above the configured threshold.
 */
class RecaptchaService
{
    /** Configured well enough to use? Without both keys the caller keeps the picture captcha. */
    public function enabled(): bool
    {
        return filled($this->siteKey()) && filled($this->secretKey());
    }

    public function siteKey(): ?string
    {
        return $this->setting('site_key');
    }

    public function action(): string
    {
        return $this->setting('action') ?? 'contact';
    }

    /**
     * Is this token good enough to let the message through?
     *
     * A missing or refused token is a no. An unreachable Google is a yes, with a warning in the
     * log: an outage of theirs is not something a visitor can bring about, and silently
     * swallowing every enquiry until it clears would cost far more than the spam it lets in.
     */
    public function verify(?string $token, ?Request $request = null): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        if (! is_string($token) || trim($token) === '') {
            return false;
        }

        $request ??= request();

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post((string) $this->setting('verify_url'), [
                    'secret' => $this->secretKey(),
                    'response' => trim($token),
                    'remoteip' => $request instanceof Request ? $request->ip() : null,
                ]);
        } catch (\Throwable $exception) {
            Log::warning('reCAPTCHA verification could not reach Google; letting the message through', [
                'message' => $exception->getMessage(),
            ]);

            return true;
        }

        if ($response->failed()) {
            Log::warning('reCAPTCHA verification returned an error; letting the message through', [
                'status' => $response->status(),
            ]);

            return true;
        }

        if ($response->json('success') !== true) {
            Log::info('reCAPTCHA refused a token', [
                'errors' => $response->json('error-codes'),
            ]);

            return false;
        }

        // A token minted elsewhere on the site would verify fine, so the action is checked too.
        $action = $response->json('action');

        if (is_string($action) && $action !== '' && $action !== $this->action()) {
            Log::info('reCAPTCHA token was minted for a different action', ['action' => $action]);

            return false;
        }

        return ((float) $response->json('score', 0)) >= $this->minScore();
    }

    public function minScore(): float
    {
        return (float) ($this->setting('min_score') ?? 0.5);
    }

    protected function secretKey(): ?string
    {
        return $this->setting('secret_key');
    }

    protected function setting(string $key): mixed
    {
        $value = config('captcha.recaptcha.'.$key);

        return is_string($value) ? (trim($value) !== '' ? trim($value) : null) : $value;
    }
}
