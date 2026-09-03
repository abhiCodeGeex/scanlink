<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\ProfileContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileContactsModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_shows_contacts_manager_list_and_add_more(): void
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'plant'],
            ['name' => 'Plant & Equipment'],
        )->id;

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'contacts-modal@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'contacts-modal@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'Contacts Modal Plant',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        ProfileContact::query()->create([
            'profile_id' => $profile->id,
            'name_company' => 'Existing Co',
            'telephone' => '0400111222',
            'datestamp' => now(),
        ]);

        $this->actingAs($user);

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->assertSee('Contacts')
            ->assertSee('Existing Co')
            ->assertSee('Add more')
            ->assertSeeHtml('sl-contacts-repeater');
    }
}
