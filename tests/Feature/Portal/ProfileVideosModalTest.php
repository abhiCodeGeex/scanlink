<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileVideosModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_shows_videos_manager_with_clickable_title(): void
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'plant'],
            ['name' => 'Plant & Equipment'],
        )->id;

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'videos-modal@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'videos-modal@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'Videos Modal Plant',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        Video::query()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'profile_id' => $profile->id,
            'title' => 'AI video',
            'video_name' => 'VO-9LJDkk2M',
            'is_extra' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->assertSee('Videos')
            ->assertSee('AI video')
            ->assertSee('Add more')
            ->assertSeeHtml('sl-videos-repeater')
            ->assertSeeHtml('sl-video-title-link')
            ->assertSeeHtml('target="_blank"')
            ->assertSeeHtml('https://www.youtube.com/watch?v=VO-9LJDkk2M');
    }
}
