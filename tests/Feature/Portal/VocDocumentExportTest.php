<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\VocDashboard;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class VocDocumentExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_only_lists_documents_due_within_30_days_or_expired(): void
    {
        $typeId = (int) EquipmentType::query()->updateOrCreate(['slag' => 'voc'], ['name' => 'VOCC'])->id;

        $client = Client::factory()->create();
        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'voc-export@example.com',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();
        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update(['email' => 'voc-export@example.com', 'user_type' => UserType::Portal, 'admin_role' => null]);

        $profile = Profile::factory()->create([
            'client_id' => $client->id,
            'user_id' => $member->id,
            'type_id' => $typeId,
            'code_profile_name' => 'VOC Export',
            'deleted' => 0,
            'update_or_not' => true,
        ]);

        $today = Carbon::today();
        VocDocument::query()->create(['voc_document_id' => 1, 'profile_id' => $profile->id, 'name' => 'Due Soon', 'expiry_date' => $today->copy()->addDays(10)->toDateString()]);
        VocDocument::query()->create(['voc_document_id' => 2, 'profile_id' => $profile->id, 'name' => 'Far Off', 'expiry_date' => $today->copy()->addDays(60)->toDateString()]);
        VocDocument::query()->create(['voc_document_id' => 3, 'profile_id' => $profile->id, 'name' => 'Expired', 'expiry_date' => $today->copy()->subDays(5)->toDateString()]);
        VocDocument::query()->create(['voc_document_id' => 4, 'profile_id' => $profile->id, 'name' => 'No Expiry', 'expiry_date' => null]);

        $this->actingAs($user);
        $component = Livewire::test(VocDashboard::class);

        $rows = $component->instance()->expiringDocumentRows();

        // Only "Due Soon" (within 30) and "Expired" — not "Far Off" (60 days) nor "No Expiry".
        $names = collect($rows)->pluck(1)->all();
        $this->assertCount(2, $rows);
        $this->assertContains('Due Soon', $names);
        $this->assertContains('Expired', $names);
        $this->assertNotContains('Far Off', $names);
        $this->assertNotContains('No Expiry', $names);

        // Due Soon → date in "30 Day Expiry" col (index 2), empty "Expired" col.
        $dueSoon = collect($rows)->firstWhere(1, 'Due Soon');
        $this->assertSame($today->copy()->addDays(10)->format('d/m/Y'), $dueSoon[2]);
        $this->assertSame('', $dueSoon[3]);

        // Expired → empty "30 Day Expiry", date in "Expired" col (index 3).
        $expired = collect($rows)->firstWhere(1, 'Expired');
        $this->assertSame('', $expired[2]);
        $this->assertSame($today->copy()->subDays(5)->format('d/m/Y'), $expired[3]);

        // The xlsx download works.
        $component->call('exportDocuments')->assertFileDownloaded('document_list.xlsx');
    }
}
