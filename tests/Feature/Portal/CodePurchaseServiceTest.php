<?php

namespace Tests\Feature\Portal;

use App\Enums\CodeOrderStatus;
use App\Models\Client;
use App\Models\CodePrising;
use App\Models\CodePurchase;
use App\Services\CodePurchaseService;
use Database\Seeders\Phase2Seeder;
use Database\Seeders\Phase5Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CodePurchaseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(Phase2Seeder::class);
        $this->seed(Phase5Seeder::class);

        $this->client = Client::query()->where('url', 'acme-inspections')->firstOrFail();
    }

    public function test_create_purchase_records_order_with_correct_totals(): void
    {
        $tier = CodePrising::query()->orderBy('code_min_qty')->firstOrFail();
        $qty = $tier->code_min_qty;

        $purchase = app(CodePurchaseService::class)->createPurchase(
            $this->client,
            $qty,
            null,
            $this->client->primaryUser,
        );

        $this->assertInstanceOf(CodePurchase::class, $purchase);
        $this->assertSame($qty, $purchase->no_of_codes);
        $this->assertSame(CodeOrderStatus::New, $purchase->status);
        $this->assertEquals(round($qty * (float) $tier->amount, 2), (float) $purchase->total_amount);
    }

    public function test_create_purchase_applies_reseller_pricing_when_code_matches(): void
    {
        $reseller = Client::factory()->create([
            'reseller_code' => 'RESELL01',
        ]);

        $tier = CodePrising::query()->orderBy('code_min_qty')->firstOrFail();
        $qty = $tier->code_min_qty;

        $purchase = app(CodePurchaseService::class)->createPurchase(
            $this->client,
            $qty,
            $reseller->reseller_code,
            $this->client->primaryUser,
        );

        $this->assertTrue($purchase->is_reseller_pricing_code);
        $this->assertSame($reseller->id, $purchase->reseller_client_id);
        $this->assertEquals(round($qty * (float) $tier->reseller_amount, 2), (float) $purchase->total_amount);
    }

    public function test_create_purchase_rejects_quantity_outside_tier(): void
    {
        $this->expectException(ValidationException::class);

        app(CodePurchaseService::class)->createPurchase($this->client, 99999);
    }
}
