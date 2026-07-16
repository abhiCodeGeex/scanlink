<?php

namespace Tests\Feature\Admin;

use App\Enums\CodeOrderStatus;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Mail\ClientWelcomeNotification;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ClientResourceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'email' => 'admin@scanlink.com',
            'name' => 'ScanLink Admin',
        ]);
    }

    public function test_guests_are_redirected_from_admin_clients(): void
    {
        $this->get('/admin/clients')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_clients_list(): void
    {
        Client::factory()->count(2)->create();

        $this->actingAs($this->admin());

        Livewire::test(ListClients::class)
            ->assertSuccessful();
    }

    public function test_creating_client_also_creates_primary_portal_user(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateClient::class)
            ->set('data', [
                'client_name' => 'Test Client Pty Ltd',
                'contact_person' => 'Alex Smith',
                'address' => '1 Test Street',
                'telephone' => '0400000000',
                'regi_date' => now()->toDateString(),
                'url' => 'test-client-pty',
                'email' => 'portal@test-client.example',
                'password' => 'Portal@12345',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $client = Client::query()->where('url', 'test-client-pty')->first();

        $this->assertNotNull($client);
        $this->assertDatabaseHas('client_users', [
            'client_id' => $client->id,
            'email' => 'portal@test-client.example',
            'role' => 5,
        ]);
    }

    public function test_creating_client_sends_welcome_email(): void
    {
        Mail::fake();

        $this->actingAs($this->admin());

        Livewire::test(CreateClient::class)
            ->set('data', [
                'client_name' => 'Mail Test Client',
                'contact_person' => 'Alex Smith',
                'address' => '1 Test Street',
                'telephone' => '0400000000',
                'regi_date' => now()->toDateString(),
                'url' => 'mail-test-client',
                'email' => 'welcome@mail-test.example',
                'password' => 'Portal@12345',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        Mail::assertSent(
            ClientWelcomeNotification::class,
            fn (ClientWelcomeNotification $mail): bool => $mail->hasTo('welcome@mail-test.example'),
        );
    }

    public function test_create_client_registration_date_starts_empty(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateClient::class)
            ->assertSet('data.regi_date', null);
    }

    public function test_create_client_rejects_future_registration_date(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateClient::class)
            ->set('data', [
                'client_name' => 'Future Date Client',
                'contact_person' => 'Alex Smith',
                'address' => '1 Test Street',
                'telephone' => '0400000000',
                'regi_date' => now()->addDay()->toDateString(),
                'url' => 'future-date-client',
                'email' => 'future@example.com',
                'password' => 'Portal@12345',
            ])
            ->call('create')
            ->assertHasFormErrors(['regi_date']);
    }

    public function test_client_create_validates_required_fields(): void
    {
        $this->actingAs($this->admin());

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
    }

    public function test_add_reseller_code_action_saves_code(): void
    {
        $client = Client::factory()->create(['reseller_code' => null]);

        $this->actingAs($this->admin());

        Livewire::test(EditClient::class, ['record' => $client->getKey()])
            ->mountAction('addResellerCode')
            ->assertActionMounted('addResellerCode')
            ->set('mountedActions.0.data.reseller_code', 'NEWCODE01')
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertSame('NEWCODE01', $client->fresh()->reseller_code);
    }

    public function test_add_free_codes_action_creates_free_order(): void
    {
        $client = Client::factory()->create();

        $this->actingAs($this->admin());

        Livewire::test(EditClient::class, ['record' => $client->getKey()])
            ->mountAction('addFreeCodes')
            ->assertActionMounted('addFreeCodes')
            ->set('mountedActions.0.data.no_of_codes', 10)
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('code_purchase', [
            'client_id' => $client->id,
            'no_of_codes' => 10,
            'free_code' => true,
            'status' => CodeOrderStatus::New->value,
        ]);
    }

    public function test_client_create_rejects_invalid_url_characters(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateClient::class)
            ->set('data', [
                'client_name' => 'Bad URL Client',
                'contact_person' => 'Alex',
                'address' => '1 Test Street',
                'telephone' => '0400000000',
                'regi_date' => now()->toDateString(),
                'url' => 'bad url!!',
                'email' => 'bad-url@example.com',
                'password' => 'Portal@12345',
            ])
            ->call('create')
            ->assertHasFormErrors(['url']);
    }
}
