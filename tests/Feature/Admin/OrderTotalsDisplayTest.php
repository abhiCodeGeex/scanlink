<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminRole;
use App\Enums\CodeOrderStatus;
use App\Enums\UserType;
use App\Filament\Resources\CodePurchases\Pages\ViewCodePurchase;
use App\Filament\Resources\FormBuilderOrders\Pages\ViewFormBuilderOrder;
use App\Models\Client;
use App\Models\CodePurchase;
use App\Models\FormBuilderOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class OrderTotalsDisplayTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create([
            'email' => 'admin@yopmail.com',
            'user_type' => UserType::Admin,
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        $this->actingAs($admin);
        $this->client = Client::factory()->create(['url' => 'order-totals-test']);
    }

    public function test_form_builder_order_view_shows_total(): void
    {
        $order = $this->createFormBuilderOrder();

        Livewire::test(ViewFormBuilderOrder::class, ['record' => $order->getKey()])
            ->assertSuccessful()
            ->assertSee('Total')
            ->assertSee('$5.00');
    }

    public function test_form_builder_total_falls_back_to_quantity_times_unit_price(): void
    {
        // The live legacy table has no total_amount column at all.
        Schema::table('form_builder_orders', function ($table): void {
            $table->dropColumn('total_amount');
        });

        $order = $this->createFormBuilderOrder(quantity: 3, withTotal: false);

        $this->assertSame(15.0, $order->fresh()->totalAmount());

        Livewire::test(ViewFormBuilderOrder::class, ['record' => $order->getKey()])
            ->assertSuccessful()
            ->assertSee('$15.00');
    }

    public function test_code_purchase_view_shows_grand_total(): void
    {
        $order = CodePurchase::query()->create([
            'client_id' => $this->client->id,
            'email' => 'buyer@yopmail.com',
            'first_name' => 'Buyer',
            'last_name' => 'Test',
            'no_of_codes' => 10,
            'per_code_amount' => 12,
            'total_amount' => 1440,
            'status' => CodeOrderStatus::New,
            'enable' => false,
        ]);

        Livewire::test(ViewCodePurchase::class, ['record' => $order->getKey()])
            ->assertSuccessful()
            ->assertSee('Grand Total')
            ->assertSee('$1,440.00 AUD');
    }

    public function test_code_purchase_total_falls_back_when_stored_total_is_zero(): void
    {
        $order = CodePurchase::query()->create([
            'client_id' => $this->client->id,
            'email' => 'buyer@yopmail.com',
            'first_name' => 'Buyer',
            'last_name' => 'Test',
            'no_of_codes' => 4,
            'per_code_amount' => 8,
            'total_amount' => 0,
            'status' => CodeOrderStatus::New,
            'enable' => false,
        ]);

        Livewire::test(ViewCodePurchase::class, ['record' => $order->getKey()])
            ->assertSuccessful()
            ->assertSee('$32.00 AUD');
    }

    private function createFormBuilderOrder(int $quantity = 1, bool $withTotal = true): FormBuilderOrder
    {
        $payload = [
            'client_id' => $this->client->id,
            'email' => 'buyer@yopmail.com',
            'first_name' => 'Buyer',
            'last_name' => 'Test',
            'no_of_codes' => $quantity,
            'per_code_amount' => 5,
            'status' => CodeOrderStatus::New,
            'enable' => false,
        ];

        if ($withTotal && FormBuilderOrder::hasTotalAmountColumn()) {
            $payload['total_amount'] = 5 * $quantity;
        }

        return FormBuilderOrder::query()->create($payload);
    }
}
