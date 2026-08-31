<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SwmsFormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            PreventRequestForgery::class,
        ]);
    }

    #[Test]
    public function it_stores_photos_for_each_swms_hazard_row(): void
    {
        Storage::fake('public');

        $client = Client::factory()->create(['url' => 'swms-test-client']);
        $profile = Profile::factory()->create(['client_id' => $client->id]);

        $question = FormBuilderQuestion::query()->create([
            'question_id' => 501,
            'profile_id' => $profile->id,
            'form_id' => 1,
            'question_type_id' => 22,
            'question_text' => 'SWMS hazard',
            'is_mandatory' => false,
        ]);

        $photo0 = UploadedFile::fake()->image('swms-row0.jpg');
        $photo1 = UploadedFile::fake()->image('swms-row1.jpg');

        $this->post("/{$client->url}/{$profile->id}/form", [
            'answers_meta' => [
                $question->question_id => [
                    'task' => ['Dig trench', 'Install pipe'],
                    'potential_hazards' => ['Collapse', 'Manual handling'],
                    'risk_score_before' => ['2', '4'],
                    'control_measures' => ['Shoring', 'Team lift'],
                    'risk_score_after' => ['1', '2'],
                ],
            ],
            'answers_file' => [
                $question->question_id => [
                    0 => [$photo0],
                    1 => [$photo1],
                ],
            ],
        ])->assertRedirect();

        $stored = FormBuilderAnswer::query()
            ->where('question_id', $question->question_id)
            ->value('question_answer');

        $this->assertNotNull($stored);
        $this->assertStringContainsString('@@SWMS@@', (string) $stored);

        $instances = explode('@@SWMS@@', (string) $stored);
        $this->assertCount(2, $instances);
        $this->assertStringContainsString('photo=', $instances[0]);
        $this->assertStringContainsString('photo=', $instances[1]);
        $this->assertStringContainsString('swms-row0.jpg', $instances[0]);
        $this->assertStringContainsString('swms-row1.jpg', $instances[1]);
    }

    #[Test]
    public function it_stores_second_row_photo_when_first_row_has_no_photo(): void
    {
        Storage::fake('public');

        $client = Client::factory()->create(['url' => 'swms-sparse-client']);
        $profile = Profile::factory()->create(['client_id' => $client->id]);

        $question = FormBuilderQuestion::query()->create([
            'question_id' => 502,
            'profile_id' => $profile->id,
            'form_id' => 1,
            'question_type_id' => 22,
            'question_text' => 'SWMS hazard',
            'is_mandatory' => false,
        ]);

        $photo1 = UploadedFile::fake()->image('swms-row1-only.jpg');

        $this->post("/{$client->url}/{$profile->id}/form", [
            'answers_meta' => [
                $question->question_id => [
                    'task' => ['No photo row', 'Photo row'],
                    'potential_hazards' => ['A', 'B'],
                    'risk_score_before' => ['1', '2'],
                    'control_measures' => ['A', 'B'],
                    'risk_score_after' => ['1', '2'],
                ],
            ],
            'answers_file' => [
                $question->question_id => [
                    1 => [$photo1],
                ],
            ],
        ])->assertRedirect();

        $stored = FormBuilderAnswer::query()
            ->where('question_id', $question->question_id)
            ->value('question_answer');

        $instances = explode('@@SWMS@@', (string) $stored);
        $this->assertCount(2, $instances);
        $this->assertStringNotContainsString('photo=', $instances[0]);
        $this->assertStringContainsString('photo=', $instances[1]);
        $this->assertStringContainsString('swms-row1-only.jpg', $instances[1]);
    }

    #[Test]
    public function it_stores_photos_for_three_swms_rows(): void
    {
        Storage::fake('public');

        $client = Client::factory()->create(['url' => 'swms-three-client']);
        $profile = Profile::factory()->create(['client_id' => $client->id]);

        $question = FormBuilderQuestion::query()->create([
            'question_id' => 503,
            'profile_id' => $profile->id,
            'form_id' => 1,
            'question_type_id' => 22,
            'question_text' => 'SWMS hazard',
            'is_mandatory' => false,
        ]);

        $photos = [
            UploadedFile::fake()->image('swms-a.jpg'),
            UploadedFile::fake()->image('swms-b.jpg'),
            UploadedFile::fake()->image('swms-c.jpg'),
        ];

        $this->post("/{$client->url}/{$profile->id}/form", [
            'answers_meta' => [
                $question->question_id => [
                    'task' => ['A', 'B', 'C'],
                    'potential_hazards' => ['A', 'B', 'C'],
                    'risk_score_before' => ['1', '2', '3'],
                    'control_measures' => ['A', 'B', 'C'],
                    'risk_score_after' => ['1', '2', '3'],
                ],
            ],
            'answers_file' => [
                $question->question_id => [
                    0 => [$photos[0]],
                    1 => [$photos[1]],
                    2 => [$photos[2]],
                ],
            ],
        ])->assertRedirect();

        $stored = FormBuilderAnswer::query()
            ->where('question_id', $question->question_id)
            ->value('question_answer');

        $instances = explode('@@SWMS@@', (string) $stored);
        $this->assertCount(3, $instances);
        $this->assertStringContainsString('photo=', $instances[0]);
        $this->assertStringContainsString('photo=', $instances[1]);
        $this->assertStringContainsString('photo=', $instances[2]);
    }
}
