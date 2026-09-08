<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\PurchaseOrderSummary;
use App\Filament\Portal\Pages\RenewCodeSummary;
use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * One transaction, one message per recipient.
 *
 * A purchase made with a reseller code used to send the admin two "Scanlink Purchase Code"
 * emails — one describing the buyer, one describing the reseller. A renewal was worse: one for
 * the member's own codes plus another for every reseller group in the selection.
 */
class PurchaseRenewEmailFanoutTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected ClientUser $member;

    /** Count messages actually addressed to a given recipient. */
    protected function sentTo(string $email, ?string $subject = null): int
    {
        $count = 0;

        Mail::assertSent(ScanlinkMail::class, function (ScanlinkMail $mail) use ($email, $subject, &$count): bool {
            $matchesSubject = $subject === null || $mail->subjectLine === $subject;

            if ($matchesSubject && $mail->hasTo($email)) {
                $count++;
            }

            return true;
        });

        return $count;
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['scanlink.admin_email' => 'admin@scanlink.test']);

        $this->client = Client::factory()->create(['email' => 'buyer@example.com']);

        $this->member = ClientUser::factory()->primary()->create([
            'client_id' => $this->client->id,
            'email' => 'buyer@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $this->member->refresh();

        $user = User::query()->findOrFail($this->member->auth_user_id);
        $user->update([
            'email' => 'buyer@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($user);
    }

    protected function reseller(string $email = 'reseller@example.com'): Client
    {
        return Client::factory()->create([
            'client_name' => 'Reseller Co',
            'reseller_email' => $email,
            'email' => $email,
        ]);
    }

    /** @param array<string, mixed> $checkout */
    protected function dispatchPurchase(array $checkout): void
    {
        $page = new PurchaseOrderSummary;

        $method = new \ReflectionMethod($page, 'dispatchPurchaseEmails');
        $method->invoke(
            $page,
            (object) ['id' => 1, 'total_amount' => 120.0],
            ['email' => 'buyer@example.com', 'first_name' => 'Bea', 'last_name' => 'Buyer'],
            $checkout,
            2,
            5.0,
        );
    }

    #[Test]
    public function a_plain_purchase_emails_the_buyer_once_and_the_admin_once(): void
    {
        Mail::fake();

        $this->dispatchPurchase(['is_reseller_pricing_code' => '0', 'reseller_client_id' => 0]);

        $this->assertSame(1, $this->sentTo('buyer@example.com'));
        $this->assertSame(1, $this->sentTo('admin@scanlink.test'));
    }

    #[Test]
    public function a_reseller_purchase_still_emails_the_admin_only_once(): void
    {
        Mail::fake();
        $reseller = $this->reseller();

        $this->dispatchPurchase([
            'is_reseller_pricing_code' => '1',
            'reseller_client_id' => $reseller->id,
        ]);

        // The reported bug: this was 2.
        $this->assertSame(1, $this->sentTo('admin@scanlink.test', 'Scanlink Purchase Code'));
        $this->assertSame(1, $this->sentTo('buyer@example.com'));
        $this->assertSame(1, $this->sentTo('reseller@example.com'));
    }

    #[Test]
    public function buying_with_your_own_reseller_code_does_not_double_the_confirmation(): void
    {
        Mail::fake();
        $reseller = $this->reseller('buyer@example.com');

        $this->dispatchPurchase([
            'is_reseller_pricing_code' => '1',
            'reseller_client_id' => $reseller->id,
        ]);

        $this->assertSame(1, $this->sentTo('buyer@example.com'), 'one person, one confirmation');
        $this->assertSame(1, $this->sentTo('admin@scanlink.test'));
    }

    protected function profile(?Client $reseller = null): Profile
    {
        $type = EquipmentType::query()->firstOrCreate(['slag' => 'location'], ['name' => 'Location']);

        $p = Profile::factory()->create([
            'client_id' => $this->client->id,
            'type_id' => $type->id,
            'deleted' => false,
            'expired_at' => now()->addDays(5),
        ]);

        Profile::query()->whereKey($p->id)->update([
            'is_reseller_code' => $reseller ? '1' : '0',
            'reseller_client_id' => $reseller?->id ?? 0,
        ]);

        return $p->refresh();
    }

    #[Test]
    public function a_renewal_spanning_two_resellers_emails_the_admin_only_once(): void
    {
        Mail::fake();

        $first = $this->reseller('r1@example.com');
        $second = $this->reseller('r2@example.com');

        $ordered = collect([
            $this->profile(),          // own code
            $this->profile($first),
            $this->profile($second),
        ]);

        $page = new RenewCodeSummary;
        (new \ReflectionMethod($page, 'dispatchRenewalEmails'))->invoke(
            $page,
            (object) ['id' => 7, 'total_amount' => 360.0],
            $ordered,
            $this->member,
            $this->client,
        );

        // Was 3: one for the own codes and one per reseller group.
        $this->assertSame(1, $this->sentTo('admin@scanlink.test', 'Scanlink Renew Code'));

        // Each distinct party still hears about it exactly once.
        $this->assertSame(1, $this->sentTo('buyer@example.com'));
        $this->assertSame(1, $this->sentTo('r1@example.com'));
        $this->assertSame(1, $this->sentTo('r2@example.com'));
    }

    #[Test]
    public function renewing_your_own_reseller_codes_does_not_double_the_confirmation(): void
    {
        Mail::fake();

        $self = $this->reseller('buyer@example.com');
        $ordered = collect([$this->profile(), $this->profile($self)]);

        $page = new RenewCodeSummary;
        (new \ReflectionMethod($page, 'dispatchRenewalEmails'))->invoke(
            $page,
            (object) ['id' => 8, 'total_amount' => 240.0],
            $ordered,
            $this->member,
            $this->client,
        );

        $this->assertSame(1, $this->sentTo('buyer@example.com'), 'one person, one confirmation');
        $this->assertSame(1, $this->sentTo('admin@scanlink.test', 'Scanlink Renew Code'));
    }
}
