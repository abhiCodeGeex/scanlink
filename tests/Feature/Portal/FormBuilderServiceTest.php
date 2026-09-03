<?php

namespace Tests\Feature\Portal;

use App\Models\Client;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionType;
use App\Models\Profile;
use App\Services\FormBuilderService;
use Database\Seeders\Phase2Seeder;
use Database\Seeders\Phase3Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FormBuilderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Client $client;

    protected Profile $profile;

    protected FormBuilderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(Phase2Seeder::class);
        $this->seed(Phase3Seeder::class);

        $this->client = Client::query()->where('url', 'acme-inspections')->firstOrFail();
        $this->profile = Profile::query()
            ->where('client_id', $this->client->id)
            ->where('code_profile_name', 'acme-forklift-a12')
            ->firstOrFail();

        $this->service = app(FormBuilderService::class);

        $this->seedQuestionTypes();
    }

    public function test_save_question_creates_question_and_options(): void
    {
        $question = $this->service->saveQuestion($this->profile, [
            'question_type_id' => 3,
            'question_text' => 'Pick one',
            'is_mandatory' => true,
        ], [
            ['option_name' => 'Yes', 'question_option_type_id' => 0],
            ['option_name' => 'No', 'question_option_type_id' => 0],
        ]);

        $this->assertDatabaseHas('form_builder_question', [
            'question_id' => $question->question_id,
            'profile_id' => $this->profile->id,
            'question_text' => 'Pick one',
            'question_type_id' => 3,
        ]);

        $this->assertCount(2, $question->options);
        $this->assertSame('Yes', $question->options->first()->option_name);
    }

    public function test_reorder_updates_question_order(): void
    {
        $first = $this->service->saveQuestion($this->profile, [
            'question_type_id' => 1,
            'question_text' => 'First',
        ]);

        $second = $this->service->saveQuestion($this->profile, [
            'question_type_id' => 1,
            'question_text' => 'Second',
        ]);

        $this->service->reorder($this->profile->fresh(), [
            $second->question_id,
            $first->question_id,
        ]);

        $this->assertSame(2, $first->fresh()->question_order);
        $this->assertSame(1, $second->fresh()->question_order);

        $ordered = FormBuilderQuestion::query()
            ->where('profile_id', $this->profile->id)
            ->orderBy('question_order')
            ->pluck('question_text')
            ->all();

        $this->assertSame(['Second', 'First'], $ordered);
    }

    public function test_palette_groups_returns_active_types(): void
    {
        $groups = $this->service->paletteGroups();

        $this->assertArrayHasKey('question', $groups);
        $this->assertArrayHasKey('format', $groups);
        $this->assertArrayHasKey('answer', $groups);
        $this->assertGreaterThan(0, $groups['question']->count() + $groups['format']->count() + $groups['answer']->count());
    }

    protected function seedQuestionTypes(): void
    {
        if (! Schema::hasTable('form_builder_question_types')) {
            return;
        }

        $types = [
            1 => ['label' => 'Text', 'type' => 0, 'is_active' => 1],
            2 => ['label' => 'Display', 'type' => 1, 'is_active' => 1],
            3 => ['label' => 'Radio', 'type' => 2, 'is_active' => 1],
        ];

        foreach ($types as $id => $row) {
            FormBuilderQuestionType::query()->updateOrCreate(
                ['question_type_id' => $id],
                $row,
            );
        }
    }
}
