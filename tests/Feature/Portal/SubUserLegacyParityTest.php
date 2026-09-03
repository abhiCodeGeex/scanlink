<?php

namespace Tests\Feature\Portal;

use App\Enums\ClientUserRole;
use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kohana decides what a client user may do from users.is_sub_user, never from users.role:
 *
 *   Controller_Template_Master::before  — the two-item "My Account" submenu
 *   location.php / dashboard.php / …    — `$user_data["is_sub_user"] == 1 && $access_x == 0`
 *   profile.php action_edit_user        — "Enable Admin Access" clears is_sub_user and
 *                                         leaves role untouched
 *
 * These cases pin the rules that used to be read off `role` instead.
 */
class SubUserLegacyParityTest extends TestCase
{
    use RefreshDatabase;

    /** Legacy "Enable Admin Access": is_sub_user = 0 while role stays 1. */
    public function test_admin_access_user_with_sub_user_role_gets_the_full_account_menu(): void
    {
        $user = $this->createMember([
            'role' => ClientUserRole::SubUser,
            'is_sub_user' => false,
            'enable_admin_access' => true,
        ]);

        $this->actingAs($user)->get('/portal/code-balance')->assertOk();
        $this->actingAs($user)->get('/portal/purchase-codes')->assertOk();
        $this->actingAs($user)->get('/portal/renew-codes')->assertOk();
    }

    /** The mirror case: role 5 but flagged as a sub user (one such row exists live). */
    public function test_sub_user_flagged_account_is_restricted_even_with_the_owner_role(): void
    {
        $user = $this->createMember([
            'role' => ClientUserRole::Primary,
            'is_sub_user' => true,
        ]);

        $this->actingAs($user)->get('/portal/code-balance')->assertForbidden();
        $this->actingAs($user)->get('/portal/purchase-codes')->assertForbidden();
        $this->actingAs($user)->get('/portal/renew-codes')->assertForbidden();
    }

    /**
     * Legacy reaches the participant list only from inside the Form Builder, behind
     * Edit Profile / Form Submissions.
     */
    public function test_participants_is_denied_without_edit_or_form_submission_permission(): void
    {
        [$user, $profile] = $this->createSubUserWithProfile([
            'access_analytics' => true,
        ]);

        $this->actingAs($user)
            ->get('/portal/participants?profile='.$profile->id)
            ->assertRedirect('/portal/profiles');
    }

    public function test_participants_opens_with_form_submission_permission(): void
    {
        [$user, $profile] = $this->createSubUserWithProfile([
            'access_form_submission' => true,
        ]);

        $this->actingAs($user)
            ->get('/portal/participants?profile='.$profile->id)
            ->assertOk();
    }

    /**
     * Legacy has no read-only profile page — the Master Code List row is plain text.
     * Filament links the row to view/edit, so viewing follows the edit permission.
     */
    public function test_profile_view_page_is_denied_without_edit_permission(): void
    {
        [$user, $profile] = $this->createSubUserWithProfile([
            'access_analytics' => true,
        ]);

        $this->actingAs($user)
            ->get('/portal/profiles/'.$profile->id)
            ->assertRedirect('/portal/profiles');
    }

    public function test_profile_view_page_opens_with_edit_permission(): void
    {
        [$user, $profile] = $this->createSubUserWithProfile([
            'access_edit' => true,
        ]);

        $this->actingAs($user)
            ->get('/portal/profiles/'.$profile->id)
            ->assertOk();
    }

    /**
     * Add-a-new-code with nothing to bind used to render EditRecord without a $record and
     * die with "must not be accessed before initialization" — a 500 instead of the
     * notification and bounce back to the list.
     */
    public function test_add_a_new_code_without_an_unused_slot_redirects_instead_of_erroring(): void
    {
        $user = $this->createMember([
            'role' => ClientUserRole::Primary,
            'is_sub_user' => false,
        ]);

        $this->actingAs($user)
            ->get('/portal/profiles/create?type=location')
            ->assertRedirect('/portal/profiles');
    }

    public function test_add_a_new_code_without_a_type_redirects_instead_of_erroring(): void
    {
        $user = $this->createMember([
            'role' => ClientUserRole::Primary,
            'is_sub_user' => false,
        ]);

        $this->actingAs($user)
            ->get('/portal/profiles/create')
            ->assertRedirect('/portal/profiles');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createMember(array $attributes): User
    {
        $client = Client::factory()->create();

        $member = ClientUser::factory()->create(array_merge([
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
        ], $attributes));
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['user_type' => UserType::Portal, 'admin_role' => null]);

        return $user;
    }

    /**
     * A sub user whose "Select Code Profile" list holds exactly one profile.
     *
     * @param  array<string, mixed>  $permissions
     * @return array{0: User, 1: Profile}
     */
    private function createSubUserWithProfile(array $permissions): array
    {
        $client = Client::factory()->create();

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'code_profile_name' => 'Sub User Visible',
            'deleted' => 0,
        ]);

        $member = ClientUser::factory()->subUser()->create(array_merge([
            'client_id' => $client->id,
            'email' => fake()->unique()->safeEmail(),
            'status' => true,
            'is_password_change' => true,
            'show_code_profile_id_to_acc_user' => (string) $profile->id,
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

        return [$user, $profile];
    }
}
