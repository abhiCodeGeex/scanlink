<?php

namespace Tests\Feature\Portal;

use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A password-protected URL-link code asks for the password first.
 *
 * Legacy tested `protect` at the top of the scan (mobile.php:268), before it looked at the code
 * type. The rebuild forwarded a "code" profile to its destination first, so the password on a
 * URL-link code protected nothing: scanning it took the visitor straight through.
 */
class UrlLinkPasswordGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([VerifyCsrfToken::class, PreventRequestForgery::class]);
    }

    #[Test]
    public function a_protected_url_link_code_asks_for_the_password_instead_of_redirecting(): void
    {
        [$client, $profile] = $this->protectedUrlLinkCode();

        $this->get("/{$client->url}/{$profile->id}")
            ->assertOk()
            ->assertSee('password', false)
            ->assertDontSee('https://example.com/secret-destination', false);
    }

    #[Test]
    public function the_right_password_then_forwards_to_the_destination(): void
    {
        [$client, $profile] = $this->protectedUrlLinkCode();

        $this->post("/{$client->url}/{$profile->id}/unlock", ['password' => 'Secret@123'])
            ->assertRedirect();

        $this->get("/{$client->url}/{$profile->id}")
            ->assertRedirect('https://example.com/secret-destination');
    }

    #[Test]
    public function the_wrong_password_keeps_the_destination_hidden(): void
    {
        [$client, $profile] = $this->protectedUrlLinkCode();

        $this->post("/{$client->url}/{$profile->id}/unlock", ['password' => 'nope'])
            ->assertSessionHasErrors('password');

        $this->get("/{$client->url}/{$profile->id}")
            ->assertOk()
            ->assertDontSee('https://example.com/secret-destination', false);
    }

    #[Test]
    public function an_unprotected_url_link_code_still_redirects_straight_away(): void
    {
        [$client, $profile] = $this->protectedUrlLinkCode();
        $profile->update(['protect' => false]);

        $this->get("/{$client->url}/{$profile->id}")
            ->assertRedirect('https://example.com/secret-destination');
    }

    /**
     * @return array{0: Client, 1: Profile}
     */
    private function protectedUrlLinkCode(): array
    {
        $client = Client::factory()->create(['url' => 'gate-co']);
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'code'], ['name' => 'Code'])->id;

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $typeId,
            'name' => 'Protected link',
            'code_profile_name' => 'Protected link',
            'url' => 'https://example.com/secret-destination',
            'protect' => true,
            // Live Kohana stores unlock passwords in plain text.
            'password' => 'Secret@123',
            'deleted' => 0,
            'update_or_not' => true,
            'expired_at' => now()->addYear(),
        ]);

        return [$client, $profile];
    }
}
