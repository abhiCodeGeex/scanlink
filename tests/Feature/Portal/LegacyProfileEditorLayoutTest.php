<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LegacyProfileEditorLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_create_page_uses_legacy_two_column_layout(): void
    {
        $this->seedLocationType();

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'legacy-layout@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => false,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'legacy-layout@example.com',
            'password' => 'Portal@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($user);

        // Force query string type=location via HTTP
        $response = $this->get('/portal/profiles/create?type=location');
        $response->assertOk();
        $response->assertSee('sl-profile-editor', false);
        $response->assertSee('add-form-left', false);
        $response->assertSee('add-form-right', false);
        $response->assertSee('iphone-preview', false);
        $response->assertSee('Form Builder', false);
        $response->assertSee('Code Profile Name', false);
        $response->assertSee('Logo', false);
        $response->assertSee('Videos', false);
        $response->assertSee('Words', false);
        $response->assertSee('Pictures', false);
        $response->assertSee('Documents', false);
        $response->assertSee('Web Link', false);
        $response->assertSee('Data Collection', false);
        $response->assertSee('Location Name', false);
    }

    public function test_location_create_form_accepts_core_fields(): void
    {
        $typeId = $this->seedLocationType();

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'legacy-create@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => false,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'legacy-create@example.com',
            'password' => 'Portal@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($user);

        Livewire::withQueryParams(['type' => 'location'])
            ->test(\App\Filament\Portal\Resources\Profiles\Pages\CreateProfile::class)
            ->assertFormSet([
                'type_id' => $typeId,
                'client_id' => $client->id,
            ])
            ->fillForm([
                'code_profile_name' => 'Hotel Lobby QR',
                'name' => 'Main Lobby',
                'address' => '1 Test Street',
                'url' => 'https://maps.example.com/lobby',
                'description' => 'Welcome desk location',
                'notes' => 'Near entrance',
                'enable_data_collection' => true,
                'data_collection_name' => 'Full Name',
                'data_collection_email' => 'Email',
                'data_collection_mobile' => 'Phone',
                'data_collection_content' => 'Please leave your details',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('profiles', [
            'client_id' => $client->id,
            'type_id' => $typeId,
            'code_profile_name' => 'Hotel Lobby QR',
            'name' => 'Main Lobby',
            'address' => '1 Test Street',
        ]);
    }

    private function seedLocationType(): int
    {
        return (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'location'],
            ['name' => 'Location'],
        )->id;
    }
}
