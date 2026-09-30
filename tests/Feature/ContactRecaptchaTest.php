<?php

namespace Tests\Feature;

use App\Filament\Portal\Pages\ContactUs;
use App\Mail\ContactUsMessage;
use App\Models\User;
use App\Services\ContactCaptchaService;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Google reCAPTCHA v3 guards both contact forms.
 *
 * v3 shows the visitor nothing: the page fetches a token as the form is sent and Google scores
 * the visitor behind the scenes, so the check that matters is the one made here against
 * siteverify. Without keys configured the forms keep the built-in picture captcha rather than
 * going out with no challenge at all.
 */
class ContactRecaptchaTest extends TestCase
{
    use RefreshDatabase;

    private const VERIFY = 'https://www.google.com/recaptcha/api/siteverify';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([PreventRequestForgery::class]);
    }

    private function withKeys(float $minScore = 0.5): void
    {
        config([
            'captcha.recaptcha.site_key' => 'test-site-key',
            'captcha.recaptcha.secret_key' => 'test-secret-key',
            'captcha.recaptcha.min_score' => $minScore,
            'captcha.recaptcha.verify_url' => self::VERIFY,
            'captcha.recaptcha.action' => 'contact',
        ]);
    }

    private function withoutKeys(): void
    {
        config(['captcha.recaptcha.site_key' => null, 'captcha.recaptcha.secret_key' => null]);
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function googleSays(array $body): void
    {
        Http::fake([self::VERIFY => Http::response($body)]);
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function message(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pat Sender',
            'email' => 'pat@yopmail.com',
            'comments' => 'Hello there',
        ], $overrides);
    }

    #[Test]
    public function the_public_form_loads_the_v3_script_and_drops_the_picture_captcha(): void
    {
        $this->withKeys();

        $page = $this->get('/contact')->assertOk();

        $page->assertSee('recaptcha/api.js?render=test-site-key', false);
        $page->assertSee('name="g-recaptcha-response"', false);
        $page->assertDontSee('/captcha/default', false);
    }

    #[Test]
    public function without_keys_the_public_form_keeps_the_picture_captcha(): void
    {
        $this->withoutKeys();

        $this->get('/contact')
            ->assertOk()
            ->assertSee('/captcha/default', false)
            ->assertDontSee('recaptcha/api.js', false);
    }

    #[Test]
    public function a_good_score_sends_the_enquiry(): void
    {
        Mail::fake();
        $this->withKeys();
        $this->googleSays(['success' => true, 'score' => 0.9, 'action' => 'contact']);

        $this->post('/contact', $this->message(['g-recaptcha-response' => 'a-token']))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('marketing.contact'));

        Mail::assertSent(ContactUsMessage::class);
    }

    #[Test]
    public function a_score_below_the_threshold_is_turned_away(): void
    {
        Mail::fake();
        $this->withKeys(0.5);
        $this->googleSays(['success' => true, 'score' => 0.1, 'action' => 'contact']);

        $this->post('/contact', $this->message(['g-recaptcha-response' => 'a-token']))
            ->assertSessionHasErrors('captcha');

        Mail::assertNothingSent();
    }

    #[Test]
    public function a_refused_token_is_turned_away(): void
    {
        Mail::fake();
        $this->withKeys();
        $this->googleSays(['success' => false, 'error-codes' => ['invalid-input-response']]);

        $this->post('/contact', $this->message(['g-recaptcha-response' => 'a-token']))
            ->assertSessionHasErrors('captcha');

        Mail::assertNothingSent();
    }

    #[Test]
    public function a_missing_token_is_turned_away_without_asking_google(): void
    {
        Mail::fake();
        $this->withKeys();
        Http::fake();

        $this->post('/contact', $this->message())
            ->assertSessionHasErrors('captcha');

        Mail::assertNothingSent();
        Http::assertNothingSent();
    }

    #[Test]
    public function a_token_minted_for_another_action_is_turned_away(): void
    {
        Mail::fake();
        $this->withKeys();
        $this->googleSays(['success' => true, 'score' => 0.9, 'action' => 'login']);

        $this->post('/contact', $this->message(['g-recaptcha-response' => 'a-token']))
            ->assertSessionHasErrors('captcha');

        Mail::assertNothingSent();
    }

    #[Test]
    public function an_unreachable_google_does_not_swallow_the_enquiry(): void
    {
        Mail::fake();
        $this->withKeys();
        Http::fake([self::VERIFY => Http::response('', 500)]);

        $this->post('/contact', $this->message(['g-recaptcha-response' => 'a-token']))
            ->assertSessionHasNoErrors();

        Mail::assertSent(ContactUsMessage::class);
    }

    #[Test]
    public function a_blank_message_is_still_refused_before_google_is_asked(): void
    {
        Mail::fake();
        $this->withKeys();
        Http::fake();

        $this->post('/contact', $this->message(['comments' => '', 'g-recaptcha-response' => 'a-token']))
            ->assertSessionHasErrors('form');

        Mail::assertNothingSent();
        Http::assertNothingSent();
    }

    #[Test]
    public function without_keys_the_public_form_still_checks_the_picture_captcha(): void
    {
        Mail::fake();
        $this->withoutKeys();
        Session::put(ContactCaptchaService::SESSION_KEY, sha1('ABCD'));

        $this->post('/contact', $this->message(['captcha' => 'WXYZ']))
            ->assertSessionHasErrors('captcha');

        Mail::assertNothingSent();

        $this->post('/contact', $this->message(['captcha' => 'abcd']))
            ->assertSessionHasNoErrors();

        Mail::assertSent(ContactUsMessage::class);
    }

    #[Test]
    public function the_portal_form_shows_the_widget_and_sends_on_a_good_score(): void
    {
        Mail::fake();
        $this->withKeys();
        $this->googleSays(['success' => true, 'score' => 0.9, 'action' => 'contact']);

        $page = Livewire::actingAs($this->portalUser())->test(ContactUs::class)->assertOk();

        $page->assertSee('recaptcha/api.js?render=test-site-key', false);
        $page->assertDontSee('/captcha/default', false);

        $page->set('name', 'Pat Sender')
            ->set('email', 'pat@yopmail.com')
            ->set('comments', 'Hello there')
            ->call('submit', 'a-token')
            ->assertSet('submitted', true);

        Mail::assertSent(ContactUsMessage::class);
    }

    #[Test]
    public function the_portal_form_refuses_a_bad_score(): void
    {
        Mail::fake();
        $this->withKeys();
        $this->googleSays(['success' => true, 'score' => 0.1, 'action' => 'contact']);

        Livewire::actingAs($this->portalUser())
            ->test(ContactUs::class)
            ->set('name', 'Pat Sender')
            ->set('email', 'pat@yopmail.com')
            ->set('comments', 'Hello there')
            ->call('submit', 'a-token')
            ->assertSet('submitted', false)
            ->assertSet('formError', 'We could not verify that you are human. Please try again.');

        Mail::assertNothingSent();
    }

    #[Test]
    public function the_portal_form_keeps_the_picture_captcha_without_keys(): void
    {
        $this->withoutKeys();

        Livewire::actingAs($this->portalUser())
            ->test(ContactUs::class)
            ->assertOk()
            ->assertSee('/captcha/default', false)
            ->assertDontSee('recaptcha/api.js', false);
    }

    private function portalUser(): User
    {
        return User::factory()->create(['email' => 'contact-recaptcha@yopmail.com']);
    }
}
