<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Blocking a client actually shuts the account.
 *
 * Legacy checked clients.approve before anything else on the client login (welcome.php:265) and
 * refused the whole company. The rebuild kept the Block button and the column, but no login path
 * read it: a blocked client and every user under it went on signing in as though nothing had
 * happened.
 */
class BlockedClientLoginTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'Blocked@12345';

    #[Test]
    public function the_owner_of_a_blocked_client_cannot_sign_in(): void
    {
        $client = Client::factory()->create(['approve' => false]);
        $this->memberOf($client, 'blocked-owner@yopmail.com');

        $this->post('/portal-login', ['email' => 'blocked-owner@yopmail.com', 'password' => self::PASSWORD])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function a_sub_user_of_a_blocked_client_cannot_sign_in_either(): void
    {
        $client = Client::factory()->create(['approve' => false]);
        $this->memberOf($client, 'blocked-sub@yopmail.com', subUser: true);

        $this->post('/portal-login', ['email' => 'blocked-sub@yopmail.com', 'password' => self::PASSWORD])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function the_message_is_the_one_legacy_gave(): void
    {
        $client = Client::factory()->create(['approve' => false]);
        $this->memberOf($client, 'blocked-message@yopmail.com');

        $this->post('/portal-login', ['email' => 'blocked-message@yopmail.com', 'password' => self::PASSWORD])
            ->assertSessionHasErrors(['email' => 'Your account is blocked, please contact admin to un-block account.']);
    }

    #[Test]
    public function an_approved_client_is_unaffected(): void
    {
        $client = Client::factory()->create(['approve' => true]);
        $user = $this->memberOf($client, 'approved-owner@yopmail.com');

        $this->post('/portal-login', ['email' => 'approved-owner@yopmail.com', 'password' => self::PASSWORD])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/portal/profiles');

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_block_on_one_client_does_not_shut_a_person_out_of_another(): void
    {
        $blocked = Client::factory()->create(['approve' => false]);
        $approved = Client::factory()->create(['approve' => true]);

        $user = $this->memberOf($blocked, 'two-clients@yopmail.com');

        // The same login, a second membership. (The test schema keeps client_users.email
        // unique, so the row carries a distinct address; what ties it to the person is
        // auth_user_id, which is what the check reads.)
        ClientUser::factory()->primary()->create([
            'client_id' => $approved->id,
            'auth_user_id' => $user->id,
            'email' => 'two-clients-second@yopmail.com',
            'password' => self::PASSWORD,
            'status' => true,
            'is_password_change' => true,
        ]);

        // Saving a membership syncs the login's own address to it; live rows carry the same
        // address on both, so put it back to the one typed at the login.
        User::query()->whereKey($user->id)->update(['email' => 'two-clients@yopmail.com']);

        $this->post('/portal-login', ['email' => 'two-clients@yopmail.com', 'password' => self::PASSWORD])
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user->fresh());
    }

    private function memberOf(Client $client, string $email, bool $subUser = false): User
    {
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => $email,
            'password' => self::PASSWORD,
            'status' => true,
            'is_password_change' => true,
            'is_sub_user' => $subUser,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => $email,
            'password' => self::PASSWORD,
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        return $user->refresh();
    }
}
