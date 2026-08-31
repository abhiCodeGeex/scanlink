<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Deleting a video from the client's library ("Select From Existing" table) must also drop
 * it from the profile form's Videos list straight away. It used to linger there, and since
 * saving re-syncs the repeater back to the video table, the deleted video was recreated.
 */
class RemovedVideoLeavesProfileFormTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Client::factory()->create();
        $this->profile = Profile::factory()->create(['client_id' => $this->client->id]);

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $this->client->id,
            'email' => 'videos@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'videos@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($user);
    }

    protected function video(string $title, string $name, bool $extra = false): Video
    {
        return Video::query()->create([
            'client_id' => $this->client->id,
            'profile_id' => $this->profile->id,
            'title' => $title,
            'video_name' => $name,
            'is_extra' => $extra,
        ]);
    }

    #[Test]
    public function removing_a_video_from_the_library_drops_it_from_the_open_form(): void
    {
        $this->video('video 2', 'yt-two');
        $this->video('video 4', 'yt-four');

        $component = Livewire::test(EditProfile::class, ['record' => $this->profile->id]);

        $titles = collect($component->get('data.video_titles'))->pluck('title')->all();
        $this->assertEqualsCanonicalizing(['video 2', 'video 4'], $titles);

        $component->dispatch('sl-video-removed', videoName: 'yt-two', title: 'video 2');

        $after = collect($component->get('data.video_titles'))->pluck('title')->all();
        $this->assertSame(['video 4'], array_values($after), 'The deleted video must leave the form.');

        // The removal must go through the Repeater COMPONENT, so read it back from there
        // rather than from the page's data array.
        $repeater = $component->instance()->getSchemaComponent('form.video_titles', withHidden: true);
        $this->assertSame(
            ['video 4'],
            collect($repeater->getRawState())->pluck('title')->values()->all(),
        );

        // NOTE: these assertions cannot tell the two implementations apart. An earlier fix
        // that rewrote $this->data satisfied every one of them and still left the deleted
        // video rendered on screen, because a repeater renders from child schemas it caches
        // and only rawState() on the component flushes those. Livewire's test harness
        // rebuilds those schemas from state on each access, so it never reproduces the
        // stale render. That half is verified in a browser, not here.
    }

    #[Test]
    public function it_also_drops_the_video_from_the_videos_2_list(): void
    {
        $this->video('extra one', 'yt-extra-one', extra: true);
        $this->video('extra two', 'yt-extra-two', extra: true);

        $component = Livewire::test(EditProfile::class, ['record' => $this->profile->id])
            ->dispatch('sl-video-removed', videoName: 'yt-extra-one', title: 'extra one');

        $after = collect($component->get('data.video_extra_titles'))->pluck('title')->all();
        $this->assertSame(['extra two'], array_values($after));
    }

    #[Test]
    public function a_video_belonging_to_another_profile_leaves_this_form_alone(): void
    {
        $this->video('video 2', 'yt-two');

        $component = Livewire::test(EditProfile::class, ['record' => $this->profile->id])
            ->dispatch('sl-video-removed', videoName: 'yt-somewhere-else', title: 'other');

        $after = collect($component->get('data.video_titles'))->pluck('title')->all();
        $this->assertSame(['video 2'], array_values($after));
    }

    #[Test]
    public function the_remove_endpoint_reports_what_it_deleted(): void
    {
        $video = $this->video('video 2', 'yt-two');

        $this->post("/portal/videos/{$video->id}/remove")
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'removed' => ['video_name' => 'yt-two', 'title' => 'video 2'],
            ]);

        $this->assertDatabaseMissing('video', ['id' => $video->id]);
    }
}
