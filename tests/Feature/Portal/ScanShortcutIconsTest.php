<?php

namespace Tests\Feature\Portal;

use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The Shortcut Title and the two Shortcut Images a client uploads on My Account belong in the
 * head of every scanned card: the page title, the iOS home-screen icon and startup image from
 * image 1, the Android icon from image 2 (legacy views/mobile/template/head.php:2, 12-24).
 *
 * The rebuild saved all three and read none of them, so "Add to Home Screen" gave a generic icon
 * and the tab kept the default title.
 */
class ScanShortcutIconsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_card_carries_the_clients_shortcut_title_and_home_screen_icons(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('touch_icons/icon-180.png', 'ios');
        Storage::disk('public')->put('touch_icons/icon-192.png', 'android');

        $client = Client::factory()->create([
            'url' => 'shortcut-co',
            'shortcut_title' => 'Support Test Title',
            'shortcut_image1' => 'icon-180.png',
            'shortcut_image2' => 'icon-192.png',
        ]);

        $page = $this->get('/shortcut-co/'.$this->profileFor($client)->id)->assertSuccessful();

        $page->assertSee('<title>Support Test Title</title>', false);
        $page->assertSee('rel="apple-touch-icon" sizes="180x180" href="'.Storage::disk('public')->url('touch_icons/icon-180.png').'"', false);
        $page->assertSee('rel="apple-touch-startup-image"', false);
        $page->assertSee('rel="icon" sizes="192x192" href="'.Storage::disk('public')->url('touch_icons/icon-192.png').'"', false);
    }

    #[Test]
    public function a_client_without_them_keeps_the_default_title_and_gets_no_icons(): void
    {
        Storage::fake('public');

        $client = Client::factory()->create([
            'url' => 'plain-co',
            'shortcut_title' => '',
            'shortcut_image1' => '',
            'shortcut_image2' => '',
        ]);
        $profile = $this->profileFor($client);

        $page = $this->get('/plain-co/'.$profile->id)->assertSuccessful();

        $page->assertSee('<title>'.$profile->name.' — ScanLink</title>', false);
        $page->assertDontSee('apple-touch-icon', false);
        $page->assertDontSee('sizes="192x192"', false);
    }

    #[Test]
    public function an_icon_whose_file_is_missing_is_not_linked(): void
    {
        Storage::fake('public');

        $client = Client::factory()->create([
            'url' => 'gone-co',
            'shortcut_image1' => 'deleted-long-ago.png',
        ]);

        $this->assertNull($client->shortcutIconUrl(1), 'no link to a file that is not there');

        $this->get('/gone-co/'.$this->profileFor($client)->id)
            ->assertSuccessful()
            ->assertDontSee('apple-touch-icon', false);
    }

    private function profileFor(Client $client): Profile
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'location'], ['name' => 'Location'])->id;

        return Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $typeId,
            'name' => 'Shortcut Test Profile',
            'code_profile_name' => 'Shortcut Test Profile',
            'deleted' => 0,
            'update_or_not' => true,
        ]);
    }
}
