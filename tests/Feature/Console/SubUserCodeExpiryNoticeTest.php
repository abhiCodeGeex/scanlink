<?php

namespace Tests\Feature\Console;

use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A code expiry warning goes to whoever owns the code, sub-user or not.
 *
 * Legacy's profileexpiry task mailed the code's owner with no test beyond the expiry date. The
 * rebuild skipped a sub-user whose `notice` was 0 — but `notice` is the account-renewal stage
 * counter and it is 0 on nearly every imported sub-user, so codes owned by sub-users expired in
 * silence.
 */
class SubUserCodeExpiryNoticeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_sub_users_code_is_warned_about_like_anyone_elses(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        $subUser = ClientUser::factory()->subUser()->create([
            'client_id' => $client->id,
            'email' => 'code-owner-sub@yopmail.com',
            'notice' => 0,
        ]);

        $this->codeExpiringIn(30, $client, $subUser);

        $this->artisan('scanlink:send-expiry-notifications')->assertSuccessful();

        Mail::assertSent(ScanlinkMail::class, fn (ScanlinkMail $mail): bool => $mail->hasTo('code-owner-sub@yopmail.com'));
    }

    #[Test]
    public function the_primary_users_codes_are_unaffected(): void
    {
        Mail::fake();

        $client = Client::factory()->create();
        $owner = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'code-owner-primary@yopmail.com',
        ]);

        $this->codeExpiringIn(3, $client, $owner);

        $this->artisan('scanlink:send-expiry-notifications')->assertSuccessful();

        Mail::assertSent(ScanlinkMail::class, fn (ScanlinkMail $mail): bool => $mail->hasTo('code-owner-primary@yopmail.com'));
    }

    private function codeExpiringIn(int $days, Client $client, ClientUser $member): Profile
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'location'], ['name' => 'Location'])->id;

        return Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'name' => 'Expiring code',
            'code_profile_name' => 'Expiring code',
            'deleted' => 0,
            'update_or_not' => true,
            'free_code' => false,
            'expired_at' => now()->addDays($days),
        ]);
    }
}
