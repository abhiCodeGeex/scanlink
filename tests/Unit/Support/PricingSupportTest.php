<?php

namespace Tests\Unit\Support;

use App\Models\CodePurchase;
use App\Models\CodePurchaseDetail;
use App\Models\Order;
use App\Support\CodePurchasePricing;
use App\Support\PhysicalOrderPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingSupportTest extends TestCase
{
    use RefreshDatabase;

    public function test_code_purchase_uses_per_code_amount_when_set(): void
    {
        $order = CodePurchase::factory()->create([
            'per_code_amount' => 5.50,
            'no_of_codes' => 10,
            'total_amount' => 55.00,
        ]);

        $summary = CodePurchasePricing::summarize($order);

        $this->assertSame(['$5.50 AUD'], $summary['lines']);
        $this->assertSame(55.0, $summary['subtotal']);
        $this->assertSame(55.0, $summary['grand_total']);
    }

    public function test_code_purchase_uses_tier_lines_when_per_code_amount_is_zero(): void
    {
        $order = CodePurchase::factory()->create([
            'per_code_amount' => 0,
            'no_of_codes' => 3,
            'total_amount' => 36.00,
        ]);

        CodePurchaseDetail::query()->create([
            'code_purchase_id' => $order->id,
            'amount' => 1.50,
        ]);

        CodePurchaseDetail::query()->create([
            'code_purchase_id' => $order->id,
            'amount' => 1.50,
        ]);

        CodePurchaseDetail::query()->create([
            'code_purchase_id' => $order->id,
            'amount' => 2.00,
        ]);

        $summary = CodePurchasePricing::summarize($order->fresh('details'));

        $this->assertContains('2 code/s @ $18.00', $summary['lines']);
        $this->assertContains('1 code/s @ $24.00', $summary['lines']);
        $this->assertSame(36.0, $summary['grand_total']);
    }

    public function test_physical_order_uses_label_sizes_by_date_cutoff(): void
    {
        $oldOrder = Order::factory()->create([
            'ordered_on' => '2014-07-01',
            'qty_small' => 2,
            'qty_large' => 1,
            'price_small' => 10,
            'price_large' => 20,
        ]);

        $newOrder = Order::factory()->create([
            'ordered_on' => '2014-08-01',
            'qty_small' => 2,
            'qty_large' => 1,
            'price_small' => 10,
            'price_large' => 20,
        ]);

        $oldSummary = PhysicalOrderPricing::summarize($oldOrder);
        $newSummary = PhysicalOrderPricing::summarize($newOrder);

        $this->assertSame('45 X 60 mm label', $oldSummary['small_label']);
        $this->assertSame('50 X 40 mm label', $newSummary['small_label']);
        $this->assertSame(42.95, $oldSummary['grand_total']);
        $this->assertSame(2.95, $oldSummary['postage']);
    }
}
