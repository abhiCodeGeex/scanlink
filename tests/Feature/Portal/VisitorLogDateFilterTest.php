<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\VisitorLog;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CollectedContact;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VisitorLogDateFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'vlog-filter@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $this->user = User::query()->findOrFail($member->auth_user_id);
        $this->user->update([
            'email' => 'vlog-filter@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->profile = Profile::factory()->create([
            'client_id' => $client->id,
            'code_profile_name' => 'Visitor Filter Test',
            'deleted' => 0,
        ]);

        CollectedContact::query()->create([
            'id_profile' => $this->profile->id,
            'name' => 'Lucius',
            'surname' => 'Stuart',
            'mobile' => '0400000000',
            'email' => 'lucius@example.com',
            'created_at' => '2026-08-05 04:57:00',
        ]);

        CollectedContact::query()->create([
            'id_profile' => $this->profile->id,
            'name' => 'In',
            'surname' => 'Range',
            'mobile' => '0400000001',
            'email' => 'inrange@example.com',
            'created_at' => '2026-08-07 10:00:00',
        ]);
    }

    public function test_apply_date_range_excludes_out_of_range_rows(): void
    {
        $this->actingAs($this->user);

        Livewire::test(VisitorLog::class)
            ->set('selectedProfileId', $this->profile->id)
            ->set('profileExpired', false)
            ->call('applyDateRange', '06/08/2026', '08/08/2026')
            ->assertSet('fromDate', '06/08/2026')
            ->assertSet('toDate', '08/08/2026')
            ->assertSee('In')
            ->assertSee('Range')
            ->assertDontSee('Lucius')
            ->assertDontSee('Stuart');
    }

    public function test_clear_dates_restores_all_rows(): void
    {
        $this->actingAs($this->user);

        Livewire::test(VisitorLog::class)
            ->set('selectedProfileId', $this->profile->id)
            ->set('profileExpired', false)
            ->call('applyDateRange', '06/08/2026', '08/08/2026')
            ->assertDontSee('Lucius')
            ->call('clearDates')
            ->assertSet('fromDate', '')
            ->assertSet('toDate', '')
            ->assertSee('Lucius')
            ->assertSee('In');
    }
}
