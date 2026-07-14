<?php

namespace Tests\Unit\Services;

use App\Enums\CodeOrderStatus;
use App\Models\Client;
use App\Models\CodePrising;
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
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $type->id,
            'expired_at' => now()->subDay(),
            'update_or_not' => true,
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
}
