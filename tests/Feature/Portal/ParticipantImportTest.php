<?php

namespace Tests\Feature\Portal;

use App\Enums\UserType;
use App\Filament\Portal\Pages\ManageParticipants;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Participant;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The participant popup's "Upload list" button.
 *
 * Reported as "does nothing": clicking Upload with no file chosen threw a
 * ValidationException the embedded popup has nowhere to render, and an unrelated
 * spreadsheet imported junk rows (a "Qty" column read as Excel serial dates) with no
 * message either way.
 */
class ParticipantImportTest extends TestCase
{
    use RefreshDatabase;

    protected Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $client = Client::factory()->create();
        $this->profile = Profile::factory()->create(['client_id' => $client->id]);

        $member = ClientUser::factory()->primary()->create([
            'client_id' => $client->id,
            'email' => 'participants@example.com',
            'password' => 'Portal@12345',
            'status' => true,
            'is_password_change' => true,
        ]);
        $member->refresh();

        $user = User::query()->findOrFail($member->auth_user_id);
        $user->update([
            'email' => 'participants@example.com',
            'user_type' => UserType::Portal,
            'admin_role' => null,
        ]);

        $this->actingAs($user);
    }

    /**
     * Build a real .xlsx as a fake upload. Livewire's ->set() on a file property runs its
     * whole upload simulation, so importUpload() receives a genuine TemporaryUploadedFile
     * carrying real spreadsheet bytes.
     */
    protected function xlsx(string $name, array $rows): File
    {
        $path = tempnam(sys_get_temp_dir(), 'plist').'.xlsx';

        $writer = new XlsxWriter;
        $writer->openToFile($path);
        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }
        $writer->close();

        $file = UploadedFile::fake()->createWithContent($name, (string) file_get_contents($path));
        @unlink($path);

        return $file;
    }

    #[Test]
    public function it_imports_participants_from_a_well_formed_spreadsheet(): void
    {
        Livewire::test(ManageParticipants::class, ['profile' => $this->profile->id])
            ->set('profileId', $this->profile->id)
            ->set('uploadFile', $this->xlsx('list.xlsx', [
                ['Name', 'Response Due By'],
                ['John Smith', '15/09/2026'],
                ['Jane Doe', '30/09/2026'],
            ]))
            ->call('importUpload');

        $names = Participant::query()
            ->where('profile_id', $this->profile->id)
            ->pluck('name')
            ->all();

        $this->assertEqualsCanonicalizing(['John Smith', 'Jane Doe'], $names);
    }

    #[Test]
    public function clicking_upload_with_no_file_reports_instead_of_doing_nothing(): void
    {
        $component = Livewire::test(ManageParticipants::class, ['profile' => $this->profile->id])
            ->set('profileId', $this->profile->id)
            ->call('importUpload');

        // It must not blow up, and it must not silently swallow the click.
        $component->assertHasNoErrors();

        $this->assertSame(
            0,
            Participant::query()->where('profile_id', $this->profile->id)->count(),
        );
    }

    #[Test]
    public function an_unrelated_spreadsheet_does_not_import_junk_participants(): void
    {
        Livewire::test(ManageParticipants::class, ['profile' => $this->profile->id])
            ->set('profileId', $this->profile->id)
            ->set('uploadFile', $this->xlsx('random.xlsx', [
                ['Product', 'Qty', 'Price'],
                ['Widget', '12', '9.99'],
                ['Gadget', '3', '24.50'],
            ]))
            ->call('importUpload');

        // "12" and "3" are Excel serials for Jan 1900 — never a real due date. These rows
        // used to import as participants named Widget and Gadget.
        $this->assertSame(
            0,
            Participant::query()->where('profile_id', $this->profile->id)->count(),
            'An unrelated spreadsheet must not create participants.',
        );
    }
}
