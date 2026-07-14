<?php

namespace Tests\Unit\Models;

use App\Enums\ClientUserRole;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CodePurchase;
use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacySchemaModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_relationships_are_defined(): void
    {
        $client = Client::factory()->create();

        ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => $client->email,
        ]);

        ClientUser::factory()->subUser()->create([
            'client_id' => $client->id,
        ]);

        CodePurchase::factory()->create([
            'client_id' => $client->id,
        ]);

        $client->refresh();

        $this->assertCount(1, $client->primaryUser()->get());
        $this->assertCount(1, $client->subUsers);
        $this->assertCount(1, $client->codePurchases);
        $this->assertSame(ClientUserRole::Primary, $client->primaryUser->role);
    }

    public function test_profile_relationships_and_active_scope(): void
    {
        $client = Client::factory()->create();
        $type = EquipmentType::factory()->create(['slag' => 'asset', 'name' => 'Asset']);

        Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $type->id,
            'deleted' => false,
        ]);

        Profile::factory()->deleted()->create([
            'client_id' => $client->id,
            'type_id' => $type->id,
        ]);

        $client->refresh();

        $this->assertCount(2, $client->profiles);
        $this->assertCount(1, $client->profiles()->active()->get());
        $this->assertSame('profiles', (new Profile)->getTable());
    }

    public function test_code_purchase_uses_legacy_table_name(): void
    {
        $purchase = CodePurchase::factory()->create();

        $this->assertDatabaseHas('code_purchase', [
            'id' => $purchase->id,
        ]);
    }
}
