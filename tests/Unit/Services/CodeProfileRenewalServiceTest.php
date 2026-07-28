<?php

namespace Tests\Unit\Services;

use App\Enums\CodeOrderStatus;
use App\Models\Client;
use App\Models\CodePrising;
use App\Models\CodePurchase;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Services\CodeProfileRenewalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CodeProfileRenewalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_renew_extends_expiry_and_creates_order(): void
    {
        $client = Client::factory()->create();
        $type = EquipmentType::factory()->create(['slag' => 'code', 'name' => 'Code']);

        CodePrising::query()->create([
            'code_min_qty' => 1,
            'code_max_qty' => 100,
            'amount' => 2.5,
            'reseller_amount' => 2.0,
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $type->id,
            'expired_at' => now()->subDay(),
            'update_or_not' => true,
            'is_reseller_code' => '0',
        ]);

        $order = app(CodeProfileRenewalService::class)->renew([$profile]);

        $this->assertSame(CodeOrderStatus::Renew, $order->status);
        $this->assertSame(1, $order->no_of_codes);
        $this->assertEquals(30.0, (float) $order->total_amount);

        $profile->refresh();

        $this->assertTrue($profile->expired_at->isFuture());
        $this->assertSame($order->id, $profile->code_purchase_id);
        $this->assertDatabaseHas('code_purchase_detail', [
            'code_purchase_id' => $order->id,
            'profile_id' => $profile->id,
        ]);
    }

    public function test_quote_reuses_original_price_when_not_expired(): void
    {
        $client = Client::factory()->create();
        $type = EquipmentType::factory()->create(['slag' => 'code', 'name' => 'Code']);

        CodePrising::query()->create([
            'code_min_qty' => 1,
            'code_max_qty' => 100,
            'amount' => 2.5,
            'reseller_amount' => 2.0,
        ]);

        $purchase = CodePurchase::query()->create([
            'client_id' => $client->id,
            'email' => 'a@example.com',
            'no_of_codes' => 1,
            'per_code_amount' => 1.75,
            'total_amount' => 21.0,
            'status' => CodeOrderStatus::New,
            'enable' => false,
            'exipry_date' => now()->addYear(),
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $type->id,
            'code_purchase_id' => $purchase->id,
            'expired_at' => now()->addMonths(3),
            'update_or_not' => true,
            'is_reseller_code' => '0',
        ]);

        $quote = app(CodeProfileRenewalService::class)->quote([$profile]);

        $this->assertSame([1.75], $quote['amounts']);
        $this->assertEquals(21.0, $quote['total']);
    }
}
