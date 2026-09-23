<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Logo;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A profile keeps one company logo: the last one uploaded.
 *
 * Legacy never deleted the previous row — each upload added one and every read took the newest
 * row that was not an abandoned draft. The rebuild ordered and filtered nothing, so a migrated
 * profile stacked its whole logo history on the scanned card, the editor previewed the oldest of
 * them, and saving deleted the rest: the customer's live logo was quietly replaced by an old one.
 * 346 live profiles hold more than one logo row.
 */
class ProfileLogoCurrentOnlyTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    #[Test]
    public function only_the_newest_logo_counts_and_drafts_are_ignored(): void
    {
        $profile = $this->profileWithLogoHistory();

        $this->assertSame(
            ['images/logo/newest.jpg', 'images/logo/oldest.jpg'],
            $profile->logos()->pluck('logo_name')->all(),
            'newest first, and the abandoned draft is not one of them',
        );
    }

    #[Test]
    public function the_editor_shows_the_logo_that_is_on_the_card(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('images/logo/newest.jpg', 'logo');

        $profile = $this->profileWithLogoHistory();

        $this->actingAs($this->owner);

        $state = Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->get('data.logo_upload');

        $this->assertSame(['images/logo/newest.jpg'], array_values((array) $state));
    }

    #[Test]
    public function saving_the_profile_keeps_the_current_logo_not_an_old_one(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('images/logo/newest.jpg', 'logo');

        $profile = $this->profileWithLogoHistory();

        $this->actingAs($this->owner);

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->call('save');

        $this->assertSame(
            ['images/logo/newest.jpg'],
            Logo::query()->where('profile_id', $profile->id)->where('is_temp', '0')->pluck('logo_name')->all(),
            'the save must not swap the live logo for an older upload',
        );
    }

    /** A migrated profile: two real logos over the years, plus an upload that was never saved. */
    private function profileWithLogoHistory(): Profile
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'logo-owner@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $this->owner = User::query()->findOrFail($member->auth_user_id);
        $this->owner->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'location'], ['name' => 'Location'])->id;

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'Logo History',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        $this->logo($profile, 'images/logo/oldest.jpg', '2013-01-01 09:00:00');
        $this->logo($profile, 'images/logo/newest.jpg', '2021-06-01 09:00:00');
        $this->logo($profile, 'images/logo/abandoned-draft.jpg', '2024-01-01 09:00:00', isTemp: true);

        return $profile->fresh();
    }

    private function logo(Profile $profile, string $name, string $createdAt, bool $isTemp = false): void
    {
        Logo::query()->create([
            'client_id' => $profile->client_id,
            'user_id' => $profile->user_id,
            'profile_id' => $profile->id,
            'logo_name' => $name,
            'is_temp' => $isTemp,
            'created_at' => $createdAt,
        ]);
    }
}
