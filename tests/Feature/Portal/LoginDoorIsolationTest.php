<?php

namespace Tests\Feature\Portal;

use App\Enums\AdminRole;
use App\Enums\UserType;
use App\Filament\Auth\AdminLogin;
use App\Http\Middleware\ConfineVocCardHolder;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocUser;
use App\Support\LoginEntrance;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Each login door leads to its own area and no further:
 *
 *  - the admin login admits admin accounts, and only they reach the admin panel;
 *  - the client login gives a client member their client's portal;
 *  - the VOCC login gives a card holder their own card.
 *
 * Two gaps are closed here. The admin panel still let a client member with "Enable Admin
 * Access" in (legacy's flag for full rights inside their own client, not over ScanLink). And
 * the VOC dashboard picked its mode from user_type rather than the door, so a card holder who
 * is also a primary client member, signing in at /voclogin, saw every VOC card the client owns.
 *
 * One person may hold several of those roles under a single login. The door they sign in at
 * decides which one they are acting in for that session: an admin who also holds a card is
 * only a card holder after /voclogin, and only an admin after the admin login. user_type can
 * name just one role, which used to turn such a person away from every door but one.
 */
class LoginDoorIsolationTest extends TestCase
{
    use RefreshDatabase;

    private const MULTI_ROLE_PASSWORD = 'Multi@12345';

    #[Test]
    public function an_admin_who_holds_a_card_signs_in_at_the_vocc_url_as_a_card_holder_only(): void
    {
        $person = $this->adminHoldingACard('abc@example.com');

        $this->post('/voclogin', ['email' => 'abc@example.com', 'password' => self::MULTI_ROLE_PASSWORD])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/portal/voc-dashboard');

        $this->assertSame(LoginEntrance::VOC, session(LoginEntrance::SESSION_KEY));
        $this->assertFalse($person->fresh()->canAccessPanel(Filament::getPanel('admin')), 'not an admin in this session');

        $this->get('/portal/voc-dashboard')->assertSuccessful()->assertSee('ABC-OWN-CARD');
        $this->get('/admin')->assertForbidden();
    }

    #[Test]
    public function the_same_person_signs_in_at_the_admin_login_as_an_admin_only(): void
    {
        $person = $this->adminHoldingACard('abc@example.com');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(AdminLogin::class)
            ->set('data.email', 'abc@example.com')
            ->set('data.password', self::MULTI_ROLE_PASSWORD)
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticatedAs($person);
        $this->assertSame(LoginEntrance::ADMIN, session(LoginEntrance::SESSION_KEY));
        $this->assertTrue($person->fresh()->canAccessPanel(Filament::getPanel('admin')));
        $this->assertFalse($person->fresh()->canAccessPanel(Filament::getPanel('portal')), 'not a card holder in this session');
    }

    #[Test]
    public function an_admin_who_is_a_client_member_uses_the_client_login_for_their_client(): void
    {
        $person = $this->adminAccount('ops@example.com');
        $this->clientMembershipFor('ops@example.com');

        $this->post('/portal-login', ['email' => 'ops@example.com', 'password' => self::MULTI_ROLE_PASSWORD])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/portal/profiles');

        $this->assertSame(LoginEntrance::CLIENT, session(LoginEntrance::SESSION_KEY));
        $this->get('/admin')->assertForbidden();
        $this->assertTrue($person->fresh()->holdsAdminRole(), 'still an admin — just not in this session');
    }

    #[Test]
    public function saving_a_client_membership_does_not_strip_an_admins_role(): void
    {
        $person = $this->adminAccount('ops@example.com');

        $this->clientMembershipFor('ops@example.com');

        $person->refresh();
        $this->assertSame(UserType::Admin, $person->user_type);
        $this->assertSame(AdminRole::Support, $person->admin_role);
    }

    #[Test]
    public function an_admin_with_no_client_role_is_turned_away_from_the_client_login(): void
    {
        $this->adminAccount('ops@example.com');

        $this->post('/portal-login', ['email' => 'ops@example.com', 'password' => self::MULTI_ROLE_PASSWORD])
            ->assertSessionHasErrors(['email' => 'This account cannot access the client portal.']);

        $this->assertGuest();
    }

    private function adminAccount(string $email): User
    {
        return User::factory()->create([
            'email' => $email,
            'password' => self::MULTI_ROLE_PASSWORD,
            'user_type' => UserType::Admin,
            'admin_role' => AdminRole::Support,
        ]);
    }

    /** An admin whose email is also a VOCC card holder, linked the way VocUserProvisioner links it. */
    private function adminHoldingACard(string $email): User
    {
        $admin = $this->adminAccount($email);

        VocUser::query()->create([
            'voc_user_id' => ((int) VocUser::query()->max('voc_user_id')) + 1,
            'profile_id' => $this->vocProfile(Client::factory()->create(), 'ABC-OWN-CARD')->id,
            'email' => $email,
            'password' => self::MULTI_ROLE_PASSWORD,
        ]);

        $admin->refresh();
        $this->assertSame(UserType::Admin, $admin->user_type, 'provisioning must link the card, not relabel the admin');
        $this->assertTrue($admin->hasVocCardHolderAccess());

        return $admin;
    }

    private function clientMembershipFor(string $email): ClientUser
    {
        return ClientUser::factory()->primary()->create([
            'client_id' => Client::factory()->create()->id,
            'email' => $email,
            'password' => self::MULTI_ROLE_PASSWORD,
            'status' => true,
            'is_password_change' => true,
        ]);
    }

    #[Test]
    public function the_vocc_door_shows_a_dual_identity_only_their_own_card(): void
    {
        [$user] = $this->dualIdentityWithTwoCards();

        $this->withSession([ConfineVocCardHolder::SESSION_KEY => true])
            ->actingAs($user)
            ->get('/portal/voc-dashboard')
            ->assertSuccessful()
            ->assertSee('HOLDERS-OWN-CARD')
            ->assertDontSee('CLIENT-OWNED-CARD');
    }

    #[Test]
    public function the_client_door_shows_the_same_person_their_clients_voc_cards(): void
    {
        [$user] = $this->dualIdentityWithTwoCards();

        $this->actingAs($user)
            ->get('/portal/voc-dashboard')
            ->assertSuccessful()
            ->assertSee('CLIENT-OWNED-CARD');
    }

    #[Test]
    public function a_client_member_with_enable_admin_access_cannot_enter_the_admin_panel(): void
    {
        // An @scanlink.com address is the exact shape the old fallback admitted.
        $member = $this->member('staff@scanlink.com', enableAdminAccess: true);

        $this->assertFalse($member->canAccessPanel(Filament::getPanel('admin')));
        $this->assertTrue($member->canAccessPanel(Filament::getPanel('portal')), 'their client area is untouched');

        $this->actingAs($member)->get('/admin')->assertForbidden();
    }

    #[Test]
    public function a_card_holder_cannot_enter_the_admin_panel(): void
    {
        $holder = $this->cardHolderOn($this->vocProfile(Client::factory()->create(), 'ANY'), 'holder@scanlink.com');

        $this->assertFalse($holder->canAccessPanel(Filament::getPanel('admin')));
    }

    #[Test]
    public function an_admin_account_reaches_the_admin_panel_and_not_the_client_portal(): void
    {
        $admin = User::factory()->create([
            'email' => 'ops@example.com',
            'user_type' => UserType::Admin,
            'admin_role' => AdminRole::Support,
        ]);

        $this->assertTrue($admin->canAccessPanel(Filament::getPanel('admin')));
        $this->assertFalse($admin->canAccessPanel(Filament::getPanel('portal')));
    }

    /**
     * A primary client member whose client owns a VOC card, who also holds a card on another
     * client's profile.
     *
     * @return array{0: User}
     */
    private function dualIdentityWithTwoCards(): array
    {
        $user = $this->member('dual@example.com');
        $clientId = (int) $user->clientMemberships()->value('client_id');

        $this->vocProfile(Client::query()->findOrFail($clientId), 'CLIENT-OWNED-CARD');

        $ownCard = $this->vocProfile(Client::factory()->create(), 'HOLDERS-OWN-CARD');
        VocUser::query()->create([
            'voc_user_id' => ((int) VocUser::query()->max('voc_user_id')) + 1,
            'profile_id' => $ownCard->id,
            'email' => 'dual@example.com',
            'password' => 'Portal@12345',
        ])->forceFill(['auth_user_id' => $user->id])->save();

        return [$user->refresh()];
    }

    private function member(string $email, bool $enableAdminAccess = false): User
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => $email,
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
            'enable_admin_access' => $enableAdminAccess,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        return $user->refresh();
    }

    private function vocProfile(Client $client, string $name): Profile
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;

        return Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $typeId,
            'code_profile_name' => $name,
            'name' => $name,
            'deleted' => 0,
            'update_or_not' => true,
        ]);
    }

    private function cardHolderOn(Profile $profile, string $email): User
    {
        $vocUser = VocUser::query()->create([
            'voc_user_id' => ((int) VocUser::query()->max('voc_user_id')) + 1,
            'profile_id' => $profile->id,
            'email' => $email,
            'password' => 'Holder@12345',
        ]);

        return User::query()->findOrFail($vocUser->fresh()->auth_user_id);
    }
}
