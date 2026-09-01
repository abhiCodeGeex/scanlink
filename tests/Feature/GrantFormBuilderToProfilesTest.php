<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Bulk grant of Form Builder entitlement. This writes a paid flag across thousands of live
 * rows, so the guards matter as much as the update: nothing happens without --force, every
 * run is undoable, and the public-facing toggle stays off unless asked for.
 */
class GrantFormBuilderToProfilesTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Client::factory()->create();
    }

    protected function profile(string $slug = 'location', int $formActive = 0, int $enabled = 0): Profile
    {
        $type = EquipmentType::query()->firstOrCreate(['slag' => $slug], ['name' => ucfirst($slug)]);

        $p = Profile::factory()->create([
            'client_id' => $this->client->id,
            'type_id' => $type->id,
            'deleted' => false,
        ]);

        Profile::query()->whereKey($p->id)->update([
            'form_active' => $formActive,
            'form_is_enable' => $enabled,
        ]);

        return $p->refresh();
    }

    #[Test]
    public function it_writes_nothing_without_force(): void
    {
        $p = $this->profile();

        $this->artisan('scanlink:grant-form-builder')->assertSuccessful();

        $this->assertSame(0, (int) $p->refresh()->getRawOriginal('form_active'));
    }

    #[Test]
    public function a_dry_run_writes_nothing_even_with_force(): void
    {
        $p = $this->profile();

        $this->artisan('scanlink:grant-form-builder', ['--dry-run' => true, '--force' => true])
            ->assertSuccessful();

        $this->assertSame(0, (int) $p->refresh()->getRawOriginal('form_active'));
    }

    #[Test]
    public function it_grants_entitlement_without_making_forms_public(): void
    {
        Storage::fake('local');
        $p = $this->profile();

        $this->artisan('scanlink:grant-form-builder', ['--force' => true])->assertSuccessful();

        $p->refresh();
        $this->assertSame(1, (int) $p->getRawOriginal('form_active'), 'entitlement should be granted');
        $this->assertSame(0, (int) $p->getRawOriginal('form_is_enable'), 'the form must not go public unasked');
        $this->assertTrue($p->formBuilderEntitled());
    }

    #[Test]
    public function enable_also_turns_the_form_on(): void
    {
        Storage::fake('local');
        $p = $this->profile();

        $this->artisan('scanlink:grant-form-builder', ['--force' => true, '--enable' => true])
            ->assertSuccessful();

        $p->refresh();
        $this->assertSame(1, (int) $p->getRawOriginal('form_active'));
        $this->assertSame(1, (int) $p->getRawOriginal('form_is_enable'));
    }

    #[Test]
    public function it_leaves_already_entitled_profiles_alone_and_is_idempotent(): void
    {
        Storage::fake('local');
        $paid = $this->profile('exhibit', formActive: 1, enabled: 1);
        $this->profile('location');

        $this->artisan('scanlink:grant-form-builder', ['--force' => true])->assertSuccessful();
        // Second run has nothing left to do.
        $this->artisan('scanlink:grant-form-builder', ['--force' => true])->assertSuccessful();

        $paid->refresh();
        $this->assertSame(1, (int) $paid->getRawOriginal('form_active'));
        $this->assertSame(1, (int) $paid->getRawOriginal('form_is_enable'), 'an already-paid profile must not be altered');
    }

    #[Test]
    public function type_limits_the_blast_radius(): void
    {
        Storage::fake('local');
        $exhibit = $this->profile('exhibit');
        $location = $this->profile('location');

        $this->artisan('scanlink:grant-form-builder', ['--force' => true, '--type' => ['exhibit']])
            ->assertSuccessful();

        $this->assertSame(1, (int) $exhibit->refresh()->getRawOriginal('form_active'));
        $this->assertSame(0, (int) $location->refresh()->getRawOriginal('form_active'), 'other types must be untouched');
    }

    #[Test]
    public function a_grant_can_be_rolled_back(): void
    {
        Storage::fake('local');
        $p = $this->profile();

        $this->artisan('scanlink:grant-form-builder', ['--force' => true, '--enable' => true])
            ->assertSuccessful();
        $this->assertSame(1, (int) $p->refresh()->getRawOriginal('form_active'));

        $snapshot = collect(Storage::disk('local')->files('form-builder-grants'))->first();
        $this->assertNotNull($snapshot, 'a snapshot must be written before any bulk write');

        $this->artisan('scanlink:grant-form-builder', ['--rollback' => $snapshot, '--force' => true])
            ->assertSuccessful();

        $p->refresh();
        $this->assertSame(0, (int) $p->getRawOriginal('form_active'), 'rollback must restore the prior value');
        $this->assertSame(0, (int) $p->getRawOriginal('form_is_enable'));
    }

    #[Test]
    public function deleted_profiles_are_never_touched(): void
    {
        Storage::fake('local');
        $p = $this->profile();
        Profile::query()->whereKey($p->id)->update(['deleted' => '1']);

        $this->artisan('scanlink:grant-form-builder', ['--force' => true])->assertSuccessful();

        $this->assertSame(0, (int) $p->refresh()->getRawOriginal('form_active'));
    }
}
