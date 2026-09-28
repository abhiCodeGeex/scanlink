<?php

namespace Tests\Feature\Portal;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The logo in the footer of a scanned card.
 *
 * Legacy read users.footer_logo from the code's owner and loaded the file from images/logo/
 * (mobile.php:201). The rebuilt My Account page uploads to the public disk instead, so the
 * reader could no longer find anything uploaded since the cutover and every card fell back to
 * "Powered by ScanLink" — including cards that had shown a logo for years.
 *
 * The account owner's logo now also stands in for a user who has none, so one upload covers
 * every card the company owns, however its codes are split between admins.
 */
class ScanFooterLogoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_logo_uploaded_through_my_account_reaches_the_card(): void
    {
        [$client, $owner] = $this->clientWithOwner();
        Storage::disk('public')->put('logos/01UPLOADED.png', 'png');
        $owner->update(['footer_logo' => '01UPLOADED.png']);

        $this->get($this->cardUrl($client, $this->cardOwnedBy($client, $owner)))
            ->assertSuccessful()
            ->assertSee(Storage::disk('public')->url('logos/01UPLOADED.png'), false)
            ->assertDontSee('PoweredbyScanLink.png', false);
    }

    #[Test]
    public function an_imported_legacy_logo_still_reaches_the_card(): void
    {
        [$client, $owner] = $this->clientWithOwner();
        Storage::disk('legacy_media')->put('logo/548a841ddc249.png', 'png');
        $owner->update(['footer_logo' => '548a841ddc249.png']);

        $this->get($this->cardUrl($client, $this->cardOwnedBy($client, $owner)))
            ->assertSuccessful()
            ->assertSee(Storage::disk('legacy_media')->url('logo/548a841ddc249.png'), false);
    }

    #[Test]
    public function a_second_admins_card_falls_back_to_the_account_owners_logo(): void
    {
        [$client, $owner] = $this->clientWithOwner();
        Storage::disk('public')->put('logos/01COMPANY.png', 'png');
        $owner->update(['footer_logo' => '01COMPANY.png']);

        // is_sub_user = 0, role = 1: an "Enable Admin Access" user, with no logo of their own.
        $otherAdmin = ClientUser::factory()->subUser()->create([
            'client_id' => $client->id,
            'is_sub_user' => false,
            'footer_logo' => '',
        ]);

        $this->get($this->cardUrl($client, $this->cardOwnedBy($client, $otherAdmin)))
            ->assertSuccessful()
            ->assertSee(Storage::disk('public')->url('logos/01COMPANY.png'), false);
    }

    #[Test]
    public function an_admin_who_set_their_own_logo_keeps_it(): void
    {
        [$client, $owner] = $this->clientWithOwner();
        Storage::disk('public')->put('logos/01COMPANY.png', 'png');
        Storage::disk('public')->put('logos/01THEIRS.png', 'png');
        $owner->update(['footer_logo' => '01COMPANY.png']);

        $otherAdmin = ClientUser::factory()->create([
            'client_id' => $client->id,
            'is_sub_user' => false,
            'footer_logo' => '01THEIRS.png',
        ]);

        $this->get($this->cardUrl($client, $this->cardOwnedBy($client, $otherAdmin)))
            ->assertSuccessful()
            ->assertSee(Storage::disk('public')->url('logos/01THEIRS.png'), false)
            ->assertDontSee(Storage::disk('public')->url('logos/01COMPANY.png'), false);
    }

    #[Test]
    public function a_client_with_no_logo_anywhere_keeps_the_scanlink_default(): void
    {
        [$client, $owner] = $this->clientWithOwner();

        $this->get($this->cardUrl($client, $this->cardOwnedBy($client, $owner)))
            ->assertSuccessful()
            ->assertSee('PoweredbyScanLink.png', false);
    }

    #[Test]
    public function a_name_whose_file_is_gone_keeps_the_scanlink_default(): void
    {
        [$client, $owner] = $this->clientWithOwner();
        $owner->update(['footer_logo' => 'deleted-long-ago.png']);

        $this->get($this->cardUrl($client, $this->cardOwnedBy($client, $owner)))
            ->assertSuccessful()
            ->assertSee('PoweredbyScanLink.png', false);
    }

    #[Test]
    public function the_unlock_screen_carries_the_same_logo(): void
    {
        [$client, $owner] = $this->clientWithOwner();
        Storage::disk('public')->put('logos/01COMPANY.png', 'png');
        $owner->update(['footer_logo' => '01COMPANY.png']);

        $card = $this->cardOwnedBy($client, $owner);
        $card->update(['protect' => true, 'password' => 'Secret@123']);

        $this->get($this->cardUrl($client, $card))
            ->assertSuccessful()
            ->assertSee(Storage::disk('public')->url('logos/01COMPANY.png'), false);
    }

    /**
     * @return array{0: Client, 1: ClientUser}
     */
    private function clientWithOwner(): array
    {
        Storage::fake('public');
        Storage::fake('legacy_media');

        $client = Client::factory()->create(['url' => 'footer-co']);
        $owner = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'footer-owner@yopmail.com',
            'footer_logo' => '',
        ]);

        return [$client, $owner->refresh()];
    }

    private function cardOwnedBy(Client $client, ClientUser $member): Profile
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'location'], ['name' => 'Location'])->id;

        return Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'name' => 'Footer Logo Card',
            'code_profile_name' => 'Footer Logo Card',
            'deleted' => 0,
            'update_or_not' => true,
            'expired_at' => now()->addYear(),
        ]);
    }

    private function cardUrl(Client $client, Profile $profile): string
    {
        return '/'.$client->url.'/'.$profile->id;
    }
}
