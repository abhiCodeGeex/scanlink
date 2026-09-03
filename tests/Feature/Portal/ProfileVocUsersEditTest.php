<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileVocUsersEditTest extends TestCase
{
    use RefreshDatabase;

    protected function createLegacyRenderTables(): void
    {
        // Legacy tables the after-save phone-preview render touches; absent from the
        // migrated test DB. Minimal shims so save() completes without a QueryException.
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $t): void {
                $t->id();
                $t->string('title')->nullable();
                $t->longText('values')->nullable();
            });
        }
        if (! Schema::hasTable('testimonial')) {
            Schema::create('testimonial', function (Blueprint $t): void {
                $t->id();
                $t->longText('content')->nullable();
                $t->string('name')->nullable();
            });
        }
    }

    public function test_editing_an_existing_voc_user_persists_on_save(): void
    {
        $this->createLegacyRenderTables();

        $typeId = (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'voc'],
            ['name' => 'VOCC'],
        )->id;

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'voc-edit@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'voc-edit@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'VOC Edit Test',
            'voc_first_name' => 'Jane',
            'voc_last_name' => 'Doe',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        $vocUser = VocUser::query()->create([
            'voc_user_id' => 1,
            'profile_id' => $profile->id,
            'email' => 'old@yopmail.com',
            'password' => 'oldpassword',
        ]);

        // Voc save now requires ≥1 notification recipient (legacy parity).
        \App\Models\VocRecipient::query()->create([
            'voc_recipient_id' => 1,
            'profile_id' => $profile->id,
            'email' => 'notify@yopmail.com',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(EditProfile::class, ['record' => $profile->getKey()]);
        $component->assertOk();

        $vocUsers = $component->get('data.vocUsers');
        $this->assertIsArray($vocUsers);
        $this->assertNotEmpty($vocUsers, 'vocUsers state should contain the existing user');

        $key = array_key_first($vocUsers);

        // The edit modal loads the password blank (VocUser hides it). Editing the email
        // and leaving the password untouched is exactly what updateRepeaterItem does:
        // merge only the changed field into the item state.
        $component->set("data.vocUsers.$key.email", 'new@yopmail.com');

        // Save may raise a non-fatal after-save preview error in the test env (legacy
        // render tables); the relationship still commits — assert on the DB directly.
        $component->call('save');

        $rows = VocUser::where('profile_id', $profile->id)->get();
        $this->assertCount(1, $rows, 'the existing row must be updated in place, not duplicated');

        $updated = $rows->first();
        $this->assertSame('new@yopmail.com', $updated->email, 'email edit must persist');
        $this->assertSame('oldpassword', $updated->getRawOriginal('password'), 'blank password must keep the existing one');
    }

    public function test_editing_the_password_persists(): void
    {
        $this->createLegacyRenderTables();

        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;
        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'voc-pw@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();
        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['email' => 'voc-pw@example.com', 'user_type' => UserType::Portal, 'admin_role' => null]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'VOC PW Test',
            'voc_first_name' => 'Jane',
            'voc_last_name' => 'Doe',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        VocUser::query()->create([
            'voc_user_id' => 1,
            'profile_id' => $profile->id,
            'email' => 'old@yopmail.com',
            'password' => 'oldpassword',
        ]);

        \App\Models\VocRecipient::query()->create([
            'voc_recipient_id' => 1,
            'profile_id' => $profile->id,
            'email' => 'notify@yopmail.com',
        ]);

        $this->actingAs($user);
        $component = Livewire::test(EditProfile::class, ['record' => $profile->getKey()]);
        $key = array_key_first($component->get('data.vocUsers'));

        $component->set("data.vocUsers.$key.email", 'changed@yopmail.com');
        $component->set("data.vocUsers.$key.password", 'brandnewpass9');
        $component->call('save');

        $updated = VocUser::where('profile_id', $profile->id)->firstOrFail();
        $this->assertSame('changed@yopmail.com', $updated->email);
        $this->assertSame('brandnewpass9', $updated->getRawOriginal('password'), 'new password must persist');
    }
}
