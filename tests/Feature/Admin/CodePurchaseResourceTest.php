<?php

namespace Tests\Feature\Admin;

use App\Enums\CodeOrderStatus;
use App\Filament\Resources\CodePurchases\Pages\ListCodePurchases;
use App\Filament\Resources\CodePurchases\Pages\ViewCodePurchase;
use App\Models\CodePurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CodePurchaseResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_code_orders_list(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@scanlink.com',
        ]);

        CodePurchase::factory()->count(2)->create();

        $this->actingAs($admin);

        Livewire::test(ListCodePurchases::class)
            ->assertSuccessful();
    }

    public function test_code_order_create_route_is_not_available(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@scanlink.com',
        ]);

        $this->actingAs($admin)
            ->get('/admin/code-purchases/create')
            ->assertNotFound();
    }

    public function test_status_is_stored_as_legacy_integer(): void
    {
        $order = CodePurchase::factory()->invoiceSend()->create();

        $this->assertSame(CodeOrderStatus::InvoiceSend, $order->fresh()->status);
        $this->assertDatabaseHas('code_purchase', [
            'id' => $order->id,
            'status' => 2,
        ]);
    }

    public function test_change_status_action_updates_order(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@scanlink.com',
        ]);

        $order = CodePurchase::factory()->statusNew()->create();

        $this->actingAs($admin);

        Livewire::test(ViewCodePurchase::class, ['record' => $order->getKey()])
            ->mountAction('changeStatus')
            ->assertActionMounted('changeStatus')
            ->set('mountedActions.0.data.status', CodeOrderStatus::Paid->value)
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertSame(CodeOrderStatus::Paid, $order->fresh()->status);
    }
}
