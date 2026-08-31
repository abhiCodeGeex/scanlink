<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocDocument;
use App\Models\VocProfileImage;
use App\Models\VocRecipient;
use App\Models\VocUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The four VOCC parity gaps found by comparing the rebuilt lifecycle against legacy.
 *
 * 1. Photos imported into voc_profile_image were never displayed — the card read only the
 *    newer `picture` table, so an imported card showed no picture of its holder.
 * 2. A card holder was not confined to their own card. Legacy bounces them out of every
 *    dashboard action; the rebuild let them into the whole portal panel.
 * 3. /voclogin redirected to the generic marketing home instead of a VOCC sign-in page.
 * 4. Expiry notices matched an exact date, so a missed scheduler run dropped that day's
 *    cohort permanently.
 */
class VoccLifecycleGapsTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::factory()->create();

        $vocType = EquipmentType::query()->firstOrCreate(['slag' => 'voc'], ['name' => 'VOCC']);

        $this->profile = Profile::factory()->create([
            'client_id' => $this->client->id,
            'type_id' => $vocType->id,
            'deleted' => false,
        ]);
    }

    /**
     * These legacy tables carry a non-incrementing primary key — the app assigns ids itself
     * (PortalProfileForm::nextLegacyId), so a test has to do the same.
     */
    protected function nextId(string $table, string $column): int
    {
        return ((int) DB::table($table)->max($column)) + 1;
    }

    /** The card holder's own login, as VocUserProvisioner creates it. */
    protected function cardHolder(): User
    {
        $vocUser = VocUser::query()->create([
            'voc_user_id' => $this->nextId('voc_users', 'voc_user_id'),
            'profile_id' => $this->profile->id,
            'email' => 'holder@example.com',
            'password' => 'Holder@12345',
        ]);
        $vocUser->refresh();

        $user = User::query()->firstOrNew(['email' => 'holder@example.com']);
        $user->name = 'Card Holder';
        $user->password = 'Holder@12345';
        $user->user_type = UserType::Voc;
        $user->save();

        $vocUser->forceFill(['auth_user_id' => $user->id])->save();

        return $user;
    }

    // ---------------------------------------------------------------- gap 1

    #[Test]
    public function a_photo_imported_from_the_legacy_table_is_shown_on_the_card(): void
    {
        VocProfileImage::query()->create([
            'client_id' => $this->client->id,
            'user_id' => 0,
            'profile_id' => $this->profile->id,
            'image_name' => 'images/picture/legacy-card-photo.jpg',
            'created_at' => now(),
            'is_temp' => false,
        ]);

        $this->profile->refresh()->loadMissing('vocProfileImages');

        $this->assertCount(1, $this->profile->vocProfileImages);
        $this->assertSame(
            'images/picture/legacy-card-photo.jpg',
            $this->profile->vocProfileImages->first()->image_name,
        );
    }

    #[Test]
    public function an_unconfirmed_legacy_upload_is_not_shown(): void
    {
        // Legacy stages an upload with is_temp=1 until the form is saved. Those must not
        // surface on the card — one of the four rows in the live import is exactly this.
        VocProfileImage::query()->create([
            'client_id' => $this->client->id,
            'user_id' => 0,
            'profile_id' => $this->profile->id,
            'image_name' => 'images/picture/never-confirmed.jpg',
            'created_at' => now(),
            'is_temp' => true,
        ]);

        $this->assertCount(0, $this->profile->refresh()->vocProfileImages);
    }

    // ---------------------------------------------------------------- gap 2

    #[Test]
    public function a_card_holder_is_sent_back_to_their_own_card(): void
    {
        $this->actingAs($this->cardHolder());

        foreach (['/portal/profiles', '/portal/purchase-codes', '/portal/team-users', '/portal/code-balance'] as $path) {
            $this->get($path)->assertRedirect('/portal/voc-dashboard');
        }
    }

    #[Test]
    public function a_card_holder_may_still_reach_their_own_pages(): void
    {
        $this->actingAs($this->cardHolder());

        $this->get('/portal/voc-dashboard')->assertSuccessful();
        $this->get('/portal/edit-voc-profile?profile='.$this->profile->id)->assertSuccessful();
    }

    #[Test]
    public function an_ordinary_portal_member_is_not_confined(): void
    {
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $this->client->id,
            'email' => 'boss@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['email' => 'boss@example.com', 'user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user);

        // Not asserting a 200: the profiles page runs raw SQL using NOW(), which sqlite has no
        // function for, so it 500s under the test database regardless of this middleware. What
        // matters here is that an ordinary member is not bounced to the VOC dashboard.
        $response = $this->get('/portal/profiles');

        $this->assertNotSame(
            url('/portal/voc-dashboard'),
            $response->headers->get('Location'),
            'An ordinary portal member must not be treated as a card holder.',
        );
    }

    // ---------------------------------------------------------------- gap 3

    #[Test]
    public function voclogin_serves_a_vocc_sign_in_page(): void
    {
        $this->get('/voclogin')
            ->assertSuccessful()
            ->assertSee('VOCC card holder login')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    #[Test]
    public function voclogin_sends_an_already_signed_in_holder_to_their_card(): void
    {
        $this->actingAs($this->cardHolder());

        $this->get('/voclogin')->assertRedirect('/portal/voc-dashboard');
    }

    // ---------------------------------------------------------------- gap 4

    protected function document(string $expiry, string $name = 'White Card'): VocDocument
    {
        return VocDocument::query()->create([
            'voc_document_id' => $this->nextId('voc_documents', 'voc_document_id'),
            'profile_id' => $this->profile->id,
            'name' => $name,
            'expiry_date' => $expiry,
            'file_name' => 'ticket.pdf',
        ]);
    }

    protected function recipient(): void
    {
        VocRecipient::query()->create([
            'voc_recipient_id' => $this->nextId('voc_recipients', 'voc_recipient_id'),
            'profile_id' => $this->profile->id,
            'email' => 'safety@example.com',
        ]);
    }

    #[Test]
    public function a_missed_run_is_caught_up_rather_than_lost(): void
    {
        Mail::fake();
        $this->recipient();

        // Expired four days ago. Under the old exact-date match this document could only ever
        // have been notified on the day itself — a scheduler outage lost it for good.
        $this->document(now()->subDays(4)->toDateString());

        $this->artisan('scanlink:send-voc-document-expiry')->assertSuccessful();

        Mail::assertSent(ScanlinkMail::class, 1);
    }

    #[Test]
    public function a_document_is_never_notified_twice(): void
    {
        Mail::fake();
        $this->recipient();
        $this->document(now()->addDays(10)->toDateString());

        $this->artisan('scanlink:send-voc-document-expiry')->assertSuccessful();
        Mail::assertSent(ScanlinkMail::class, 1);

        // Running again the next day must not mail the same reminder a second time.
        $this->artisan('scanlink:send-voc-document-expiry')->assertSuccessful();
        Mail::assertSent(ScanlinkMail::class, 1);

        $this->assertSame(1, DB::table('voc_document_notifications')->count());
    }

    #[Test]
    public function since_holds_back_historic_expiries_on_the_first_run(): void
    {
        Mail::fake();
        $this->recipient();

        $this->document(now()->subYears(2)->toDateString(), 'Ancient ticket');

        // Switching the sender on must not mail years of history at the client.
        $this->artisan('scanlink:send-voc-document-expiry', ['--since' => now()->subDays(7)->toDateString()])
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    #[Test]
    public function a_dry_run_sends_nothing_and_records_nothing(): void
    {
        Mail::fake();
        $this->recipient();
        $this->document(now()->addDays(10)->toDateString());

        $this->artisan('scanlink:send-voc-document-expiry', ['--dry-run' => true])->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertSame(0, DB::table('voc_document_notifications')->count());
    }

    #[Test]
    public function a_profile_with_no_recipients_is_left_alone(): void
    {
        Mail::fake();
        $this->document(now()->addDays(10)->toDateString());

        $this->artisan('scanlink:send-voc-document-expiry')->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertSame(0, DB::table('voc_document_notifications')->count());
    }
}
