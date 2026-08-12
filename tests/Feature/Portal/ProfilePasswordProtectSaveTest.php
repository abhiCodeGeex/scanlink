<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfilePasswordProtectSaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_profile_persists_password_protect_and_gates_scan_page(): void
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'plant'],
            ['name' => 'Plant'],
        )->id;

        $client = Client::factory()->create([
            'url' => 'protect-test-client',
        ]);
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'protect-save@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'protect-save@example.com',
            'password' => 'Portal@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'Protect Save Plant',
            'name' => 'Protect Save Plant',
            'protect' => false,
            'password' => '',
            'deleted' => false,
            'update_or_not' => true,
            'expired_at' => now()->addYear(),
        ]);

        $this->actingAs($user);

        Livewire::test(EditProfile::class, ['record' => $profile->getRouteKey()])
            ->fillForm([
                'code_profile_name' => 'Protect Save Plant',
                'protect' => '1',
                'password' => '111111',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $profile->refresh();

        $this->assertTrue((bool) $profile->protect, 'protect should be true after save');
        $this->assertSame('111111', (string) $profile->password);

        $this->get('/'.$client->url.'/'.$profile->id)
            ->assertOk()
            ->assertSee('password protected', false);
    }

    public function test_phone_preview_draft_applies_password_protect_before_save(): void
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'location'],
            ['name' => 'Location'],
        )->id;

        $client = Client::factory()->create([
            'url' => 'protect-preview-client',
        ]);
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'protect-preview@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'protect-preview@example.com',
            'password' => 'Portal@12345',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'Protect Preview Location',
            'name' => 'Protect Preview Location',
            'protect' => false,
            'password' => '',
            'deleted' => false,
            'update_or_not' => true,
            'expired_at' => now()->addYear(),
        ]);

        $this->actingAs($user);

        Livewire::test(EditProfile::class, ['record' => $profile->getRouteKey()])
            ->set('data.protect', '1')
            ->set('data.password', 'preview-secret')
            ->call('pushPhonePreviewDraft');

        $this->get('/'.$client->url.'/'.$profile->id.'?ask_for_location=no&portal_preview=1')
            ->assertOk()
            ->assertSee('password protected', false);

        // Unsaved — DB must still be unprotected.
        $profile->refresh();
        $this->assertFalse((bool) $profile->protect);
    }
}
