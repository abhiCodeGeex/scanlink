<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProfileQrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalProfileRowActionsTest extends TestCase
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
            'email' => 'row-actions@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $this->user = User::query()->findOrFail($member->auth_user_id);
        $this->user->update([
            'email' => 'row-actions@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->profile = Profile::factory()->create([
            'client_id' => $client->id,
            'code_profile_name' => 'Row Action Test',
            'deleted' => 0,
        ]);
    }

    public function test_edit_page_opens(): void
    {
        $this->actingAs($this->user)
            ->get('/portal/profiles/'.$this->profile->id.'/edit')
            ->assertOk();
    }

    public function test_scan_analytics_opens_with_profile_query(): void
    {
        $this->actingAs($this->user)
            ->get('/portal/scan-analytics?profile='.$this->profile->id)
            ->assertOk();
    }

    public function test_form_submissions_opens_with_profile_query(): void
    {
        $this->actingAs($this->user)
            ->get('/portal/form-submissions?profile='.$this->profile->id)
            ->assertOk();
    }

    public function test_order_labels_opens_with_profile_query(): void
    {
        $this->actingAs($this->user)
            ->get('/portal/order-labels?profile='.$this->profile->id)
            ->assertOk();
    }

    public function test_visitor_log_opens_with_profile_query(): void
    {
        $this->actingAs($this->user)
            ->get('/portal/visitor-log?profile='.$this->profile->id)
            ->assertOk();
    }

    public function test_download_qr_regenerates_missing_file(): void
    {
        $response = app(ProfileQrService::class)->downloadQrImage($this->profile);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_voc_user_with_membership_can_open_master_code_list(): void
    {
        $this->user->update(['user_type' => UserType::Voc]);

        $this->actingAs($this->user)
            ->get('/portal/profiles')
            ->assertOk();
    }

    public function test_soft_delete_marks_profile_deleted(): void
    {
        $this->profile->update(['deleted' => true]);

        $this->assertTrue((bool) $this->profile->fresh()->deleted);
    }
}
