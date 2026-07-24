<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\CodePurchase;
use App\Models\User;
use App\Services\ContactCaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

class PortalRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_is_available(): void
    {
        $this->get('/portal/register')->assertOk();
    }

    public function test_client_can_complete_registration_wizard(): void
    {
        Mail::fake();

        Session::put(ContactCaptchaService::SESSION_KEY, sha1('ABCD'));

        Livewire::test(\App\Filament\Portal\Auth\Register::class)
            ->set('data.first_name', 'Jane')
            ->set('data.last_name', 'Doe')
            ->set('data.company_name', 'Acme Pty')
            ->set('data.billing_address', '1 Test St')
            ->set('data.email', 'jane.doe@example.com')
            ->set('data.town', 'Melbourne')
            ->set('data.phone', '0412345678')
            ->set('data.postal_code', '3000')
            ->set('data.password', 'Secret1')
            ->set('data.cpassword', 'Secret1')
            ->set('data.client_reseller_code', '')
            ->set('data.captcha', 'ABCD')
            ->call('register')
            ->assertSet('wizardStep', 2)
            ->assertHasNoErrors()
            ->call('register')
            ->assertSet('showNearlyDoneModal', true)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'client_name' => 'Acme Pty',
            'email' => 'jane.doe@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane.doe@example.com',
            'user_type' => UserType::Portal->value,
        ]);

        $user = User::query()->where('email', 'jane.doe@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('client_users', [
            'email' => 'jane.doe@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $client = Client::query()->where('email', 'jane.doe@example.com')->firstOrFail();
        $this->assertTrue(
            CodePurchase::query()
                ->where('client_id', $client->id)
                ->where('free_code', true)
                ->where('no_of_codes', 1)
                ->exists()
        );

        Mail::assertSent(\App\Mail\RegistrationWelcomeMail::class);
    }

    public function test_landing_page_links_to_register(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('/portal/register', false);
    }
}
