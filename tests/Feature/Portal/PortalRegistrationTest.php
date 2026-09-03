<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Auth\Register;
use App\Mail\RegistrationWelcomeMail;
use App\Models\Client;
use App\Models\ClientUser;
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

    public function test_register_page_shows_legacy_two_column_markup(): void
    {
        $html = $this->get('/portal/register')->assertOk()->getContent();

        $this->assertStringContainsString('sl-reg-cols', $html);
        $this->assertStringContainsString('sl-reg-col-left', $html);
        $this->assertStringContainsString('sl-reg-col-right', $html);
        $this->assertStringContainsString('breadcrumbsMain', $html);
        $this->assertStringContainsString('id="frmregister"', $html);
        $this->assertStringContainsString('class="save"', $html);

        $leftPos = strpos($html, 'id="first_name"');
        $rightPos = strpos($html, 'id="last_name"');

        $this->assertNotFalse($leftPos);
        $this->assertNotFalse($rightPos);
        $this->assertLessThan($rightPos, $leftPos);
    }

    public function test_register_page_shows_legacy_fields_and_three_step_bar(): void
    {
        $html = $this->get('/portal/register')->assertOk()->getContent();

        foreach ([
            'First name:',
            'Last name:',
            'Company name/Business name:',
            'Address:',
            'Email:',
            'Town:',
            'Telephone number:',
            'Postal code:',
            'Password:',
            'Confirm Password:',
            'Verification code:',
            'Reseller Code',
            'Enter',
            'Select',
            'Upload',
            'web links, videos',
        ] as $label) {
            $this->assertStringContainsString($label, $html);
        }
    }

    public function test_invalid_step_one_does_not_advance_wizard(): void
    {
        Session::put(ContactCaptchaService::SESSION_KEY, sha1('TEST'));

        Livewire::test(Register::class)
            ->set('data', [
                'first_name' => '',
                'last_name' => '',
                'company_name' => '',
                'billing_address' => '',
                'email' => '',
                'town' => '',
                'phone' => '',
                'postal_code' => '',
                'password' => '',
                'cpassword' => '',
                'client_reseller_code' => '',
                'captcha' => '',
                'no_codes' => '',
            ])
            ->call('register')
            ->assertHasErrors([
                'data.first_name',
                'data.last_name',
                'data.company_name',
                'data.billing_address',
                'data.email',
                'data.town',
                'data.phone',
                'data.postal_code',
                'data.password',
                'data.cpassword',
                'data.captcha',
            ])
            ->assertSet('wizardStep', 1)
            ->assertSet('showNearlyDoneModal', false);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_first_next_stays_on_page_at_step_two_without_creating_account(): void
    {
        Session::put(ContactCaptchaService::SESSION_KEY, sha1('TEST'));

        $email = 'wizard-step1@example.com';

        Livewire::test(Register::class)
            ->set('data', $this->validData($email))
            ->call('register')
            ->assertHasNoErrors()
            ->assertSet('wizardStep', 2)
            ->assertSet('showNearlyDoneModal', false);

        $this->assertDatabaseMissing('users', ['email' => $email]);
        $this->assertDatabaseMissing('clients', ['email' => $email]);
    }

    public function test_second_next_creates_account_sends_mail_shows_popup_and_logs_in(): void
    {
        Mail::fake();
        Session::put(ContactCaptchaService::SESSION_KEY, sha1('TEST'));

        $email = 'new-client@example.com';

        Livewire::test(Register::class)
            ->set('data', $this->validData($email))
            ->call('register')
            ->assertSet('wizardStep', 2)
            ->call('register')
            ->assertHasNoErrors()
            ->assertSet('showNearlyDoneModal', true);

        $this->assertAuthenticated();

        Mail::assertSent(RegistrationWelcomeMail::class, function (RegistrationWelcomeMail $mail) use ($email): bool {
            return $mail->hasTo($email)
                && $mail->firstName === 'Jane'
                && $mail->lastName === 'Client';
        });

        $this->assertDatabaseHas('clients', [
            'client_name' => 'Acme Scan Co',
            'email' => $email,
            'address' => '1 Test Street',
        ]);

        $user = User::query()->where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertSame(UserType::Portal, $user->user_type);

        $member = ClientUser::query()
            ->where('email', $email)
            ->where('auth_user_id', $user->id)
            ->first();

        $this->assertNotNull($member);
        $this->assertSame('Jane', $member->first_name);
        $this->assertSame('Client', $member->last_name);
        $this->assertSame('Melbourne', $member->town);
        $this->assertSame('3000', (string) $member->postal_code);

        $this->assertTrue(
            CodePurchase::query()
                ->where('client_id', $member->client_id)
                ->where('no_of_codes', 1)
                ->where('free_code', true)
                ->exists()
        );
    }

    public function test_registration_rejects_invalid_captcha(): void
    {
        Session::put(ContactCaptchaService::SESSION_KEY, sha1('TEST'));

        Livewire::test(Register::class)
            ->set('data', array_merge($this->validData('bad-captcha@example.com'), [
                'captcha' => 'WRONG',
            ]))
            ->call('register')
            ->assertHasErrors(['data.captcha'])
            ->assertSet('wizardStep', 1);
    }

    public function test_portal_user_can_open_master_code_list(): void
    {
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'codes@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user)
            ->get('/portal/profiles')
            ->assertOk();
    }

    /**
     * @return array<string, string>
     */
    private function validData(string $email): array
    {
        return [
            'first_name' => 'Jane',
            'last_name' => 'Client',
            'company_name' => 'Acme Scan Co',
            'billing_address' => '1 Test Street',
            'email' => $email,
            'town' => 'Melbourne',
            'phone' => '0400000000',
            'postal_code' => '3000',
            'password' => 'Pass12',
            'cpassword' => 'Pass12',
            'client_reseller_code' => '',
            'captcha' => 'TEST',
            'no_codes' => '',
        ];
    }
}
