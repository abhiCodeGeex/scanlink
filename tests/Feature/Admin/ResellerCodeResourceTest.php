<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\ResellerCodes\Pages\ListResellerCodes;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResellerCodeResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_a_reseller_code_from_the_list_page(): void
    {
        $client = Client::factory()->create(['reseller_code' => null]);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListResellerCodes::class)
            ->mountAction('addResellerCode')
            ->assertActionMounted('addResellerCode')
            ->set('mountedActions.0.data.client_id', $client->getKey())
            ->set('mountedActions.0.data.reseller_code', 'NEW-RESELLER-01')
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $client->refresh();

        $this->assertSame('NEW-RESELLER-01', $client->reseller_code);
        $this->assertTrue((bool) $client->reseller_code_active);
    }

    public function test_client_and_reseller_code_are_required(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ListResellerCodes::class)
            ->mountAction('addResellerCode')
            ->callMountedAction()
            ->assertHasActionErrors([
                'client_id' => 'required',
                'reseller_code' => 'required',
            ]);
    }

    public function test_duplicate_reseller_code_is_rejected(): void
    {
        Client::factory()->create(['reseller_code' => 'EXISTING-CODE']);
        $client = Client::factory()->create(['reseller_code' => null]);

        $this->actingAs(User::factory()->create());

        Livewire::test(ListResellerCodes::class)
            ->mountAction('addResellerCode')
            ->set('mountedActions.0.data.client_id', $client->getKey())
            ->set('mountedActions.0.data.reseller_code', 'EXISTING-CODE')
            ->callMountedAction()
            ->assertHasActionErrors(['reseller_code']);

        $this->assertSame('', $client->fresh()->reseller_code);
    }
}
