<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * VOCC card holders get their own login URL.
 *
 * /voclogin was a branded page whose form posted to /portal-login, so clients, sub-clients and
 * card holders all signed in through one endpoint under one policy. The page now posts to
 * POST /voclogin, and that endpoint admits card holders only.
 *
 * The client login is deliberately left permissive: a card holder who has always used the home
 * page modal keeps working. Only the VOCC address is restricted.
 */
class VocLoginUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function profile(): Profile
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;

        return Profile::factory()->create([
            'client_id' => Client::factory()->create()->id,
            'type_id' => $typeId,
            'deleted' => 0,
            'update_or_not' => true,
        ]);
    }

    /** A card holder as VocUserProvisioner makes one: a voc_users row owning the identity. */
    protected function cardHolder(string $email, string $password): User
    {
        $vocUser = VocUser::query()->create([
            'voc_user_id' => ((int) VocUser::query()->max('voc_user_id')) + 1,
            'profile_id' => $this->profile()->id,
            'email' => $email,
            'password' => $password,
        ]);

        return User::query()->findOrFail($vocUser->fresh()->auth_user_id);
    }

    /** An ordinary client member — no card holder access. */
    protected function member(string $email, string $password): User
    {
        $client = Client::factory()->create(['email' => $email]);

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => $email,
            'password' => $password,
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['email' => $email, 'user_type' => UserType::Portal, 'admin_role' => null]);

        return $user->refresh();
    }

    #[Test]
    public function the_card_holder_page_posts_to_its_own_url(): void
    {
        // The whole complaint in one assertion: the VOCC form must not aim at the client login.
        // The page still carries the site header's own Login modal - that is the shared nav,
        // not this form - so the assertion names the VOCC form exactly.
        $this->get('/voclogin')
            ->assertSuccessful()
            ->assertSee(
                '<form method="post" action="'.url('/voclogin').'" class="mkt__voclogin-form">',
                false,
            );
    }

    #[Test]
    public function a_failed_attempt_does_not_pop_the_client_modal_over_the_vocc_page(): void
    {
        // The layout opens its login modal whenever there are errors. On this page that would
        // drop the card holder straight back onto the login they were just moved off.
        $this->cardHolder('holder@example.com', 'Holder@12345');

        $this->from('/voclogin')->post('/voclogin', [
            'email' => 'holder@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect('/voclogin');

        $this->followingRedirects()
            ->from('/voclogin')
            ->post('/voclogin', ['email' => 'holder@example.com', 'password' => 'wrong-password'])
            ->assertSuccessful()
            ->assertDontSee('id="mkt-login" role="dialog" aria-modal="true" aria-labelledby="mkt-login-title" data-open="true"', false);
    }

    #[Test]
    public function a_card_holder_signs_in_at_the_vocc_url(): void
    {
        $holder = $this->cardHolder('holder@example.com', 'Holder@12345');

        $this->post('/voclogin', [
            'email' => 'holder@example.com',
            'password' => 'Holder@12345',
        ])->assertRedirect('/portal/voc-dashboard');

        $this->assertAuthenticatedAs($holder);
    }

    #[Test]
    public function a_client_member_is_turned_away_from_the_vocc_url(): void
    {
        $this->member('boss@example.com', 'Portal@12345');

        $this->from('/voclogin')
            ->post('/voclogin', [
                'email' => 'boss@example.com',
                'password' => 'Portal@12345',
            ])
            ->assertRedirect('/voclogin')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function a_holder_whose_identity_predates_the_card_is_still_admitted(): void
    {
        // VocUserProvisioner links a voc row to an email that already signs in elsewhere
        // *without* changing its type. Gating on user_type would lock this person out of the
        // very URL printed on their card.
        $user = $this->member('dual@example.com', 'Portal@12345');

        VocUser::query()->create([
            'voc_user_id' => ((int) VocUser::query()->max('voc_user_id')) + 1,
            'profile_id' => $this->profile()->id,
            'email' => 'dual@example.com',
            'password' => 'Portal@12345',
        ])->forceFill(['auth_user_id' => $user->id])->save();

        $this->assertSame(UserType::Portal, $user->fresh()->user_type);

        $this->post('/voclogin', [
            'email' => 'dual@example.com',
            'password' => 'Portal@12345',
        ])->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function the_client_login_url_is_unchanged(): void
    {
        $member = $this->member('unchanged@example.com', 'Portal@12345');

        $this->post('/portal-login', [
            'email' => 'unchanged@example.com',
            'password' => 'Portal@12345',
        ])->assertRedirect('/portal/profiles');

        $this->assertAuthenticatedAs($member);
    }

    #[Test]
    public function the_card_holder_url_still_appears_in_the_public_menu(): void
    {
        $this->get('/')->assertSuccessful()->assertSee(url('/voclogin'), false);
        $this->get('/pricing')->assertSuccessful()->assertSee(url('/voclogin'), false);
    }
}
