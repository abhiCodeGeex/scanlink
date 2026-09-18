<?php

namespace Tests\Feature\Portal;

use App\Filament\Portal\Pages\CodeBalance;
use App\Filament\Portal\Pages\ContactUs;
use App\Filament\Portal\Pages\VocDashboard;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\VocUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The VOCC login gets legacy's card holder menu — VOCC Dashboard and Sign out — and none of the
 * client's menu, even when the same person is also a primary member of a client.
 *
 * The client pages asked only "is this a portal session with a membership", which a VOCC session
 * of a client member is, so Master Code List, Code Balance, Purchase codes and Multiple Code
 * Renewal showed; Dashboard, Contact us and the How to links had no rule at all.
 */
class VocMenuTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_PASSWORD = 'Client@12345';

    private const CARD_PASSWORD = 'Card@67890';

    private const CLIENT_MENU = [
        'Master Code List',
        'Code Balance',
        'Purchase codes',
        'Multiple Code Renewal',
        'Contact us',
        'Getting Started',
    ];

    #[Test]
    public function a_vocc_session_shows_only_the_card_holder_menu(): void
    {
        $this->primaryMemberWhoHoldsACard('same@example.com');

        $this->post('/voclogin', ['email' => 'same@example.com', 'password' => self::CARD_PASSWORD])
            ->assertRedirect('/portal/voc-dashboard');

        $menu = $this->sidebar($this->get('/portal/voc-dashboard')->assertSuccessful()->getContent());

        $this->assertSame(['VOC Dashboard'], $menu, 'legacy card holder menu: VOCC Dashboard (and Sign out)');

        $this->assertTrue(VocDashboard::shouldRegisterNavigation(), 'VOC Dashboard is the card holder menu');
        $this->assertFalse(ProfileResource::canViewAny());
        $this->assertFalse(CodeBalance::canAccess());
        $this->assertFalse(ContactUs::canAccess());
        $this->get('/portal/code-balance')->assertRedirect('/portal/voc-dashboard');
    }

    #[Test]
    public function the_same_person_at_the_client_login_keeps_the_client_menu(): void
    {
        $this->primaryMemberWhoHoldsACard('same@example.com');

        $this->post('/portal-login', ['email' => 'same@example.com', 'password' => self::CLIENT_PASSWORD])
            ->assertRedirect('/portal/profiles');

        $this->assertTrue(ProfileResource::canViewAny());
        $this->assertTrue(CodeBalance::canAccess());
        $this->assertTrue(ContactUs::canAccess());
        $this->assertFalse(VocDashboard::shouldRegisterNavigation(), 'not in the client menu');

        $menu = $this->sidebar($this->get('/portal/contact')->assertSuccessful()->getContent());

        foreach (self::CLIENT_MENU as $label) {
            $this->assertContains($label, $menu);
        }
        $this->assertNotContains('VOC Dashboard', $menu);
    }

    /**
     * Labels of the sidebar navigation items as rendered.
     *
     * @return list<string>
     */
    private function sidebar(string $html): array
    {
        preg_match_all('/class="fi-sidebar-item-label"\s*>\s*([^<\n]+?)\s*(?:<|\n)/', $html, $matches);

        return array_values(array_unique($matches[1]));
    }

    private function primaryMemberWhoHoldsACard(string $email): void
    {
        $client = Client::factory()->create();

        ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => $email,
            'password' => self::CLIENT_PASSWORD,
            'status' => true,
            'is_password_change' => true,
        ]);

        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;
        $profile = Profile::factory()->create([
            'client_id' => Client::factory()->create()->id,
            'type_id' => $typeId,
            'code_profile_name' => 'HOLDERS-CARD',
            'name' => 'HOLDERS-CARD',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        VocUser::query()->create([
            'voc_user_id' => 1,
            'profile_id' => $profile->id,
            'email' => $email,
            'password' => self::CARD_PASSWORD,
        ]);
    }
}
