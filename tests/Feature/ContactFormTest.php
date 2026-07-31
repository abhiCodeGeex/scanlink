<?php

namespace Tests\Feature;

use App\Mail\ContactUsMessage;
use App\Services\ContactCaptchaService;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            PreventRequestForgery::class,
        ]);
    }

    public function test_contact_page_shows_legacy_form(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('Contact us')
            ->assertSee('Verification Code:')
            ->assertSee('+61 0364314025')
            ->assertSee('Emu Heights');
    }

    public function test_captcha_image_is_available(): void
    {
        $this->get('/captcha/default')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_contact_form_rejects_invalid_captcha(): void
    {
        $this->withSession([
            ContactCaptchaService::SESSION_KEY => sha1('TEST'),
        ])
            ->from('/contact')
            ->post('/contact', [
                'name' => 'Jane',
                'email' => 'jane@example.com',
                'comments' => 'Hello there',
                'captcha' => 'WRONG',
                'submit' => 'Send',
            ])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors('captcha');
    }

    public function test_contact_form_sends_mail_on_success(): void
    {
        Mail::fake();

        $this->withSession([
            ContactCaptchaService::SESSION_KEY => sha1('TEST'),
        ])
            ->from('/contact')
            ->post('/contact', [
                'name' => 'Jane',
                'email' => 'jane@example.com',
                'comments' => 'Hello there',
                'captcha' => 'TEST',
                'submit' => 'Send',
            ])
            ->assertRedirect('/contact')
            ->assertSessionHas('contact_submitted', true);

        Mail::assertSent(ContactUsMessage::class, function (ContactUsMessage $mail): bool {
            return $mail->senderName === 'Jane'
                && $mail->senderEmail === 'jane@example.com'
                && $mail->comments === 'Hello there';
        });
    }
}
