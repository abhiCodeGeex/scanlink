<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VocLoginProvisioningTest extends TestCase
{
    use RefreshDatabase;

    private function vocProfile(): Profile
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;
        $client = Client::factory()->create();

        return Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $typeId,
            'code_profile_name' => 'VOC Login Test',
            'deleted' => 0,
            'update_or_not' => true,
        ]);
    }

    public function test_adding_a_voc_user_provisions_a_login_immediately(): void
    {
        $profile = $this->vocProfile();

        $vocUser = VocUser::create([
            'voc_user_id' => 1,
            'profile_id' => $profile->id,
            'email' => 'CardHolder@Yopmail.com',
            'password' => 'secret123',
        ]);

        $vocUser->refresh();
        $this->assertNotNull($vocUser->auth_user_id, 'voc_users.auth_user_id must be linked on create');

        $user = User::findOrFail($vocUser->auth_user_id);
        $this->assertSame('cardholder@yopmail.com', $user->email);
        $this->assertSame(UserType::Voc, $user->user_type);
        $this->assertTrue(Hash::check('secret123', $user->password), 'plaintext password must be hashed & verifiable');
    }

    public function test_second_voc_user_with_same_email_reuses_the_identity(): void
    {
        $p1 = $this->vocProfile();
        $p2 = $this->vocProfile();

        $a = VocUser::create(['voc_user_id' => 10, 'profile_id' => $p1->id, 'email' => 'dup@yopmail.com', 'password' => 'pass123']);
        $b = VocUser::create(['voc_user_id' => 11, 'profile_id' => $p2->id, 'email' => 'dup@yopmail.com', 'password' => 'pass123']);

        $a->refresh();
        $b->refresh();

        $this->assertNotNull($a->auth_user_id);
        $this->assertSame($a->auth_user_id, $b->auth_user_id, 'same email must map to a single users row');
        $this->assertSame(1, User::where('email', 'dup@yopmail.com')->count());
    }

    public function test_existing_admin_email_is_not_downgraded(): void
    {
        $admin = User::factory()->create([
            'email' => 'boss@yopmail.com',
            'user_type' => UserType::Admin,
        ]);
        $adminHash = $admin->password;

        $profile = $this->vocProfile();
        $vocUser = VocUser::create([
            'voc_user_id' => 20,
            'profile_id' => $profile->id,
            'email' => 'boss@yopmail.com',
            'password' => 'vocpass9',
        ]);

        $vocUser->refresh();
        $admin->refresh();

        $this->assertSame($admin->id, (int) $vocUser->auth_user_id, 'links to the existing identity');
        $this->assertSame(UserType::Admin, $admin->user_type, 'admin must not be downgraded to voc');
        $this->assertSame($adminHash, $admin->password, 'admin password must be untouched');
    }

    public function test_editing_the_password_resyncs_the_login(): void
    {
        $profile = $this->vocProfile();
        $vocUser = VocUser::create(['voc_user_id' => 30, 'profile_id' => $profile->id, 'email' => 'resync@yopmail.com', 'password' => 'oldpass1']);
        $vocUser->refresh();
        $userId = (int) $vocUser->auth_user_id;

        $vocUser->update(['password' => 'brandnew9']);

        $user = User::findOrFail($userId);
        $this->assertTrue(Hash::check('brandnew9', $user->password), 'edited password must re-sync to the login');
    }
}
