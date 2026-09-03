<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Resources\Profiles\Pages\EditProfile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Document;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileDocumentsModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_shows_documents_manager_with_clickable_title(): void
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(
            ['slag' => 'plant'],
            ['name' => 'Plant & Equipment'],
        )->id;

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'documents-modal@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'documents-modal@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'Documents Modal Plant',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        Document::query()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'profile_id' => $profile->id,
            'name' => 'test doc',
            'doc_name' => 'profiles/documents/sample.pdf',
            'txt_align' => 'right',
            'btn_color' => '007A01',
            'sort_order' => 1,
            'is_temp' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(EditProfile::class, ['record' => $profile->getKey()])
            ->assertOk()
            ->assertSee('Documents')
            ->assertSee('test doc')
            ->assertSee('Add more')
            ->assertSeeHtml('sl-documents-repeater')
            ->assertSeeHtml('sl-doc-title-link')
            ->assertSeeHtml('target="_blank"')
            ->assertSeeHtml('storage/profiles/documents/sample.pdf');
    }
}
