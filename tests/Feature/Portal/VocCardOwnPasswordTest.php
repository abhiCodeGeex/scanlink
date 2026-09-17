<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocUser;
use App\Support\LoginEntrance;
use App\Support\VocCardLogin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A VOCC card has its own password, as it did in legacy, where /voclogin checked voc_users alone.
 *
 * The rebuild checked the person's `users` password at /voclogin and copied the card password onto
 * that login, so a client who also held a card (admin@nwit.com.au) had their client login
 * overwritten by the card. The same email may now be a client with one password and a card holder
 * with another, and neither door accepts or changes the other's password.
 */
class VocCardOwnPasswordTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_PASSWORD = 'Client@12345';

    private const CARD_PASSWORD = 'Card@67890';

    #[Test]
    public function the_same_email_signs_in_with_the_card_password_at_voclogin_and_the_client_password_at_the_client_login(): void
    {
        $login = $this->clientWhoHoldsACard('same@example.com');

        $this->post('/voclogin', ['email' => 'same@example.com', 'password' => self::CARD_PASSWORD])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/portal/voc-dashboard');
        $this->assertAuthenticatedAs($login);
        $this->assertSame(LoginEntrance::VOC, session(LoginEntrance::SESSION_KEY));

        $this->signOut();

        $this->post('/portal-login', ['email' => 'same@example.com', 'password' => self::CLIENT_PASSWORD])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/portal/profiles');
        $this->assertAuthenticatedAs($login);
        $this->assertSame(LoginEntrance::CLIENT, session(LoginEntrance::SESSION_KEY));
    }

    #[Test]
    public function the_client_password_does_not_open_the_card(): void
    {
        $this->clientWhoHoldsACard('same@example.com');

        $this->post('/voclogin', ['email' => 'same@example.com', 'password' => self::CLIENT_PASSWORD])
            ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);

        $this->assertGuest();
    }

    #[Test]
    public function the_card_password_does_not_open_the_client_login(): void
    {
        $this->clientWhoHoldsACard('same@example.com');

        $this->post('/portal-login', ['email' => 'same@example.com', 'password' => self::CARD_PASSWORD])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function adding_or_editing_a_card_never_changes_the_client_login(): void
    {
        $login = $this->clientWhoHoldsACard('same@example.com');
        $hash = $login->fresh()->getRawOriginal('password');

        $card = VocUser::query()->where('email', 'same@example.com')->firstOrFail();
        $card->update(['password' => 'Changed@111']);
        $card->update(['email' => 'same@example.com ']);

        $login->refresh();
        $this->assertSame(UserType::Portal, $login->user_type, 'a client member is never relabelled voc by their card');
        $this->assertSame($hash, $login->getRawOriginal('password'), 'the client password is never overwritten by the card');
        $this->assertTrue(Hash::check(self::CLIENT_PASSWORD, $login->password));
    }

    #[Test]
    public function a_card_holder_sees_only_the_cards_their_password_unlocked(): void
    {
        $this->card($this->vocProfile('FIRST-CARD'), 'holder@example.com', 'First@1111');
        $this->card($this->vocProfile('SECOND-CARD'), 'holder@example.com', 'Second@2222');

        $this->post('/voclogin', ['email' => 'holder@example.com', 'password' => 'First@1111'])
            ->assertRedirect('/portal/voc-dashboard');

        $this->get('/portal/voc-dashboard')
            ->assertSuccessful()
            ->assertSee('FIRST-CARD')
            ->assertDontSee('SECOND-CARD');
    }

    #[Test]
    public function a_vocc_session_cannot_reach_the_client_account_page(): void
    {
        $this->clientWhoHoldsACard('same@example.com');

        $this->post('/voclogin', ['email' => 'same@example.com', 'password' => self::CARD_PASSWORD]);

        $this->get('/portal/account')->assertRedirect('/portal/voc-dashboard');
        $this->assertFalse(\App\Filament\Portal\Pages\EditAccount::canAccess());
    }

    #[Test]
    public function a_legacy_plaintext_card_password_still_works_and_is_hashed_on_first_use(): void
    {
        $card = $this->card($this->vocProfile('LEGACY-CARD'), 'legacy@example.com', 'placeholder');
        DB::table('voc_users')->where('voc_user_id', $card->voc_user_id)->update(['password' => 'plain-legacy']);

        $this->post('/voclogin', ['email' => 'legacy@example.com', 'password' => 'plain-legacy'])
            ->assertRedirect('/portal/voc-dashboard');

        $stored = (string) DB::table('voc_users')->where('voc_user_id', $card->voc_user_id)->value('password');
        $this->assertTrue(Hash::check('plain-legacy', $stored));
    }

    #[Test]
    public function a_client_members_card_with_no_password_yet_is_told_to_set_one(): void
    {
        $this->clientWhoHoldsACard('same@example.com');
        DB::table('voc_users')->update(['password' => '']);

        $this->post('/voclogin', ['email' => 'same@example.com', 'password' => self::CLIENT_PASSWORD])
            ->assertSessionHasErrors(['email' => 'This card login has no password yet. Use "Forgot your password?" to set one.']);

        $this->assertGuest();
    }

    #[Test]
    public function a_pure_card_holder_whose_card_lost_its_password_keeps_the_one_they_know(): void
    {
        // The old sync blanked voc_users.password after copying it onto a card-holder-only login.
        $card = $this->card($this->vocProfile('ONLY-CARD'), 'only@example.com', 'placeholder');
        $login = User::query()->findOrFail($card->fresh()->auth_user_id);
        $login->forceFill(['password' => 'Known@4444'])->save();
        $loginHash = $login->fresh()->getRawOriginal('password');
        DB::table('voc_users')->update(['password' => '']);

        $this->post('/voclogin', ['email' => 'only@example.com', 'password' => 'Known@4444'])
            ->assertRedirect('/portal/voc-dashboard');

        $this->assertTrue(Hash::check('Known@4444', (string) DB::table('voc_users')->value('password')), 'the card gets its own copy');
        $this->assertSame($loginHash, $login->fresh()->getRawOriginal('password'), 'the login password is not changed');
    }

    #[Test]
    public function forgot_password_at_voclogin_resets_the_card_and_leaves_the_client_login_alone(): void
    {
        Mail::fake();
        $login = $this->clientWhoHoldsACard('same@example.com');
        $loginHash = $login->fresh()->getRawOriginal('password');

        $this->post('/voclogin/forgot', ['email' => 'same@example.com'])->assertSessionHas('status');

        $resetUrl = null;
        Mail::assertSent(ScanlinkMail::class, function (ScanlinkMail $mail) use (&$resetUrl): bool {
            $resetUrl = $mail->mailData['resetUrl'] ?? null;

            return $mail->hasTo('same@example.com');
        });
        $this->assertIsString($resetUrl);

        $this->get($resetUrl)->assertSuccessful()->assertSee('Set a new card password');

        $this->post($resetUrl, ['password' => 'NewCard@555', 'password_confirmation' => 'NewCard@555'])
            ->assertRedirect(route('marketing.voclogin'));

        $this->assertTrue(Hash::check('NewCard@555', (string) DB::table('voc_users')->value('password')));
        $this->assertSame($loginHash, $login->fresh()->getRawOriginal('password'), 'the client password is untouched');

        // Used once: the same link no longer works.
        $this->post($resetUrl, ['password' => 'Again@666', 'password_confirmation' => 'Again@666'])
            ->assertRedirect(route('marketing.voclogin.forgot'));
        $this->assertTrue(Hash::check('NewCard@555', (string) DB::table('voc_users')->value('password')));

        $this->post('/voclogin', ['email' => 'same@example.com', 'password' => 'NewCard@555'])
            ->assertRedirect('/portal/voc-dashboard');
    }

    #[Test]
    public function forgot_password_does_not_reveal_whether_an_email_holds_a_card(): void
    {
        Mail::fake();

        $this->post('/voclogin/forgot', ['email' => 'nobody@example.com'])->assertSessionHas('status');

        Mail::assertNothingSent();
    }

    private function signOut(): void
    {
        Auth::logout();
        session()->flush();
        $this->flushSession();
    }

    private function clientWhoHoldsACard(string $email): User
    {
        $member = ClientUser::factory()->primary()->create([
            'client_id' => Client::factory()->create()->id,
            'email' => $email,
            'password' => self::CLIENT_PASSWORD,
            'status' => true,
            'is_password_change' => true,
        ]);

        $this->card($this->vocProfile('CLIENT-HOLDERS-CARD'), $email, self::CARD_PASSWORD);

        return User::query()->findOrFail($member->fresh()->auth_user_id);
    }

    private function card(Profile $profile, string $email, string $password): VocUser
    {
        return VocUser::query()->create([
            'voc_user_id' => ((int) VocUser::query()->max('voc_user_id')) + 1,
            'profile_id' => $profile->id,
            'email' => $email,
            'password' => $password,
        ]);
    }

    private function vocProfile(string $name): Profile
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;

        return Profile::factory()->create([
            'client_id' => Client::factory()->create()->id,
            'type_id' => $typeId,
            'code_profile_name' => $name,
            'name' => $name,
            'deleted' => 0,
            'update_or_not' => true,
        ]);
    }
}
