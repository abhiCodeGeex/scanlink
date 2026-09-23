<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Picture;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocProfileImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A VOCC card photo that came across in the import lives in `voc_profile_image`, but the editor's
 * Profile Picture list reads `picture`. The photo showed on the scanned card and was missing from
 * the editor, so the holder could add another one but could never see or remove the one they had.
 *
 * Both tables hold the same thing — a file under storage — so the row belongs in the table the
 * whole application reads, and `scanlink:migrate-voc-profile-images` moves it there.
 */
class VocProfileImageEditableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_command_moves_a_legacy_photo_into_the_editable_list(): void
    {
        $profile = $this->vocProfile();

        $this->legacyPhoto($profile, 'images/picture/legacy-one.png');
        $draft = $this->legacyPhoto($profile, 'images/picture/draft.png', isTemp: true);

        $this->artisan('scanlink:migrate-voc-profile-images')
            ->expectsOutputToContain('1 photo(s) moved')
            ->assertSuccessful();

        $this->assertSame(
            ['images/picture/legacy-one.png'],
            Picture::query()->where('profile_id', $profile->id)->pluck('picture_name')->all(),
        );
        $this->assertSame(0, VocProfileImage::query()->where('voc_profile_image_id', '!=', $draft->voc_profile_image_id)->count());
        $this->assertSame(1, VocProfileImage::query()->where('is_temp', '1')->count(), 'an abandoned legacy draft is left alone');
    }

    #[Test]
    public function a_photo_already_in_both_tables_is_not_duplicated(): void
    {
        $profile = $this->vocProfile();

        Picture::query()->create([
            'client_id' => $profile->client_id,
            'user_id' => $profile->user_id,
            'profile_id' => $profile->id,
            'picture_name' => 'images/picture/shared.png',
            'is_temp' => false,
        ]);
        $this->legacyPhoto($profile, 'images/picture/shared.png');

        $this->artisan('scanlink:migrate-voc-profile-images')
            ->expectsOutputToContain('0 photo(s) moved, 1 skipped')
            ->assertSuccessful();

        $this->assertSame(1, Picture::query()->where('profile_id', $profile->id)->count());
        $this->assertSame(0, VocProfileImage::query()->count(), 'the duplicate legacy row still goes');
    }

    #[Test]
    public function a_dry_run_changes_nothing(): void
    {
        $profile = $this->vocProfile();
        $this->legacyPhoto($profile, 'images/picture/legacy-one.png');

        $this->artisan('scanlink:migrate-voc-profile-images', ['--dry-run' => true])
            ->expectsOutputToContain('[dry-run] 1 photo(s) moved')
            ->assertSuccessful();

        $this->assertSame(0, Picture::query()->count());
        $this->assertSame(1, VocProfileImage::query()->count());
    }

    #[Test]
    public function the_moved_photo_is_then_shown_in_the_profile_editor(): void
    {
        // The editor only offers files it can find on disk, so put the photo where the card's
        // picture really lives.
        Storage::fake('public');
        Storage::disk('public')->put('images/picture/legacy-one.png', 'photo');

        $profile = $this->vocProfile();
        $this->legacyPhoto($profile, 'images/picture/legacy-one.png');

        // Before: the editor's Profile Picture list is empty, which is the bug.
        $this->assertSame([], $this->editorPictures($profile));

        $this->artisan('scanlink:migrate-voc-profile-images')->assertSuccessful();

        $this->assertSame(['images/picture/legacy-one.png'], $this->editorPictures($profile));
    }

    /**
     * The picture names the editor has loaded into its Profile Picture repeater.
     *
     * @return list<string>
     */
    private function editorPictures(Profile $profile): array
    {
        $this->actingAs($this->owner);

        $state = Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->get('data.pictures');

        // A FileUpload holds its files as [uuid => path], so flatten to the stored paths.
        return collect($state ?? [])
            ->flatMap(fn (array $item): array => (array) ($item['picture_name'] ?? []))
            ->map(fn ($path): string => (string) $path)
            ->filter()
            ->values()
            ->all();
    }

    private User $owner;

    private function vocProfile(): Profile
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'voc-picture@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $this->owner = User::query()->findOrFail($member->auth_user_id);
        $this->owner->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;

        return Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'VOC Picture Test',
            'deleted' => 0,
            'update_or_not' => true,
        ]);
    }

    private function legacyPhoto(Profile $profile, string $name, bool $isTemp = false): VocProfileImage
    {
        return VocProfileImage::query()->create([
            'voc_profile_image_id' => ((int) VocProfileImage::query()->max('voc_profile_image_id')) + 1,
            'client_id' => $profile->client_id,
            'user_id' => $profile->user_id,
            'profile_id' => $profile->id,
            'image_name' => $name,
            'is_temp' => $isTemp,
        ]);
    }
}
