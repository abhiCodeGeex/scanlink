<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\ProfileShortLink;
use App\Services\ProfileQrService;
use App\Services\ShortLinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Short links minted and served by this application instead of TinyURL.
 *
 * The thing being bought here is independence: a code printed on a label has to resolve for
 * as long as the label exists, and a TinyURL link outlives our control of the account behind
 * it. These pin the parts that make that true - a code issued once and never re-issued, a
 * destination worked out at scan time so a renamed client cannot strand it, and a clean
 * fallback so switching the driver on can never leave a profile with no usable QR.
 */
class ShortLinkTest extends TestCase
{
    use RefreshDatabase;

    private const DOMAIN = 'scnlk.test';

    /** The host is checked per request, so plain config() is enough to switch this on. */
    private function useLocalShortener(): void
    {
        config([
            'shortlinks.driver' => 'local',
            'shortlinks.domain' => self::DOMAIN,
            'shortlinks.scheme' => 'https',
            'shortlinks.length' => 7,
        ]);
    }

    private function profile(string $clientUrl = 'acme-co', string $type = 'location'): Profile
    {
        $client = Client::factory()->create(['url' => $clientUrl]);
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => $type], ['name' => ucfirst($type)])->id;

        return Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $typeId,
            'name' => 'Short Link Test',
            'code_profile_name' => 'Short Link Test',
            'deleted' => 0,
            'update_or_not' => true,
            'expired_at' => now()->addYear(),
            'shorturl' => '',
        ]);
    }

    #[Test]
    public function a_profile_gets_a_short_url_on_our_own_domain(): void
    {
        $this->useLocalShortener();
        Http::fake();

        $url = app(ProfileQrService::class)->resolveQrData($this->profile());

        $this->assertStringStartsWith('https://'.self::DOMAIN.'/', $url);
        $this->assertMatchesRegularExpression('#^https://'.preg_quote(self::DOMAIN, '#').'/[0-9A-HJKMNP-TV-Z]{7}$#', $url);

        // Nothing was asked of TinyURL.
        Http::assertNothingSent();
    }

    #[Test]
    public function the_code_is_issued_once_and_never_changes(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile();
        $service = app(ShortLinkService::class);

        $first = $service->codeFor($profile);
        $second = $service->codeFor($profile->fresh());

        $this->assertSame($first, $second, 'a label already printed must keep working');
        $this->assertSame(1, ProfileShortLink::query()->where('profile_id', $profile->id)->count());
    }

    #[Test]
    public function two_profiles_never_share_a_code(): void
    {
        $this->useLocalShortener();
        $service = app(ShortLinkService::class);

        $codes = collect(range(1, 25))
            ->map(fn (int $i): string => $service->codeFor($this->profile('client-'.$i)))
            ->all();

        $this->assertCount(25, array_unique($codes));
    }

    #[Test]
    public function the_saved_short_url_is_reused_rather_than_minting_a_second_one(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile();
        $qr = app(ProfileQrService::class);

        $first = $qr->resolveQrData($profile);
        $second = $qr->resolveQrData($profile->fresh());

        $this->assertSame($first, $second);
        $this->assertSame(1, ProfileShortLink::query()->count());
    }

    #[Test]
    public function an_imported_tinyurl_is_left_exactly_as_it_is(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile();
        $profile->update(['shorturl' => 'https://tinyurl.com/already-printed']);

        $url = app(ProfileQrService::class)->resolveQrData($profile->fresh());

        $this->assertSame('https://tinyurl.com/already-printed', $url, 'printed links are untouchable');
        $this->assertSame(0, ProfileShortLink::query()->count());
    }

    #[Test]
    public function scanning_a_code_redirects_to_the_profile(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile('acme-co');
        $code = app(ShortLinkService::class)->codeFor($profile);

        $response = $this->get('https://'.self::DOMAIN.'/'.$code);

        $response->assertRedirect(app(ProfileQrService::class)->profileUrl($profile->fresh()));
        $response->assertStatus(302);
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    #[Test]
    public function a_code_follows_a_client_that_renames_its_url(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile('old-name');
        $code = app(ShortLinkService::class)->codeFor($profile);

        $profile->client->update(['url' => 'new-name']);

        // Built from the app's own base, not the host that was scanned - the redirect has to
        // leave the short domain behind and land on the real site.
        $expected = app(ProfileQrService::class)->profileUrl($profile->fresh());

        $this->assertStringContainsString('/new-name/'.$profile->id, $expected);

        $this->get('https://'.self::DOMAIN.'/'.$code)
            ->assertRedirect($expected);
    }

    #[Test]
    public function a_custom_qr_code_redirects_to_its_own_destination(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile('acme-co', 'customqr');
        $profile->update(['name' => 'https://example.com/brochure']);
        $code = app(ShortLinkService::class)->codeFor($profile);

        $this->get('https://'.self::DOMAIN.'/'.$code)
            ->assertRedirect('https://example.com/brochure');
    }

    #[Test]
    public function codes_are_seven_uppercase_characters_without_look_alikes(): void
    {
        $this->useLocalShortener();
        $service = app(ShortLinkService::class);

        $codes = collect(range(1, 60))
            ->map(fn (int $i): string => $service->codeFor($this->profile('alphabet-'.$i)));

        foreach ($codes as $code) {
            $this->assertSame(7, strlen($code));
            $this->assertMatchesRegularExpression('/^[0-9A-Z]{7}$/', $code, 'uppercase only');
            $this->assertDoesNotMatchRegularExpression('/[ILOU]/', $code, 'I, L, O and U are never issued');
        }

        // Every character comes from the documented 32-character set.
        $this->assertSame('', str_replace(str_split(ShortLinkService::ALPHABET), '', $codes->implode('')));
        $this->assertSame(32, strlen(ShortLinkService::ALPHABET));
        $this->assertSame(0, preg_match('/[ILOU]/', ShortLinkService::ALPHABET));
    }

    #[Test]
    public function a_code_typed_in_lowercase_opens_the_same_profile(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile('acme-co');
        $code = app(ShortLinkService::class)->codeFor($profile);
        $expected = app(ProfileQrService::class)->profileUrl($profile->fresh());

        $this->get('https://'.self::DOMAIN.'/'.strtolower($code))->assertRedirect($expected);
        $this->get('https://'.self::DOMAIN.'/'.ucfirst(strtolower($code)))->assertRedirect($expected);
        $this->get('https://'.self::DOMAIN.'/'.$code)->assertRedirect($expected);
    }

    #[Test]
    public function a_skipped_letter_typed_for_its_digit_still_finds_the_code(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile('acme-co');
        ProfileShortLink::query()->create(['profile_id' => $profile->id, 'code' => '7K30X1M']);
        $expected = app(ProfileQrService::class)->profileUrl($profile->fresh());

        // O for 0, and I or l for 1, in either case.
        $this->get('https://'.self::DOMAIN.'/7K3OXIM')->assertRedirect($expected);
        $this->get('https://'.self::DOMAIN.'/7k3oxlm')->assertRedirect($expected);
    }

    #[Test]
    public function normalising_a_code_uppercases_it_and_maps_look_alikes(): void
    {
        $service = app(ShortLinkService::class);

        $this->assertSame('7K3QX9M', $service->normalize(' 7k3qx9m '));
        $this->assertSame('7K30X11', $service->normalize('7k3oxIl'));
    }

    #[Test]
    public function an_unknown_code_lands_somewhere_useful_rather_than_a_404(): void
    {
        $this->useLocalShortener();

        $this->get('https://'.self::DOMAIN.'/zzzzzzz')
            ->assertStatus(302)
            ->assertRedirect(rtrim((string) config('scanlink.portal_url'), '/').'/');
    }

    #[Test]
    public function the_short_route_does_not_exist_on_the_main_site(): void
    {
        $this->useLocalShortener();
        $profile = $this->profile('acme-co');
        $code = app(ShortLinkService::class)->codeFor($profile);

        // The same path on the marketing domain must not resolve a short code - that is what
        // keeps /contact, /pricing and friends safe from a bare /{code}.
        $this->get('/'.$code)->assertNotFound();

        $this->get('/contact')->assertOk();
    }

    #[Test]
    public function with_the_driver_off_nothing_changes(): void
    {
        config(['shortlinks.driver' => 'tinyurl', 'shortlinks.domain' => self::DOMAIN]);
        config(['scanlink.short_url_api_token' => null]);

        $profile = $this->profile();
        $url = app(ProfileQrService::class)->resolveQrData($profile);

        $this->assertSame(app(ProfileQrService::class)->profileUrl($profile), $url);
        $this->assertSame(0, ProfileShortLink::query()->count());
    }

    #[Test]
    public function the_driver_without_a_domain_falls_back_instead_of_minting_dead_links(): void
    {
        config(['shortlinks.driver' => 'local', 'shortlinks.domain' => null]);
        config(['scanlink.short_url_api_token' => null]);

        $profile = $this->profile();
        $url = app(ProfileQrService::class)->resolveQrData($profile);

        $this->assertFalse(app(ShortLinkService::class)->enabled());
        $this->assertSame(app(ProfileQrService::class)->profileUrl($profile), $url);
        $this->assertSame(0, ProfileShortLink::query()->count());
    }

    #[Test]
    public function a_domain_pasted_in_with_a_scheme_is_tolerated(): void
    {
        config(['shortlinks.driver' => 'local', 'shortlinks.domain' => 'https://scnlk.test/', 'shortlinks.scheme' => 'https']);

        $this->assertSame('scnlk.test', app(ShortLinkService::class)->domain());
        $this->assertSame('https://scnlk.test', app(ShortLinkService::class)->baseUrl());
    }
}
