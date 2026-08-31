<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\CreateProfile;
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
 * "Add a New Code" as a sub-user, reported as a 404.
 *
 * A sub-user only sees profiles listed in show_code_profile_id_to_acc_user, plus open slots
 * stamped with their own user_id. CreateProfile mounts as an EditRecord on the claimed slot,
 * so the slot has to be visible through that scoped query or record resolution 404s.
 */
class SubUserCreateProfileDraftReuseTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected ClientUser $subMember;

    protected EquipmentType $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::factory()->create();
        // 'location' is already seeded — firstOrCreate, never a blind factory insert.
        $this->type = EquipmentType::query()->firstOrCreate(
            ['slag' => 'location'],
            ['name' => 'Location'],
        );

        $this->subMember = ClientUser::factory()->subUser()->create([
            'client_id' => $this->client->id,
            'email' => 'sub-draft@example.com',
            'password' => 'SubUser@12345',
            'status' => true,
            'is_password_change' => true,
            'access_addcode' => true,
            'access_edit' => true,
            // Sub-user is restricted to one unrelated profile id.
            'show_code_profile_id_to_acc_user' => '999',
        ]);
        $this->subMember->refresh();

        $user = User::query()->findOrFail($this->subMember->auth_user_id);
        $user->update([
            'email' => 'sub-draft@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($user);
    }

    /** An unused paid slot owned by the given client user. */
    protected function openSlot(int $userId): Profile
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

    /** Another member of the same client — e.g. the primary who bought the codes. */
    protected function otherMember(): ClientUser
    {
        return ClientUser::factory()->primary()->create([
            'client_id' => $this->client->id,
            'email' => 'primary-owner@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
    }

    #[Test]
    public function a_sub_user_can_reopen_add_new_code_on_an_existing_session_draft(): void
    {
        // A slot of the SAME type that the sub-user's session already points at. This is the
        // branch that reuses the draft instead of claiming a fresh one.
        $slot = $this->openSlot((int) $this->subMember->id);

        session(['portal_create_draft_'.$this->client->id.'_location' => (int) $slot->id]);

        Livewire::withQueryParams(['type' => 'location'])
            ->test(CreateProfile::class)
            ->assertSuccessful();
    }

    #[Test]
    public function a_sub_user_can_open_add_new_code_on_a_slot_owned_by_someone_else(): void
    {
        // Slots bought by the primary user carry the primary's user_id.
        $slot = $this->openSlot(userId: (int) $this->otherMember()->id);

        session(['portal_create_draft_'.$this->client->id.'_location' => (int) $slot->id]);

        Livewire::withQueryParams(['type' => 'location'])
            ->test(CreateProfile::class)
            ->assertSuccessful();
    }

    #[Test]
    public function a_fresh_claim_still_works_for_a_sub_user(): void
    {
        $this->openSlot((int) $this->otherMember()->id);

        Livewire::withQueryParams(['type' => 'location'])
            ->test(CreateProfile::class)
            ->assertSuccessful();
    }
}
