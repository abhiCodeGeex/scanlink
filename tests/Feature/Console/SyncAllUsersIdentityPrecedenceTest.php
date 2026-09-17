<?php

namespace Tests\Feature\Console;

use App\Console\Commands\SyncAllUsers;
use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

/**
 * One email is one login. When a client member is also a VOCC card holder, the voc pass of
 * scanlink:sync-all-users used to relabel their login `voc` — only admins were protected — so
 * the live accounts admin@nwit.com.au, sales@nwit.com.au and support@nwit.com.au came out of
 * the import as card holders.
 *
 * The passes run in the command's own order (portal, then voc). They are invoked directly
 * because handle() first runs MySQL-only session SQL the SQLite test database cannot execute.
 */
class SyncAllUsersIdentityPrecedenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_member_who_is_also_a_card_holder_stays_a_client_member(): void
    {
        [$user, $profile] = $this->clientMember('admin@nwit.com.au');
        $passwordBefore = $user->getRawOriginal('password');
        $vocRowId = $this->cardHolderRow($profile, 'admin@nwit.com.au');

        $this->runPortalThenVocPasses();

        $user->refresh();
        $this->assertSame(UserType::Portal, $user->user_type);
        $this->assertSame($passwordBefore, $user->getRawOriginal('password'), 'the voc pass must not touch the login');

        // Still linked, so the card holder door keeps working for the same person.
        $this->assertSame($user->id, (int) DB::table('voc_users')->where('voc_user_id', $vocRowId)->value('auth_user_id'));
    }

    public function test_a_login_already_mislabelled_voc_is_restored_on_the_next_sync(): void
    {
        [$user, $profile] = $this->clientMember('sales@nwit.com.au');
        $this->cardHolderRow($profile, 'sales@nwit.com.au');

        // The shape the earlier import left in the live data.
        DB::table('users')->where('id', $user->id)->update(['user_type' => UserType::Voc->value]);

        $this->runPortalThenVocPasses();

        $this->assertSame(UserType::Portal, $user->refresh()->user_type);
    }

    public function test_an_email_only_in_voc_users_is_still_a_card_holder(): void
    {
        [, $profile] = $this->clientMember('owner@example.com');
        $this->cardHolderRow($profile, 'cardholder-only@example.com');

        $this->runPortalThenVocPasses();

        $this->assertSame(
            UserType::Voc,
            User::query()->where('email', 'cardholder-only@example.com')->firstOrFail()->user_type,
        );
    }

    /**
     * @return array{0: User, 1: Profile}
     */
    private function clientMember(string $email): array
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => $email,
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
        ]);

        return [$user->refresh(), $profile];
    }

    /**
     * Inserted raw: the VocUser model observer provisions at runtime, and this test is about
     * the batch sync, not that path.
     */
    private function cardHolderRow(Profile $profile, string $email): int
    {
        $id = (int) (DB::table('voc_users')->max('voc_user_id') ?? 0) + 1;

        DB::table('voc_users')->insert([
            'voc_user_id' => $id,
            'profile_id' => $profile->id,
            'email' => $email,
            'password' => 'card-holder-password',
            'auth_user_id' => null,
        ]);

        return $id;
    }

    private function runPortalThenVocPasses(): void
    {
        $command = app(SyncAllUsers::class);
        $command->setOutput(new OutputStyle(new ArrayInput([]), new BufferedOutput()));

        foreach (['syncPortalUsers', 'syncVocUsers'] as $pass) {
            (new \ReflectionMethod($command, $pass))->invoke($command, null);
        }
    }
}
