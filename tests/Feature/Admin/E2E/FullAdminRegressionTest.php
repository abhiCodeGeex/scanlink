<?php

namespace Tests\Feature\Admin\E2E;

use App\Enums\AdminRole;
use App\Enums\CodeOrderStatus;
use App\Enums\PhysicalOrderStatus;
use App\Filament\Pages\AdminHome;
use App\Filament\Pages\ChangePassword;
use App\Filament\Pages\CodePricing;
use App\Filament\Pages\GlobalSettings;
use App\Filament\Pages\ResellerPricingSettings;
use App\Filament\Pages\SubdivideClient;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\ManageClientUsers;
use App\Filament\Resources\CodePurchases\Pages\ListCodePurchases;
use App\Filament\Resources\CodePurchases\Pages\ViewCodePurchase;
use App\Filament\Resources\FormBuilderOrders\Pages\ListFormBuilderOrders;
use App\Filament\Resources\FormBuilderOrders\Pages\ViewFormBuilderOrder;
use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Resources\Profiles\Pages\EditProfile;
use App\Filament\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Models\Client;
use App\Models\CodePurchase;
use App\Models\EquipmentType;
use App\Models\FormBuilderOrder;
use App\Models\Order;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FullAdminRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create([
            'email' => 'admin@scanlink.com',
            'admin_role' => AdminRole::SuperAdmin,
        ]);
    }

    private function support(): User
    {
        return User::factory()->create([
            'email' => 'support@scanlink.com',
            'admin_role' => AdminRole::Support,
        ]);
    }

    public function test_auth_pages_and_guest_redirects(): void
    {
        $this->get('/admin/login')->assertOk();
        $this->get('/admin/register')->assertOk();
        $this->get('/admin/password-reset/request')->assertOk();
        $this->get('/admin/password-reset/reset')->assertForbidden();
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/clients')->assertRedirect('/admin/login');
        $this->get('/admin/profiles')->assertRedirect('/admin/login');
        $this->get('/admin/global-settings')->assertRedirect('/admin/login');
    }

    public function test_all_core_pages_load_for_super_admin(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin);

        Livewire::test(AdminHome::class)->assertSuccessful();
        Livewire::test(ChangePassword::class)->assertSuccessful();
        Livewire::test(ListClients::class)->assertSuccessful();
        Livewire::test(CreateClient::class)->assertSuccessful();
        Livewire::test(ListProfiles::class)->assertSuccessful();
        Livewire::test(CreateProfile::class)->assertSuccessful();
        Livewire::test(ListOrders::class)->assertSuccessful();
        Livewire::test(ListCodePurchases::class)->assertSuccessful();
        Livewire::test(ListFormBuilderOrders::class)->assertSuccessful();
        Livewire::test(ListTestimonials::class)->assertSuccessful();
        Livewire::test(CreateTestimonial::class)->assertSuccessful();
        Livewire::test(ListGalleries::class)->assertSuccessful();
        Livewire::test(CreateGallery::class)->assertSuccessful();
        Livewire::test(SubdivideClient::class)->assertSuccessful();
        Livewire::test(GlobalSettings::class)->assertSuccessful();
        Livewire::test(CodePricing::class)->assertSuccessful();
        Livewire::test(ResellerPricingSettings::class)->assertSuccessful();
    }

    public function test_client_crud_validation_and_actions(): void
    {
        $this->actingAs($this->superAdmin());

        Livewire::test(CreateClient::class)
            ->set('data', [
                'client_name' => '',
                'contact_person' => '',
                'address' => '',
                'telephone' => '',
                'regi_date' => null,
                'url' => '',
                'email' => '',
                'password' => '',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'client_name' => 'required',
                'contact_person' => 'required',
                'address' => 'required',
                'telephone' => 'required',
                'regi_date' => 'required',
                'url' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);

        Livewire::test(CreateClient::class)
            ->set('data', [
                'client_name' => 'Bad URL',
                'contact_person' => 'Alex',
                'address' => '1 Street',
                'telephone' => '0400000000',
                'regi_date' => now()->toDateString(),
                'url' => 'bad url!!',
                'email' => 'bad-url@example.com',
                'password' => 'Portal@12345',
            ])
            ->call('create')
            ->assertHasFormErrors(['url']);

        Livewire::test(CreateClient::class)
            ->set('data', [
                'client_name' => 'Good Client',
                'contact_person' => 'Alex',
                'address' => '1 Street',
                'telephone' => '0400000000',
                'regi_date' => now()->toDateString(),
                'url' => 'good-client',
                'email' => 'good-client@example.com',
                'password' => 'Portal@12345',
                'txtUseremail' => 'sub-good@example.com',
                'txtUserpassword' => 'Sub@12345',
                'videopermission' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $client = Client::query()->where('url', 'good-client')->first();
        $this->assertNotNull($client);
        $this->assertDatabaseHas('client_users', [
            'client_id' => $client->id,
            'email' => 'good-client@example.com',
        ]);
        $this->assertDatabaseHas('client_users', [
            'client_id' => $client->id,
            'email' => 'sub-good@example.com',
        ]);

        Livewire::test(EditClient::class, ['record' => $client->getKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'client_name' => 'Good Client',
                'url' => 'good-client',
            ]);

        Livewire::test(ManageClientUsers::class, ['record' => $client->getKey()])
            ->assertSuccessful()
            ->assertSee('Add User')
            ->assertSee('sub-good@example.com')
            ->assertCanSeeTableRecords($client->subUsers()->where('email', 'sub-good@example.com')->get());
    }

    public function test_profile_crud_validation_view_and_archive(): void
    {
        $this->actingAs($this->superAdmin());

        $client = Client::factory()->create();
        $plant = EquipmentType::factory()->create(['slag' => 'plant', 'name' => 'Plant']);

        Livewire::test(CreateProfile::class)
            ->set('data', [
                'type_id' => $plant->id,
                'client_id' => $client->id,
                'name' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);

        Livewire::test(CreateProfile::class)
            ->set('data', [
                'type_id' => $plant->id,
                'client_id' => $client->id,
                'name' => 'Pump A',
                'identification' => 'P-1',
                'serial_no' => 'S-1',
                'description' => 'Test pump',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $profile = Profile::query()->where('name', 'Pump A')->first();
        $this->assertNotNull($profile);

        Livewire::test(ViewProfile::class, ['record' => $profile->getKey()])
            ->assertSuccessful()
            ->assertSee('Pump A');

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertSuccessful()
            ->callAction('delete');

        $this->assertTrue((bool) $profile->fresh()->deleted);

        Livewire::test(ListProfiles::class)
            ->assertCanNotSeeTableRecords([$profile->fresh()]);
    }

    public function test_all_order_types_list_view_and_status_change(): void
    {
        $this->actingAs($this->superAdmin());

        $code = CodePurchase::factory()->statusNew()->create();
        $order = Order::factory()->create(['status' => PhysicalOrderStatus::New]);
        $fb = FormBuilderOrder::query()->create([
            'client_id' => Client::factory()->create()->id,
            'first_name' => 'Form',
            'last_name' => 'Buyer',
            'email' => 'form@example.com',
            'phone' => '0400000000',
            'postal_code' => '3000',
            'status' => CodeOrderStatus::New,
        ]);

        Livewire::test(ListCodePurchases::class)->assertSuccessful();
        Livewire::test(ViewCodePurchase::class, ['record' => $code->getKey()])
            ->assertSuccessful()
            ->mountAction('changeStatus')
            ->set('mountedActions.0.data.status', CodeOrderStatus::Paid->value)
            ->callMountedAction()
            ->assertHasNoActionErrors();
        $this->assertSame(CodeOrderStatus::Paid, $code->fresh()->status);

        Livewire::test(ListOrders::class)->assertSuccessful();
        Livewire::test(ViewOrder::class, ['record' => $order->getKey()])
            ->assertSuccessful()
            ->mountAction('changeStatus')
            ->set('mountedActions.0.data.status', PhysicalOrderStatus::Paid->value)
            ->callMountedAction()
            ->assertHasNoActionErrors();
        $this->assertSame(PhysicalOrderStatus::Paid, $order->fresh()->status);

        Livewire::test(ListFormBuilderOrders::class)->assertSuccessful();
        Livewire::test(ViewFormBuilderOrder::class, ['record' => $fb->getKey()])
            ->assertSuccessful();

        $this->get('/admin/code-purchases/create')->assertNotFound();
        $this->get('/admin/orders/create')->assertNotFound();
    }

    public function test_cms_and_settings_validation_and_role_gates(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin);

        Livewire::test(CreateTestimonial::class)
            ->call('create')
            ->assertHasFormErrors(['title', 'video']);

        Livewire::test(CreateGallery::class)
            ->call('create')
            ->assertHasFormErrors(['images']);

        Livewire::test(GlobalSettings::class)
            ->assertSuccessful()
            ->set('data.paypal_email', 'pay@example.com')
            ->set('data.contact_email', 'contact@example.com')
            ->set('data.youtube_client_id', 'client-id')
            ->set('data.youtube_client_secret', 'GOCSPX-test')
            ->call('save')
            ->assertHasErrors();

        $support = $this->support();
        $this->actingAs($support);

        $this->assertFalse(GlobalSettings::canAccess());
        $this->assertFalse(\App\Filament\Resources\Clients\ClientResource::canCreate());
        $this->get('/admin/profiles')->assertSuccessful();
        $this->get('/admin/global-settings')->assertForbidden();
    }
}
