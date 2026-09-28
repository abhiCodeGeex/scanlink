<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\EditAccount;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Clearing an image on My Account has to stick.
 *
 * Legacy's "Remove Logo" cleared users.footer_logo and unlinked the file
 * (dashboard.php:2581-2584). The rebuilt page fell back to the stored name whenever the upload
 * field came back empty, so removing an image and pressing Submit wrote the old name straight
 * back: the picture was still there after a reload, and there was no way to take one off.
 */
class AccountImageRemovalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function clearing_the_footer_logo_removes_it(): void
    {
        [$owner, $member] = $this->accountOwner(footerLogo: 'keep-me.png');
        Storage::disk('public')->put('logos/keep-me.png', 'png');

        Livewire::actingAs($owner)->test(EditAccount::class)
            ->assertOk()
            ->set('data.footer_logo', [])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('', (string) $member->fresh()->footer_logo, 'the name is gone from the record');
        $this->assertNull($member->fresh()->footerLogoUrl(), 'and the card falls back to the default');
        $this->assertFalse(Storage::disk('public')->exists('logos/keep-me.png'), 'the file is tidied up too');
    }

    #[Test]
    public function clearing_either_shortcut_image_removes_it(): void
    {
        [$owner, $member] = $this->accountOwner(clientImages: [
            'shortcut_image1' => 'icon-180.png',
            'shortcut_image2' => 'icon-192.png',
        ]);
        Storage::disk('public')->put('touch_icons/icon-180.png', 'png');
        Storage::disk('public')->put('touch_icons/icon-192.png', 'png');

        Livewire::actingAs($owner)->test(EditAccount::class)
            ->assertOk()
            ->set('data.shortcut_image1', [])
            ->call('save')
            ->assertHasNoErrors();

        $client = $member->fresh()->client;

        $this->assertSame('', (string) $client->shortcut_image1, 'the one that was cleared');
        $this->assertSame('icon-192.png', (string) $client->shortcut_image2, 'the one that was not is untouched');
    }

    #[Test]
    public function saving_without_touching_the_images_keeps_them(): void
    {
        [$owner, $member] = $this->accountOwner(footerLogo: 'untouched.png', clientImages: [
            'shortcut_image1' => 'icon-180.png',
        ]);
        Storage::disk('public')->put('logos/untouched.png', 'png');
        Storage::disk('public')->put('touch_icons/icon-180.png', 'png');

        Livewire::actingAs($owner)->test(EditAccount::class)
            ->assertOk()
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('untouched.png', (string) $member->fresh()->footer_logo);
        $this->assertSame('icon-180.png', (string) $member->fresh()->client->shortcut_image1);
        $this->assertTrue(Storage::disk('public')->exists('logos/untouched.png'));
    }

    #[Test]
    public function an_imported_legacy_logo_survives_a_save_and_its_file_is_never_deleted(): void
    {
        [$owner, $member] = $this->accountOwner(footerLogo: '548a841ddc249.png');
        Storage::disk('legacy_media')->put('logo/548a841ddc249.png', 'png');

        Livewire::actingAs($owner)->test(EditAccount::class)
            ->assertOk()
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('548a841ddc249.png', (string) $member->fresh()->footer_logo);
        $this->assertTrue(
            Storage::disk('legacy_media')->exists('logo/548a841ddc249.png'),
            'the frozen legacy tree is never written to',
        );
    }

    #[Test]
    public function clearing_a_legacy_logo_clears_the_record_but_leaves_the_file(): void
    {
        [$owner, $member] = $this->accountOwner(footerLogo: '548a841ddc249.png');
        Storage::disk('legacy_media')->put('logo/548a841ddc249.png', 'png');

        Livewire::actingAs($owner)->test(EditAccount::class)
            ->assertOk()
            ->set('data.footer_logo', [])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('', (string) $member->fresh()->footer_logo);
        $this->assertTrue(Storage::disk('legacy_media')->exists('logo/548a841ddc249.png'));
    }

    #[Test]
    public function replacing_the_footer_logo_swaps_the_name_and_drops_the_old_file(): void
    {
        [$owner, $member] = $this->accountOwner(footerLogo: 'old.png');
        Storage::disk('public')->put('logos/old.png', 'png');
        Storage::disk('public')->put('logos/new.png', 'png');

        Livewire::actingAs($owner)->test(EditAccount::class)
            ->assertOk()
            ->set('data.footer_logo', ['fresh-upload-id' => 'logos/new.png'])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('new.png', (string) $member->fresh()->footer_logo);
        $this->assertTrue(Storage::disk('public')->exists('logos/new.png'));
        $this->assertFalse(Storage::disk('public')->exists('logos/old.png'), 'no orphan left behind');
    }

    /**
     * @param  array<string, string>  $clientImages
     * @return array{0: User, 1: ClientUser}
     */
    private function accountOwner(string $footerLogo = '', array $clientImages = []): array
    {
        Storage::fake('public');
        Storage::fake('legacy_media');

        $client = Client::factory()->create($clientImages + ['shortcut_title' => 'Support Test Title']);

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'image-removal@yopmail.com',
            'first_name' => 'Pat',
            'last_name' => 'Owner',
            'company_name' => 'Removal Co',
            'billing_address' => '1 Test Street',
            'town' => 'Melbourne',
            'phone' => '0400000000',
            'postal_code' => 3000,
            'status' => true,
            'is_password_change' => true,
            'footer_logo' => $footerLogo,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        return [$user->refresh(), $member];
    }
}
