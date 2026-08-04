<?php

namespace Tests\Feature\Portal;

use App\Enums\AdminRole;
use App\Enums\UserType;
use App\Filament\Portal\Pages\FormBuilderOrderSummary;
use App\Filament\Portal\Pages\PurchaseFormBuilder;
use App\Mail\ScanlinkMail;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\FormBuilderOrder;
use App\Models\Profile;
use App\Models\User;
use App\Services\FormBuilderPurchaseService;
use App\Services\FormBuilderService;
use Database\Seeders\Phase3Seeder;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class FormBuilderPurchaseLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;

    private ClientUser $member;

    private User $user;

    private Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(Phase3Seeder::class);

        $this->client = Client::factory()->create([
            'email' => 'buyer@yopmail.com',
            'url' => 'form-builder-purchase-test',
        ]);
        $this->member = ClientUser::factory()->primary()->create([
            'client_id' => $this->client->id,
            'email' => 'buyer@yopmail.com',
            'first_name' => 'Buyer',
            'last_name' => 'Test',
            'company_name' => 'Buyer Co',
            'billing_address' => '1 Test Street',
            'town' => 'Sydney',
            'phone' => '0400000000',
            'postal_code' => '2000',
            'status' => true,
            'is_password_change' => true,
        ]);
        $this->user = User::query()->findOrFail($this->member->auth_user_id);
        $this->user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        User::factory()->create([
            'email' => 'admin@yopmail.com',
            'user_type' => UserType::Admin,
            'admin_role' => AdminRole::SuperAdmin,
        ]);
        config()->set('scanlink.admin_email', 'admin@yopmail.com');

        $type = EquipmentType::query()->firstOrFail();
        $this->profile = Profile::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->member->id,
            'type_id' => $type->id,
            'name' => 'Form Builder Purchase Profile',
            'deleted' => false,
            'update_or_not' => true,
            'form_active' => false,
            'form_is_enable' => false,
        ]);
    }

    public function test_purchase_creates_legacy_order_and_activates_profile(): void
    {
        Mail::fake();

        $order = app(FormBuilderPurchaseService::class)->purchase(
            $this->profile,
            $this->client,
            $this->member,
            $this->billing(),
        );

        $this->assertDatabaseHas('form_builder_orders', [
            'id' => $order->id,
            'client_id' => $this->client->id,
            'email' => 'buyer@yopmail.com',
            'town' => 'Sydney',
            'postal_code' => '2000',
            'no_of_codes' => 1,
            'per_code_amount' => 5,
            'total_amount' => 5,
            'status' => '0',
            'enable' => '0',
        ]);
        $this->assertDatabaseHas('form_builder_order_detail', [
            'form_builder_order_id' => $order->id,
            'profile_id' => $this->profile->id,
        ]);

        $profile = $this->profile->fresh();
        $this->assertTrue((bool) $profile->form_active);
        $this->assertTrue((bool) $profile->form_is_enable);
        $this->assertTrue((bool) $profile->pop_up_formbuilder);
        $this->assertNotNull(FormBuilderOrder::find($order->id)?->exipry_date);

        Mail::assertSent(ScanlinkMail::class, 2);
    }

    public function test_duplicate_purchase_is_blocked_atomically(): void
    {
        Mail::fake();
        $service = app(FormBuilderPurchaseService::class);

        $service->purchase($this->profile, $this->client, $this->member, $this->billing());

        $this->expectException(DomainException::class);
        $service->purchase($this->profile->fresh(), $this->client, $this->member, $this->billing());
    }

    public function test_expired_profile_cannot_purchase_form_builder(): void
    {
        Mail::fake();
        $this->profile->update(['expired_at' => now()->subDay()]);

        $this->expectException(DomainException::class);
        app(FormBuilderPurchaseService::class)->purchase(
            $this->profile->fresh(),
            $this->client,
            $this->member,
            $this->billing(),
        );
    }

    public function test_reseller_purchase_notifies_reseller_and_admin(): void
    {
        Mail::fake();

        $reseller = Client::factory()->create([
            'client_name' => 'Reseller Co',
            'email' => 'reseller@yopmail.com',
            'reseller_email' => 'reseller@yopmail.com',
            'reseller_code' => 'FORM5',
            'reseller_code_active' => true,
        ]);
        ClientUser::factory()->primary()->create([
            'client_id' => $reseller->id,
            'email' => 'reseller@yopmail.com',
            'status' => true,
        ]);

        app(FormBuilderPurchaseService::class)->purchase(
            $this->profile,
            $this->client,
            $this->member,
            $this->billing(),
            [
                'is_reseller_pricing_code' => '1',
                'reseller_client_id' => $reseller->id,
            ],
        );

        Mail::assertSent(
            ScanlinkMail::class,
            fn (ScanlinkMail $mail): bool => $mail->hasTo('reseller@yopmail.com'),
        );
        Mail::assertSent(
            ScanlinkMail::class,
            fn (ScanlinkMail $mail): bool => $mail->hasTo('admin@yopmail.com'),
        );
        Mail::assertNotSent(
            ScanlinkMail::class,
            fn (ScanlinkMail $mail): bool => $mail->hasTo('buyer@yopmail.com'),
        );
    }

    public function test_form_settings_cannot_grant_paid_entitlement(): void
    {
        app(FormBuilderService::class)->updateFormSettings($this->profile, [
            'form_title' => 'Draft form',
            'form_is_enable' => true,
            'form_active' => true,
            'recipients' => ['buyer@yopmail.com'],
        ]);

        $profile = $this->profile->fresh();
        $this->assertFalse((bool) $profile->form_active);
        $this->assertFalse((bool) $profile->form_is_enable);
        $this->assertSame('Draft form', $profile->form_title);
    }

    public function test_existing_legacy_entitlement_can_be_disabled_without_being_revoked(): void
    {
        $this->profile->forceFill([
            'form_active' => true,
            'form_is_enable' => true,
        ])->save();

        app(FormBuilderService::class)->updateFormSettings($this->profile->fresh(), [
            'form_is_enable' => false,
        ]);

        $profile = $this->profile->fresh();
        $this->assertTrue((bool) $profile->form_active);
        $this->assertFalse((bool) $profile->form_is_enable);
    }

    public function test_primary_user_can_open_profile_scoped_billing_and_summary(): void
    {
        $this->actingAs($this->user)
            ->get('/portal/purchase-form-builder?profile='.$this->profile->id)
            ->assertOk()
            ->assertSee('Billing details')
            ->assertSee('Form Builder Purchase Profile');

        $this->withSession([
            \App\Filament\Portal\Pages\PurchaseFormBuilder::SESSION_CHECKOUT => [
                'profile_id' => $this->profile->id,
                'quantity' => 1,
                'per_code_amount' => 5,
                'is_reseller_pricing_code' => '0',
                'reseller_client_id' => 0,
            ],
            \App\Filament\Portal\Pages\PurchaseFormBuilder::SESSION_BILLING => array_merge(
                $this->billing(),
                ['client_id' => $this->client->id],
            ),
        ])->get('/portal/form-builder-order-summary')
            ->assertOk()
            ->assertSee('Scanlink Form Builder Activation')
            ->assertSee('$5.00');
    }

    public function test_livewire_billing_to_summary_to_activation_lifecycle(): void
    {
        Mail::fake();
        $this->actingAs($this->user);

        Livewire::test(PurchaseFormBuilder::class)
            ->set('profileId', $this->profile->id)
            ->set('firstName', 'Buyer')
            ->set('lastName', 'Test')
            ->set('companyName', 'Buyer Co')
            ->set('billingAddress', '1 Test Street')
            ->set('email', 'buyer@yopmail.com')
            ->set('town', 'Sydney')
            ->set('phone', '0400000000')
            ->set('postalCode', '2000')
            ->call('next')
            ->assertRedirect(FormBuilderOrderSummary::getUrl(panel: 'portal'));

        Livewire::test(FormBuilderOrderSummary::class)
            ->set('agreeTerms', true)
            ->call('proceed')
            ->assertRedirect(
                \App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl(
                    'edit',
                    ['record' => $this->profile],
                    panel: 'portal',
                ),
            );

        $this->assertTrue((bool) $this->profile->fresh()->form_active);
        $this->assertDatabaseHas('form_builder_orders', [
            'client_id' => $this->client->id,
            'total_amount' => 5,
        ]);
    }

    public function test_activate_form_builder_does_not_block_on_blank_code_profile_name(): void
    {
        // Legacy auto_save_* proceeded to activation with NO validation. Blanking the
        // required Code Profile Name must NOT block "Activate Form Builder".
        Mail::fake();
        $this->actingAs($this->user);

        $location = EquipmentType::query()->where('slag', 'location')->firstOrFail();
        $this->profile->forceFill([
            'type_id' => $location->id,
            'code_profile_name' => '',
        ])->save();

        Livewire::test(\App\Filament\Portal\Resources\Profiles\Pages\EditProfile::class, [
            'record' => $this->profile->getRouteKey(),
        ])
            ->set('data.code_profile_name', '')
            ->call('startFormBuilderPurchase')
            ->assertHasNoErrors()
            ->assertRedirect(PurchaseFormBuilder::getUrl(['profile' => $this->profile->id], panel: 'portal'));
    }

    public function test_sub_user_cannot_access_purchase_pages(): void
    {
        $sub = ClientUser::factory()->subUser()->create([
            'client_id' => $this->client->id,
            'email' => 'sub@yopmail.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $subUser = User::query()->findOrFail($sub->auth_user_id);
        $subUser->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        $this->actingAs($subUser)
            ->get('/portal/purchase-form-builder?profile='.$this->profile->id)
            ->assertForbidden();
    }

    public function test_success_popup_is_consumed_once_on_return_to_profile(): void
    {
        Mail::fake();
        app(FormBuilderPurchaseService::class)->purchase(
            $this->profile,
            $this->client,
            $this->member,
            $this->billing(),
        );

        $firstPage = new class
        {
            use \App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyProfileEditorLayout;

            public function consume(Profile $profile): void
            {
                $this->consumeFormBuilderOrderSuccess($profile);
            }
        };
        $firstPage->consume($this->profile->fresh());

        $this->assertTrue($firstPage->showFormBuilderOrderSuccess);
        $this->assertFalse((bool) $this->profile->fresh()->pop_up_formbuilder);

        $secondPage = new class
        {
            use \App\Filament\Portal\Resources\Profiles\Pages\Concerns\HasLegacyProfileEditorLayout;

            public function consume(Profile $profile): void
            {
                $this->consumeFormBuilderOrderSuccess($profile);
            }
        };
        $secondPage->consume($this->profile->fresh());

        $this->assertFalse($secondPage->showFormBuilderOrderSuccess);
    }

    /**
     * @return array<string, string>
     */
    private function billing(): array
    {
        return [
            'first_name' => 'Buyer',
            'last_name' => 'Test',
            'company_name' => 'Buyer Co',
            'billing_address' => '1 Test Street',
            'email' => 'buyer@yopmail.com',
            'town' => 'Sydney',
            'phone' => '0400000000',
            'postal_code' => '2000',
        ];
    }
}
