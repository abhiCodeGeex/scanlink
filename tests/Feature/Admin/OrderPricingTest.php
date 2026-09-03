<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminRole;
use App\Enums\PhysicalOrderStatus;
use App\Enums\UserType;
use App\Filament\Pages\OrderPricing;
use App\Filament\Portal\Pages\FormBuilderOrderSummary;
use App\Filament\Portal\Pages\OrderLabel;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use App\Services\LabelOrderService;
use App\Support\PricingSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_settings_return_legacy_defaults_when_unset(): void
    {
        $this->assertSame(3.0, PricingSettings::labelSmall());
        $this->assertSame(5.0, PricingSettings::labelLarge());
        $this->assertSame(2.95, PricingSettings::labelPostage());
        $this->assertSame(5.0, PricingSettings::formBuilder());
    }

    public function test_pricing_settings_reflect_admin_overrides(): void
    {
        PricingSettings::set(PricingSettings::KEY_LABEL_SMALL, '7.5');
        PricingSettings::set(PricingSettings::KEY_LABEL_LARGE, '9');
        PricingSettings::set(PricingSettings::KEY_LABEL_POSTAGE, '3.25');
        PricingSettings::set(PricingSettings::KEY_FORM_BUILDER, '12');

        $this->assertSame(7.5, PricingSettings::labelSmall());
        $this->assertSame(9.0, PricingSettings::labelLarge());
        $this->assertSame(3.25, PricingSettings::labelPostage());
        $this->assertSame(12.0, PricingSettings::formBuilder());
    }

    public function test_pricing_settings_reject_invalid_values(): void
    {
        PricingSettings::set(PricingSettings::KEY_LABEL_LARGE, '-4');
        PricingSettings::set(PricingSettings::KEY_LABEL_POSTAGE, 'not-a-number');

        $this->assertSame(0.0, PricingSettings::labelLarge());
        $this->assertSame(0.0, PricingSettings::labelPostage());
    }

    public function test_form_builder_order_summary_uses_dynamic_price(): void
    {
        PricingSettings::set(PricingSettings::KEY_FORM_BUILDER, '8.25');

        $this->assertSame(8.25, (new FormBuilderOrderSummary)->totalAmount());
    }

    public function test_label_order_is_created_with_dynamic_prices(): void
    {
        PricingSettings::set(PricingSettings::KEY_LABEL_SMALL, '4.5');
        PricingSettings::set(PricingSettings::KEY_LABEL_LARGE, '8');

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'status' => true,
        ]);
        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'deleted' => 0,
        ]);

        $order = app(LabelOrderService::class)->createLabelOrder($profile, 2, 3, $member);

        $this->assertSame(4.5, (float) $order->price_small);
        $this->assertSame(8.0, (float) $order->price_large);
        $this->assertSame(PhysicalOrderStatus::Paid, $order->status);
    }

    public function test_order_label_summary_applies_dynamic_postage(): void
    {
        PricingSettings::set(PricingSettings::KEY_LABEL_SMALL, '3');
        PricingSettings::set(PricingSettings::KEY_LABEL_LARGE, '5');
        PricingSettings::set(PricingSettings::KEY_LABEL_POSTAGE, '4.50');

        $page = new OrderLabel;
        $page->priceSmall = PricingSettings::labelSmall();
        $page->priceLarge = PricingSettings::labelLarge();
        $page->qtySmall = 2; // 2 x 3 = 6
        $page->qtyLarge = 1; // 1 x 5 = 5

        $summary = $page->orderSummary();

        $this->assertSame(4.50, $summary['postage']);
        // Grand total = items (11) + postage (4.50).
        $this->assertSame(15.50, $summary['grand_total']);
    }

    public function test_admin_order_pricing_page_saves_all_prices(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserType::Admin,
            'admin_role' => AdminRole::SuperAdmin,
        ]);
        $this->actingAs($admin);

        Livewire::test(OrderPricing::class)
            ->assertSuccessful()
            ->set('data.'.PricingSettings::KEY_LABEL_SMALL, '6')
            ->set('data.'.PricingSettings::KEY_LABEL_LARGE, '9')
            ->set('data.'.PricingSettings::KEY_LABEL_POSTAGE, '3.50')
            ->set('data.'.PricingSettings::KEY_FORM_BUILDER, '11')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(6.0, PricingSettings::labelSmall());
        $this->assertSame(9.0, PricingSettings::labelLarge());
        $this->assertSame(3.5, PricingSettings::labelPostage());
        $this->assertSame(11.0, PricingSettings::formBuilder());
    }
}
