<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProfileQrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Under the QR on the edit page, show the TinyURL the code encodes and, beneath it, the
 * ScanLink address that TinyURL redirects to. Previously the one "URL" box showed the tiny
 * link once a profile was shortened, hiding the real address entirely.
 */
class ProfileQrUrlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_shortened_profile_shows_tiny_url_above_scanlink_url(): void
    {
        $profile = $this->profileFor(shorturl: 'https://tinyurl.com/53266nxr');
        $scanlinkUrl = app(ProfileQrService::class)->profileUrl($profile);

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->assertSeeInOrder([
                'Tiny Url',
                'https://tinyurl.com/53266nxr',
                'Scanlink Url',
                $scanlinkUrl,
            ]);
    }

    public function test_scanlink_url_is_the_real_address_not_the_tiny_one(): void
    {
        $profile = $this->profileFor(shorturl: 'https://tinyurl.com/53266nxr');
        $scanlinkUrl = app(ProfileQrService::class)->profileUrl($profile);

        $this->assertStringNotContainsString('tinyurl.com', $scanlinkUrl);

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertSeeHtmlInOrder([
                'Scanlink Url',
                '<div class="qr-url-div">'.e($scanlinkUrl).'</div>',
            ]);
    }

    public function test_profile_without_a_short_link_shows_only_the_scanlink_url(): void
    {
        $profile = $this->profileFor(shorturl: null);

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->assertSee('Scanlink Url')
            ->assertSee(app(ProfileQrService::class)->profileUrl($profile))
            ->assertDontSee('Tiny Url');
    }

    private function profileFor(?string $shorturl): Profile
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'plant'],
            ['name' => 'Plant & Equipment'],
        )->id;

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => fake()->unique()->safeEmail(),
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'QR Urls Plant',
            'deleted' => 0,
            'update_or_not' => true,
            'shorturl' => $shorturl,
        ]);

        $this->actingAs($user);

        return $profile;
    }
}
