<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\VocDashboard;
use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocDocument;
use App\Models\VocUser;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The card holder's dashboard list.
 *
 * Legacy asked its listing endpoint for `deleted: 0` and `type_id: 12`; following the voc_users
 * row on its own showed a deleted card, and whatever non-VOCC profile a row happened to point
 * at. It also printed the code expiry date, coloured the row by it, and flagged profiles whose
 * documents had expired or were about to.
 */
class VocDashboardListTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::factory()->create();
    }

    protected function typeId(string $slag, string $name): int
    {
        return (int) EquipmentType::query()->updateOrCreate(['slag' => $slag], ['name' => $name])->id;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function profile(array $attributes = [], ?string $slag = 'voc'): Profile
    {
        // Legacy columns the factory does not set, applied after the insert. `renewal_required`
        // is in the live schema but not in the migrations, so it is absent under sqlite - the
        // same reason Profile::isExpiryManaged() reads it with a null fallback.
        $legacy = [
            'free_code' => $attributes['free_code'] ?? 0,
            'renewal_required' => $attributes['renewal_required'] ?? 0,
        ];

        unset($attributes['free_code'], $attributes['renewal_required']);

        $profile = Profile::factory()->create(array_merge([
            'client_id' => $this->client->id,
            'type_id' => $this->typeId($slag ?? 'voc', strtoupper($slag ?? 'voc')),
            'deleted' => 0,
            'update_or_not' => true,
        ], $attributes));

        $update = array_filter(
            $legacy,
            fn (string $column): bool => Schema::hasColumn('profiles', $column),
            ARRAY_FILTER_USE_KEY,
        );

        Profile::query()->whereKey($profile->id)->update($update);

        return $profile->refresh();
    }

    /** Link a profile to one card holder login and return the auth identity. */
    protected function holderFor(Profile ...$profiles): User
    {
        $user = null;

        foreach ($profiles as $profile) {
            $vocUser = VocUser::query()->create([
                'voc_user_id' => ((int) VocUser::query()->max('voc_user_id')) + 1,
                'profile_id' => $profile->id,
                'email' => 'holder@example.com',
                'password' => 'Holder@12345',
            ]);

            $user ??= User::query()->findOrFail($vocUser->fresh()->auth_user_id);
        }

        $user->update(['user_type' => UserType::Voc]);

        return $user->refresh();
    }

    protected function document(Profile $profile, ?string $expiry, string $name = 'White Card'): VocDocument
    {
        $id = ((int) DB::table('voc_documents')->max('voc_document_id')) + 1;

        return VocDocument::query()->create([
            'voc_document_id' => $id,
            'profile_id' => $profile->id,
            'name' => $name,
            'expiry_date' => $expiry,
            'file_name' => 'docs/'.$id.'.pdf',
        ]);
    }

    /** @return \Livewire\Features\SupportTesting\Testable */
    protected function dashboard(User $holder)
    {
        $this->actingAs($holder);

        // Match a real portal request: without this the current panel is admin, and the page
        // builds its own URLs against whatever panel is current.
        Filament::setCurrentPanel('portal');

        return Livewire::test(VocDashboard::class);
    }

    // ------------------------------------------------------------------ the two loader bugs

    #[Test]
    public function a_deleted_profile_is_not_listed(): void
    {
        $live = $this->profile(['code_profile_name' => 'Live card']);
        $gone = $this->profile(['code_profile_name' => 'Deleted card']);
        $holder = $this->holderFor($live, $gone);

        Profile::query()->whereKey($gone->id)->update(['deleted' => 1]);

        $ids = $this->dashboard($holder)->get('profiles')->pluck('id');

        $this->assertTrue($ids->contains($live->id));
        $this->assertFalse($ids->contains($gone->id), 'a deleted card must not come back');
    }

    #[Test]
    public function a_profile_that_is_not_a_vocc_code_is_not_listed(): void
    {
        $voc = $this->profile(['code_profile_name' => 'VOCC card']);
        $other = $this->profile(['code_profile_name' => 'A location'], slag: 'location');
        $holder = $this->holderFor($voc, $other);

        $ids = $this->dashboard($holder)->get('profiles')->pluck('id');

        $this->assertTrue($ids->contains($voc->id));
        $this->assertFalse($ids->contains($other->id), 'legacy pinned type_id 12');
    }

    // ------------------------------------------------------------------ the expiry column

    #[Test]
    public function the_code_expiry_date_is_shown(): void
    {
        $profile = $this->profile(['expired_at' => '2030-01-01', 'code_profile_name' => 'Dated card']);

        $this->dashboard($this->holderFor($profile))
            ->assertSee('Code Expiry Date')
            ->assertSee('01/01/2030');
    }

    #[Test]
    public function a_free_code_that_needs_no_renewal_shows_na(): void
    {
        // Legacy printed N/A rather than a date for these.
        $profile = $this->profile([
            'expired_at' => '2030-01-01',
            'free_code' => 1,
            'renewal_required' => 0,
        ]);

        $page = $this->dashboard($this->holderFor($profile));

        $this->assertSame('N/A', $page->instance()->codeExpiryLabel($profile->fresh()));
    }

    #[Test]
    public function the_row_carries_the_expiry_colour_class(): void
    {
        $profile = $this->profile(['expired_at' => now()->subDay()->toDateString()]);

        $this->dashboard($this->holderFor($profile))->assertSee('sl-row-expired');
    }

    // ------------------------------------------------------------------ document alert flags

    #[Test]
    public function document_flags_follow_the_legacy_day_boundaries(): void
    {
        $profile = $this->profile();

        $this->document($profile, now()->subDay()->toDateString(), 'Yesterday');
        $this->document($profile, now()->toDateString(), 'Today');
        $this->document($profile, now()->addDays(30)->toDateString(), 'Day 30');
        $this->document($profile, now()->addDays(31)->toDateString(), 'Day 31');

        $page = $this->dashboard($this->holderFor($profile))->instance();
        $fresh = Profile::query()->with('vocDocuments')->findOrFail($profile->id);

        // DATEDIFF <= 0 counts as expired, so today is expired, not near.
        $this->assertSame(2, $page->expiredDocumentCount($fresh), 'yesterday + today');
        // DATEDIFF <= 30 AND > 0: day 30 is in, day 31 is out.
        $this->assertSame(1, $page->nearExpiryDocumentCount($fresh), 'day 30 only');
    }

    #[Test]
    public function a_document_with_no_real_expiry_date_is_not_flagged(): void
    {
        // The live dump stores "no date" as the 1970 sentinel; that is not an expiry in 1970.
        $profile = $this->profile();
        $this->document($profile, '1970-01-01', 'No date');

        $page = $this->dashboard($this->holderFor($profile))->instance();
        $fresh = Profile::query()->with('vocDocuments')->findOrFail($profile->id);

        $this->assertSame(0, $page->expiredDocumentCount($fresh));
        $this->assertSame(0, $page->nearExpiryDocumentCount($fresh));
    }

    #[Test]
    public function an_expired_document_shows_a_flag_on_the_row(): void
    {
        $profile = $this->profile();
        $this->document($profile, now()->subDay()->toDateString());

        $this->dashboard($this->holderFor($profile))
            ->assertSee('1 document expired');
    }

    // ------------------------------------------------------------------ search and sort

    #[Test]
    public function search_narrows_by_name(): void
    {
        $wanted = $this->profile(['code_profile_name' => 'Jane Smith NDIS']);
        $other = $this->profile(['code_profile_name' => 'Forklift ticket']);

        $ids = $this->dashboard($this->holderFor($wanted, $other))
            ->set('search', 'jane')
            ->instance()
            ->visibleProfiles()
            ->pluck('id');

        $this->assertSame([$wanted->id], $ids->all());
    }

    #[Test]
    public function search_narrows_by_profile_number(): void
    {
        $wanted = $this->profile(['code_profile_name' => 'Jane Smith NDIS']);
        $other = $this->profile(['code_profile_name' => 'Forklift ticket']);

        $ids = $this->dashboard($this->holderFor($wanted, $other))
            ->set('search', (string) $wanted->id)
            ->instance()
            ->visibleProfiles()
            ->pluck('id');

        $this->assertTrue($ids->contains($wanted->id));
        $this->assertFalse($ids->contains($other->id));
    }

    #[Test]
    public function sorting_by_expiry_respects_the_direction(): void
    {
        $later = $this->profile(['expired_at' => '2031-01-01', 'code_profile_name' => 'Later']);
        $sooner = $this->profile(['expired_at' => '2030-01-01', 'code_profile_name' => 'Sooner']);

        $page = $this->dashboard($this->holderFor($later, $sooner));

        $this->assertSame(
            [$sooner->id, $later->id],
            $page->set('sortOn', 'expiry')->set('sortDirection', 'asc')->instance()->visibleProfiles()->pluck('id')->all(),
        );

        $this->assertSame(
            [$later->id, $sooner->id],
            $page->set('sortDirection', 'desc')->instance()->visibleProfiles()->pluck('id')->all(),
        );
    }

    #[Test]
    public function sorting_by_profile_number_is_numeric(): void
    {
        $first = $this->profile(['code_profile_name' => 'Zed']);
        $second = $this->profile(['code_profile_name' => 'Alpha']);

        $ids = $this->dashboard($this->holderFor($first, $second))
            ->set('sortOn', 'id')
            ->set('sortDirection', 'asc')
            ->instance()
            ->visibleProfiles()
            ->pluck('id')
            ->all();

        $this->assertSame([min($first->id, $second->id), max($first->id, $second->id)], $ids);
    }
}
