<?php

namespace Tests\Feature\Portal;

use App\Enums\ClientUserRole;
use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PortalPermissionGatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_sub_user_without_analytics_permission_cannot_access_scan_analytics(): void
    {
        $user = $this->createSubUser([
            'access_analytics' => false,
        ]);

        $this->assertDeniedLikeLegacy(
            $this->actingAs($user)->get('/portal/scan-analytics'),
            'Oops! You have no access to view analytics.',
        );
    }

    public function test_sub_user_with_analytics_permission_can_access_scan_analytics(): void
    {
        $user = $this->createSubUser([
            'access_analytics' => true,
        ]);

        $this->actingAs($user)
            ->get('/portal/scan-analytics')
            ->assertOk();
    }

    public function test_sub_user_without_form_permission_cannot_access_form_submissions(): void
    {
        $user = $this->createSubUser([
            'access_form_submission' => false,
        ]);

        $this->assertDeniedLikeLegacy(
            $this->actingAs($user)->get('/portal/form-submissions'),
            'Oops! You have no access to view form submissions',
        );
    }

    public function test_sub_user_without_visitor_log_permission_cannot_access_visitor_log(): void
    {
        $user = $this->createSubUser([
            'access_log' => false,
        ]);

        $this->assertDeniedLikeLegacy(
            $this->actingAs($user)->get('/portal/visitor-log'),
            'Oops! You have no access to view visitor log.',
        );
    }

    /**
     * Legacy never 403s a sub-user: every guarded controller sets an errormessage and
     * redirects to the code list (see App\Http\Middleware\RedirectDeniedPortalActions).
     */
    protected function assertDeniedLikeLegacy(TestResponse $response, string $message): void
    {
        $response->assertRedirect('/portal/profiles');

        $titles = collect(session('filament.notifications', []))
            ->map(fn (array $notification): string => (string) ($notification['title'] ?? ''))
            ->all();

        $this->assertContains($message, $titles);
    }

    /**
     * @param  array<string, mixed>  $permissions
     */
    protected function createSubUser(array $permissions = []): User
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->subUser()->create(array_merge([
            'client_id' => $client->id,
            'email' => fake()->unique()->safeEmail(),
            'status' => true,
            'is_password_change' => true,
            'access_addcode' => false,
            'access_edit' => false,
            'access_delete' => false,
            'access_analytics' => false,
            'access_form_submission' => false,
            'access_download' => false,
            'access_label' => false,
            'access_log' => false,
        ], $permissions));
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        return $user;
    }
}
