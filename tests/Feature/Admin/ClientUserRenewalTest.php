<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Clients\Pages\ManageClientUsers;
use App\Mail\AccountRenewalNotification;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * An admin can put an account's expiry right.
 *
 * Legacy siteadmin had both halves: "Re-new" added a year from the current expiry, or from today
 * when it had already passed (user.php:147-165), and the date itself could be typed in
 * (user.php:114-119). Either way the user was emailed and the warning counter reset. The rebuild
 * showed expire_at as a read-only column and had no way to change it, so an expired account could
 * only be rescued with a SQL update.
 */
class ClientUserRenewalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function renewing_adds_a_year_to_a_live_account(): void
    {
        Mail::fake();
        [$client, $member] = $this->clientWithUser(expireAt: now()->addMonths(2));
        $expected = $member->expire_at->copy()->addYear();

        $this->renew($client, $member);

        $this->assertSame($expected->toDateString(), $member->fresh()->expire_at->toDateString());
    }

    #[Test]
    public function renewing_an_expired_account_starts_the_year_from_today(): void
    {
        Mail::fake();
        [$client, $member] = $this->clientWithUser(expireAt: now()->subMonths(5));

        $this->renew($client, $member);

        $this->assertSame(
            now()->addYear()->toDateString(),
            $member->fresh()->expire_at->toDateString(),
            'no credit for the time the account spent expired',
        );
    }

    #[Test]
    public function renewing_emails_the_user_and_clears_the_warning_counter(): void
    {
        Mail::fake();
        [$client, $member] = $this->clientWithUser(expireAt: now()->addMonth(), notice: 2);

        $this->renew($client, $member);

        Mail::assertSent(AccountRenewalNotification::class, fn ($mail): bool => $mail->hasTo($member->email));
        $this->assertSame(0, (int) $member->fresh()->notice, 'the warnings start again from the new date');
    }

    #[Test]
    public function the_expiry_date_can_be_set_by_hand(): void
    {
        Mail::fake();
        [$client, $member] = $this->clientWithUser(expireAt: now()->addMonth(), notice: 1);
        $newDate = now()->addYears(2)->startOfDay();

        $this->actingAs($this->admin());

        Livewire::test(ManageClientUsers::class, ['record' => $client->getKey()])
            ->callTableAction('setExpiry', $member, ['expire_at' => $newDate->format('Y-m-d H:i:s')])
            ->assertHasNoTableActionErrors();

        $this->assertSame($newDate->toDateString(), $member->fresh()->expire_at->toDateString());
        $this->assertSame(0, (int) $member->fresh()->notice);
        Mail::assertSent(AccountRenewalNotification::class);
    }

    private function renew(Client $client, ClientUser $member): void
    {
        $this->actingAs($this->admin());

        Livewire::test(ManageClientUsers::class, ['record' => $client->getKey()])
            ->callTableAction('renewAccount', $member)
            ->assertHasNoTableActionErrors();
    }

    /**
     * @return array{0: Client, 1: ClientUser}
     */
    private function clientWithUser(Carbon $expireAt, int $notice = 0): array
    {
        $client = Client::factory()->create(['email' => 'renewal-co@yopmail.com']);

        $member = ClientUser::factory()->subUser()->create([
            'client_id' => $client->id,
            'email' => 'renewal-user@yopmail.com',
            'status' => true,
            'expire_at' => $expireAt,
            'notice' => $notice,
        ]);

        return [$client, $member];
    }

    private function admin(): User
    {
        return User::factory()->create([
            'email' => 'renewal-admin@yopmail.com',
            'name' => 'ScanLink Admin',
        ]);
    }
}
