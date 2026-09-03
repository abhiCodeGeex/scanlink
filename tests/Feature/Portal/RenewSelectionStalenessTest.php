<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\CodeBalance;
use App\Filament\Portal\Pages\RenewCodeSummary;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Order;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Code Balance → Renew Selected staged its quote into one shared session key with nothing
 * tying the quote to the page that displayed it. Selecting 2 codes, going Back, selecting 1,
 * then pressing proceed showed — and would have invoiced — the earlier selection of 2.
 *
 * Each staging now carries a one-time token in the URL, and the summary refuses any quote
 * that is not the one it was opened for.
 */
class RenewSelectionStalenessTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::factory()->create();

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $this->client->id,
            'email' => 'renewer@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $this->user = User::query()->findOrFail($member->auth_user_id);
        $this->user->update([
            'email' => 'renewer@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($this->user);
    }

    protected function renewableProfile(): Profile
    {
        $type = EquipmentType::query()->firstOrCreate(['slag' => 'location'], ['name' => 'Location']);

        return Profile::factory()->create([
            'client_id' => $this->client->id,
            'type_id' => $type->id,
            'deleted' => false,
            'expired_at' => now()->addDays(10),
        ]);
    }

    /** The token stageRenew() put in the returned URL. */
    protected function tokenFrom(string $url): string
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $q);

        return (string) ($q['t'] ?? '');
    }

    #[Test]
    public function staging_returns_a_url_carrying_a_token(): void
    {
        $url = RenewCodeSummary::stageRenew([$this->renewableProfile()]);

        $this->assertNotSame('', $this->tokenFrom($url), 'the summary URL must carry a token');
        $this->assertSame(
            $this->tokenFrom($url),
            session(RenewCodeSummary::SESSION_RENEW)['token'] ?? null,
        );
    }

    #[Test]
    public function the_summary_shows_the_quote_it_was_opened_for(): void
    {
        $a = $this->renewableProfile();
        $b = $this->renewableProfile();

        $url = RenewCodeSummary::stageRenew([$a, $b]);

        Livewire::withQueryParams(['t' => $this->tokenFrom($url)])
            ->test(RenewCodeSummary::class)
            ->assertSet('token', $this->tokenFrom($url))
            ->assertOk();

        $this->assertSame(2, session(RenewCodeSummary::SESSION_RENEW)['quantity']);
    }

    /** The reported sequence: stage 2, go back, stage 1 — the first URL must die. */
    #[Test]
    public function re_staging_invalidates_the_previous_summary_url(): void
    {
        $a = $this->renewableProfile();
        $b = $this->renewableProfile();
        $c = $this->renewableProfile();

        $twoCodeUrl = RenewCodeSummary::stageRenew([$a, $b]);
        $twoCodeToken = $this->tokenFrom($twoCodeUrl);

        // ...back to Code Balance, select one code instead.
        $oneCodeUrl = RenewCodeSummary::stageRenew([$c]);
        $oneCodeToken = $this->tokenFrom($oneCodeUrl);

        $this->assertNotSame($twoCodeToken, $oneCodeToken);

        // The new page shows one code.
        Livewire::withQueryParams(['t' => $oneCodeToken])
            ->test(RenewCodeSummary::class)
            ->assertSet('token', $oneCodeToken);

        $this->assertSame(1, session(RenewCodeSummary::SESSION_RENEW)['quantity']);
        $this->assertSame([$c->id], session(RenewCodeSummary::SESSION_RENEW)['profile_ids']);

        // Re-opening the superseded URL must not present the old two-code quote.
        Livewire::withQueryParams(['t' => $twoCodeToken])
            ->test(RenewCodeSummary::class)
            ->assertRedirect();
    }

    #[Test]
    public function a_summary_opened_without_a_token_is_refused(): void
    {
        RenewCodeSummary::stageRenew([$this->renewableProfile()]);

        Livewire::test(RenewCodeSummary::class)->assertRedirect();
    }

    #[Test]
    public function declining_the_terms_neither_renews_nor_changes_the_quote(): void
    {
        $a = $this->renewableProfile();
        $b = $this->renewableProfile();

        $url = RenewCodeSummary::stageRenew([$a, $b]);
        $before = session(RenewCodeSummary::SESSION_RENEW);

        Livewire::withQueryParams(['t' => $this->tokenFrom($url)])
            ->test(RenewCodeSummary::class)
            ->set('agreeTerms', false)
            ->call('proceed');

        $this->assertSame($before, session(RenewCodeSummary::SESSION_RENEW), 'the staged quote must be untouched');
        $this->assertSame(0, Order::query()->count(), 'nothing may be ordered without agreeing');
    }

    #[Test]
    public function the_summary_names_the_codes_being_renewed(): void
    {
        $a = $this->renewableProfile();
        $b = $this->renewableProfile();

        $url = RenewCodeSummary::stageRenew([$a, $b]);

        $page = Livewire::withQueryParams(['t' => $this->tokenFrom($url)])
            ->test(RenewCodeSummary::class);

        $this->assertSame([$a->id, $b->id], $page->instance()->profileNumbers());
        $page->assertSee('#'.$a->id)->assertSee('#'.$b->id);
    }

    /**
     * Fix A. Asserted on the component directly rather than through Livewire::test(), because
     * rendering this page needs a current Filament panel that the test harness has not set —
     * and the behaviour under test is mount(), not the view.
     */
    #[Test]
    public function code_balance_opens_with_nothing_selected(): void
    {
        $page = new CodeBalance;
        $page->selected = [1, 2, 3];

        $page->mount();

        $this->assertSame([], $page->selected, 'a fresh visit must not inherit a previous selection');
    }
}
