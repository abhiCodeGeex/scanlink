<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\ListProfiles;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortalProfilesFilterPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filter_persists_when_changing_table_page(): void
    {
        $user = $this->createPrimaryPortalUser();
        $clientId = (int) $user->clientMemberships()->value('client_id');

        $match = Profile::factory()->create([
            'client_id' => $clientId,
            'name' => 'Alpha Unique Match',
            'code_profile_name' => 'alpha-unique-match',
            'identification' => 'ID-ALPHA',
            'address' => '1 Alpha St',
        ]);

        $other = Profile::factory()->create([
            'client_id' => $clientId,
            'name' => 'Beta Other Profile',
            'code_profile_name' => 'beta-other',
            'identification' => 'ID-BETA',
            'address' => '2 Beta St',
        ]);

        // Extra rows so pagination has a second page before filtering.
        Profile::factory()->count(12)->create([
            'client_id' => $clientId,
            'name' => 'Filler Profile',
            'code_profile_name' => 'filler-profile',
        ]);

        $this->actingAs($user);

        Livewire::test(ListProfiles::class)
            ->set('tableFilters.search.search', 'Alpha Unique')
            ->assertCanSeeTableRecords([$match])
            ->assertCanNotSeeTableRecords([$other])
            ->call('gotoPage', 1)
            ->assertCanSeeTableRecords([$match])
            ->assertCanNotSeeTableRecords([$other]);
    }

    protected function createPrimaryPortalUser(): User
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => fake()->unique()->safeEmail(),
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        return $user;
    }
}
