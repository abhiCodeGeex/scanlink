<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\EditAccount;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * My Account shows the images a client already has.
 *
 * The three upload fields read the public disk, but every image that came across in the import
 * sits in the legacy media tree under public/images. Filament silently drops a value whose file
 * it cannot find on its own disk, so a client with a footer logo and both shortcut icons was
 * shown three empty drop zones — and saving the form would then clear the names from the record.
 */
class AccountImagePreviewTest extends TestCase
{
    use RefreshDatabase;

    /** @var list<string> */
    private array $legacyFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->legacyFiles as $path) {
            File::delete($path);
        }

        parent::tearDown();
    }

    #[Test]
    public function an_image_in_the_legacy_media_tree_is_loaded_into_the_form(): void
    {
        $this->legacyFile('images/touch_icons/preview-test-180.png');
        $this->legacyFile('images/logo/preview-test-footer.png');

        $owner = $this->accountOwner([
            'shortcut_image1' => 'preview-test-180.png',
        ], footerLogo: 'preview-test-footer.png');

        $state = Livewire::actingAs($owner)->test(EditAccount::class)->assertOk();

        $this->assertSame(['images/touch_icons/preview-test-180.png'], $this->files($state->get('data.shortcut_image1')));
        $this->assertSame(['images/logo/preview-test-footer.png'], $this->files($state->get('data.footer_logo')));
    }

    #[Test]
    public function a_new_upload_on_the_public_disk_is_still_loaded(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('touch_icons/fresh-upload.png', 'png');

        $owner = $this->accountOwner(['shortcut_image1' => 'fresh-upload.png']);

        $state = Livewire::actingAs($owner)->test(EditAccount::class)->assertOk();

        $this->assertSame(['touch_icons/fresh-upload.png'], $this->files($state->get('data.shortcut_image1')));
    }

    #[Test]
    public function a_name_whose_file_is_gone_leaves_the_field_empty(): void
    {
        Storage::fake('public');

        $owner = $this->accountOwner(['shortcut_image1' => 'deleted-long-ago.png']);

        $state = Livewire::actingAs($owner)->test(EditAccount::class)->assertOk();

        $this->assertSame([], $this->files($state->get('data.shortcut_image1')), 'nothing to preview, and nothing to save back');
    }

    /**
     * A FileUpload holds its files as [upload id => path]; the paths are what matters here.
     *
     * @return list<string>
     */
    private function files(mixed $state): array
    {
        return array_values(array_map(strval(...), (array) $state));
    }

    /** A file where the legacy app kept it, cleaned up after the test. */
    private function legacyFile(string $relative): void
    {
        $absolute = public_path($relative);

        File::ensureDirectoryExists(dirname($absolute));
        File::put($absolute, 'test image');

        $this->legacyFiles[] = $absolute;
    }

    /**
     * @param  array<string, string>  $clientImages
     */
    private function accountOwner(array $clientImages, string $footerLogo = ''): User
    {
        $client = Client::factory()->create($clientImages + ['shortcut_title' => 'Support Test Title']);

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'account-owner@yopmail.com',
            'status' => true,
            'is_password_change' => true,
            'footer_logo' => $footerLogo,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        return $user->refresh();
    }
}
