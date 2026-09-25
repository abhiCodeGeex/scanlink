<?php

namespace Tests\Feature\Console;

use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Account-expiry warnings (legacy welcome/sendusernotification).
 *
 * This is the account clock, not the code clock: when client_users.expire_at passes, the person
 * can no longer sign in. Legacy warned twice — two months out, then one month out — and used
 * users.notice as the stage counter so neither warning repeated. The rebuild carried the column,
 * the clock and the renewal email, but never the warning, so accounts went dark unannounced.
 */
class SendAccountExpiryNoticesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_two_months_out_gets_the_first_warning(): void
    {
        Mail::fake();
        $member = $this->memberExpiringIn(70);

        $this->artisan('scanlink:send-account-expiry-notices')->assertSuccessful();

        Mail::assertSent(ScanlinkMail::class, fn (ScanlinkMail $mail): bool => $mail->hasTo($member->email)
            && $mail->subjectLine === 'ScanLink re-new account notification.');

        $this->assertSame(1, (int) $member->fresh()->notice, 'stage 1 recorded');
    }

    #[Test]
    public function running_again_the_next_day_does_not_warn_them_twice(): void
    {
        Mail::fake();
        $member = $this->memberExpiringIn(70);

        $this->artisan('scanlink:send-account-expiry-notices')->assertSuccessful();
        $this->artisan('scanlink:send-account-expiry-notices')->assertSuccessful();

        Mail::assertSentCount(1);
        $this->assertSame(1, (int) $member->fresh()->notice);
    }

    #[Test]
    public function a_month_later_the_final_warning_goes_out(): void
    {
        Mail::fake();
        $member = $this->memberExpiringIn(20, notice: 1);

        $this->artisan('scanlink:send-account-expiry-notices')->assertSuccessful();

        Mail::assertSent(ScanlinkMail::class, fn (ScanlinkMail $mail): bool => $mail->hasTo($member->email));
        $this->assertSame(2, (int) $member->fresh()->notice, 'stage 2 recorded');

        // And that is the last of it.
        Mail::fake();
        $this->artisan('scanlink:send-account-expiry-notices')->assertSuccessful();
        Mail::assertNothingSent();
    }

    #[Test]
    public function a_missed_run_does_not_cost_someone_their_warning(): void
    {
        // Never warned, and already inside the final month: legacy's exact "month == 2" test
        // would have skipped this person for good.
        Mail::fake();
        $member = $this->memberExpiringIn(20);

        $this->artisan('scanlink:send-account-expiry-notices')->assertSuccessful();

        Mail::assertSent(ScanlinkMail::class, fn (ScanlinkMail $mail): bool => $mail->hasTo($member->email));
        $this->assertSame(2, (int) $member->fresh()->notice);
    }

    #[Test]
    public function nobody_is_warned_too_early_or_after_the_fact(): void
    {
        Mail::fake();

        $early = $this->memberExpiringIn(200);
        $gone = $this->memberExpiringIn(-5);

        $this->artisan('scanlink:send-account-expiry-notices')->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertSame(0, (int) $early->fresh()->notice);
        $this->assertSame(0, (int) $gone->fresh()->notice);
    }

    #[Test]
    public function a_dry_run_sends_nothing_and_records_nothing(): void
    {
        Mail::fake();
        $member = $this->memberExpiringIn(70);

        $this->artisan('scanlink:send-account-expiry-notices', ['--dry-run' => true])->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertSame(0, (int) $member->fresh()->notice);
    }

    #[Test]
    public function the_warning_names_the_date_the_account_expires(): void
    {
        Mail::fake();
        $member = $this->memberExpiringIn(70);
        $expected = $member->expire_at->format('d/m/Y');

        $this->artisan('scanlink:send-account-expiry-notices')->assertSuccessful();

        Mail::assertSent(ScanlinkMail::class, function (ScanlinkMail $mail) use ($expected): bool {
            return str_contains($mail->render(), $expected)
                && str_contains($mail->render(), 'going to expire');
        });
    }

    private function memberExpiringIn(int $days, int $notice = 0): ClientUser
    {
        static $n = 0;
        $n++;

        return ClientUser::factory()->primary()->create([
            'client_id' => Client::factory()->create()->id,
            'email' => 'account-expiry-'.$n.'@yopmail.com',
            'first_name' => 'Pat',
            'last_name' => 'Holder',
            'status' => true,
            'expire_at' => now()->addDays($days),
            'notice' => $notice,
        ]);
    }
}
