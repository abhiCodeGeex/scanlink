<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A sub-user's own new profile has to stay visible to them after Save.
 *
 * Manage User → "Select Code Profile" is the entire entitlement for a sub-user, and nothing
 * added a freshly created profile to it. CreateProfile's record binding lets them work on an
 * open slot stamped with their user_id, but finalize() flips update_or_not to 1, that
 * exception stops applying, and the profile they just made drops off their Master Code List.
 * Reported against profile 6773, created by support@nwit.com.au.
 */
class SubUserSeesOwnCreatedProfileTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    private EquipmentType $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::factory()->create();
        $this->type = EquipmentType::query()->firstOrCreate(
            ['slag' => 'location'],
            ['name' => 'Location'],
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function member(array $attributes): ClientUser
    {
        $member = ClientUser::factory()->create(array_merge([
            'client_id' => $this->client->id,
            'email' => fake()->unique()->safeEmail(),
            'status' => true,
            'is_password_change' => true,
            'access_addcode' => true,
            'access_edit' => true,
        ], $attributes));
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($user);

        return $member;
    }

    private function openSlot(int $userId): Profile
    {
        $slot = Profile::factory()->create([
            'client_id' => $this->client->id,
            'type_id' => $this->type->id,
            'update_or_not' => false,
            'deleted' => false,
            'expired_at' => now()->addYear(),
        ]);

        Profile::query()->whereKey($slot->getKey())->update(['user_id' => $userId]);

        return $slot->refresh();
    }

    private function saveNewProfile(Profile $slot): void
    {
        session(['portal_create_draft_'.$this->client->id.'_location' => (int) $slot->id]);

        Livewire::withQueryParams(['type' => 'location'])
            ->test(CreateProfile::class)
            ->set('data.code_profile_name', 'Sub user made this')
            ->call('save')
            ->assertHasNoErrors();
    }

    #[Test]
    public function a_sub_user_keeps_seeing_the_profile_they_just_created(): void
    {
        $member = $this->member([
            'is_sub_user' => true,
            // Restricted to one unrelated profile before they create anything.
            'show_code_profile_id_to_acc_user' => '999',
        ]);

        $slot = $this->openSlot((int) $member->id);
        $this->saveNewProfile($slot);

        $this->assertContains(
            (int) $slot->id,
            $member->fresh()->allowedProfileIds() ?? [],
            'the new profile was not added to the creator\'s Select Code Profile list',
        );

        // The entitlement the primary already granted is kept, not replaced.
        $this->assertContains(999, $member->fresh()->allowedProfileIds() ?? []);

        // And it is actually on their Master Code List.
        $this->assertTrue(
            ProfileResource::getEloquentQuery()->whereKey($slot->id)->exists(),
            'the new profile is not visible on the sub-user\'s Master Code List',
        );
    }

    #[Test]
    public function an_empty_selection_still_gains_the_profile_they_created(): void
    {
        // '0' is the legacy sentinel for "nothing selected" and must not survive as an id.
        $member = $this->member([
            'is_sub_user' => true,
            'show_code_profile_id_to_acc_user' => '0',
        ]);

        $slot = $this->openSlot((int) $member->id);
        $this->saveNewProfile($slot);

        $this->assertSame([(int) $slot->id], $member->fresh()->allowedProfileIds());
    }

    #[Test]
    public function a_primary_users_selection_is_left_alone(): void
    {
        // Primary users are unrestricted; writing a list would be meaningless and could later
        // be mistaken for a real restriction.
        $member = $this->member([
            'is_sub_user' => false,
            'show_code_profile_id_to_acc_user' => '',
        ]);

        $slot = $this->openSlot((int) $member->id);
        $this->saveNewProfile($slot);

        $this->assertSame('', (string) $member->fresh()->show_code_profile_id_to_acc_user);
        $this->assertNull($member->fresh()->allowedProfileIds());
    }
}
