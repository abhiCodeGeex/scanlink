<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminRole;
use App\Filament\Resources\Profiles\Pages\CreateProfile;
use App\Models\Client;
use App\Models\EquipmentType;
use App\Filament\Resources\Profiles\Pages\ListProfiles;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_profiles(): void
    {
        $this->get('/admin/profiles')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_profiles_list(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@scanlink.com',
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        Profile::factory()->count(2)->create();

        $this->actingAs($admin);

        Livewire::test(ListProfiles::class)
            ->assertSuccessful();
    }

    public function test_archived_profiles_are_hidden_from_list(): void
    {
        $admin = User::factory()->create([
            'email' => 'support@scanlink.com',
            'admin_role' => AdminRole::Support,
        ]);

        $active = Profile::factory()->create(['name' => 'Active Profile']);
        Profile::factory()->deleted()->create(['name' => 'Archived Profile']);

        $this->actingAs($admin);

        Livewire::test(ListProfiles::class)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords(
                Profile::query()->where('deleted', true)->get()
            );
    }

    public function test_support_role_can_access_panel(): void
    {
        $support = User::factory()->create([
            'email' => 'support@scanlink.com',
            'admin_role' => AdminRole::Support,
        ]);

        $this->assertTrue($support->canAccessPanel(filament()->getCurrentOrDefaultPanel()));
        $this->assertFalse($support->canManageAdmins());
    }

    public function test_profile_create_validates_required_fields_for_selected_type(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@scanlink.com',
            'admin_role' => AdminRole::SuperAdmin,
        ]);

        $client = Client::factory()->create();
        $plant = EquipmentType::factory()->create(['slag' => 'plant']);

        $this->actingAs($admin);

        Livewire::test(CreateProfile::class)
            ->set('data', [
                'type_id' => $plant->id,
                'client_id' => $client->id,
                'name' => '',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
            ]);
    }
}
