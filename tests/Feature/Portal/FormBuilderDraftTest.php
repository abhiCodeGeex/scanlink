<?php

namespace Tests\Feature\Portal;

use App\Models\Client;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Services\FormBuilderDraftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FormBuilderDraftTest extends TestCase
{
    use RefreshDatabase;

    private function makeProfile(): Profile
    {
        $typeId = (int) EquipmentType::query()
            ->updateOrCreate(['slag' => 'survey'], ['name' => 'Form/Survey/Checklist'])->id;

        $client = Client::factory()->create();

        return Profile::factory()->create([
            'client_id' => $client->id,
            'type_id' => $typeId,
            'code_profile_name' => 'FB Draft Test',
            'deleted' => 0,
            'update_or_not' => true,
            'form_id' => 1,
        ]);
    }

    private function addQuestion(int $profileId, string $text, int $order): int
    {
        return (int) DB::table('form_builder_question')->insertGetId([
            'profile_id' => $profileId,
            'form_id' => 1,
            'question_type_id' => 2,
            'question_text' => $text,
            'question_order' => $order,
            'image_title' => '', 'image_align' => '',
            'button_link_url' => '', 'button_colour' => '',
            'doc_title' => '', 'log_columntitle' => '',
            'include_name' => 0, 'include_employer' => 0,
            'include_email' => 0, 'include_phone' => 0,
            'participant_include_signature' => 0, 'participant_include_employer' => 0,
            'is_mandatory' => 0, 'is_logchecked' => 0,
        ], 'question_id');
    }

    private function questionCount(int $profileId): int
    {
        return (int) DB::table('form_builder_question')->where('profile_id', $profileId)->count();
    }

    public function test_reload_reverts_uncommitted_elements_but_keeps_saved_ones(): void
    {
        $profile = $this->makeProfile();
        $svc = app(FormBuilderDraftService::class);

        // One already-committed element on the form.
        $this->addQuestion($profile->id, 'Committed', 1);

        // Open the editor -> baseline captured.
        $svc->syncOnOpen($profile->id);

        // Drag + element-save a new element (live in DB), but no green page SAVE.
        $draftId = $this->addQuestion($profile->id, 'Draft', 2);
        $this->assertSame(2, $this->questionCount($profile->id));

        // Reload the editor WITHOUT the green SAVE -> draft reverted, committed kept.
        $svc->syncOnOpen($profile->id);
        $this->assertDatabaseMissing('form_builder_question', ['question_id' => $draftId]);
        $this->assertSame(1, $this->questionCount($profile->id));
        $this->assertSame('Committed', DB::table('form_builder_question')->where('profile_id', $profile->id)->value('question_text'));

        // Add again, then green SAVE (commit), then reload -> now it persists.
        $this->addQuestion($profile->id, 'Saved', 2);
        $svc->commit($profile->id);
        $svc->syncOnOpen($profile->id);
        $this->assertSame(2, $this->questionCount($profile->id));
        $this->assertDatabaseHas('form_builder_question', ['profile_id' => $profile->id, 'question_text' => 'Saved']);
    }

    public function test_delete_and_reorder_are_reverted_on_reload_with_ids_preserved(): void
    {
        $profile = $this->makeProfile();
        $svc = app(FormBuilderDraftService::class);

        $a = $this->addQuestion($profile->id, 'A', 1);
        $b = $this->addQuestion($profile->id, 'B', 2);

        $svc->syncOnOpen($profile->id); // baseline: A(1), B(2)

        // Live edits without saving: delete A, reorder B.
        DB::table('form_builder_question')->where('question_id', $a)->delete();
        DB::table('form_builder_question')->where('question_id', $b)->update(['question_order' => 5, 'question_text' => 'B edited']);

        // Reload without SAVE -> everything reverts, original ids/values restored.
        $svc->syncOnOpen($profile->id);
        $this->assertSame(2, $this->questionCount($profile->id));
        $this->assertDatabaseHas('form_builder_question', ['question_id' => $a, 'question_text' => 'A']);
        $this->assertDatabaseHas('form_builder_question', ['question_id' => $b, 'question_text' => 'B', 'question_order' => 2]);
    }
}
