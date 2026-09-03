<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\Weblink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileWeblinksModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_shows_weblinks_manager_list_and_add_another(): void
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'plant'],
            ['name' => 'Plant & Equipment'],
        )->id;

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'weblinks-modal@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'weblinks-modal@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'Weblinks Modal Plant',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        Weblink::query()->create([
            'profile_id' => $profile->id,
            'link_button' => 1,
            'link_button_text' => 'click me',
            'link_button_url' => 'https://linkinbio.com',
            'link_button_color' => '007A01',
            'link_button_align' => 'center',
        ]);

        $this->actingAs($user);

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->assertSee('Web Link')
            ->assertSee('click me')
            ->assertSee('Add another link')
            ->assertSeeHtml('sl-weblinks-repeater');
    }
}
