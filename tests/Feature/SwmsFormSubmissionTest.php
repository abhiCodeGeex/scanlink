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

        // Distinct dimensions => distinct byte sizes, which is how each row's photo is
        // identified below. store() randomises the filename, so the original name never
        // survives into the stored path and cannot be asserted on.
        $photo0 = UploadedFile::fake()->image('swms-row0.jpg', 24, 24);
        $photo1 = UploadedFile::fake()->image('swms-row1.jpg', 96, 96);
        $size0 = $photo0->getSize();
        $size1 = $photo1->getSize();
        $this->assertNotSame($size0, $size1, 'The two fixtures must be distinguishable by size.');

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

        // Each row must carry ITS OWN photo. The row index once collapsed to 0, so both
        // photos landed on hazard #1 and hazard #2 showed none.
        $this->assertSame($size0, $this->storedPhotoSize($instances[0]));
        $this->assertSame($size1, $this->storedPhotoSize($instances[1]));
    }

    /**
     * Byte size of the single photo stored against one SWMS instance, used to prove WHICH
     * uploaded file landed on that hazard row.
     */
    protected function storedPhotoSize(string $instance): int
    {
        $this->assertMatchesRegularExpression('/photo=[^@]+/', $instance);
        preg_match('/photo=([^@]+)/', $instance, $m);

        $paths = array_values(array_filter(array_map('trim', explode(',', $m[1]))));
        $this->assertCount(1, $paths, 'Expected exactly one photo on this hazard row.');
        $this->assertTrue(Storage::disk('public')->exists($paths[0]), "Missing stored file {$paths[0]}");

        return Storage::disk('public')->size($paths[0]);
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

        $photo1 = UploadedFile::fake()->image('swms-row1-only.jpg', 48, 48);
        $size1 = $photo1->getSize();

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
        // The lone photo belongs to hazard #2, not hazard #1.
        $this->assertSame($size1, $this->storedPhotoSize($instances[1]));
    }

    /**
     * The Add-another JS re-indexes each hazard row's photo input on every submit. It once
     * rebuilt the name as `match[1] + '[' + idx + ']' + match[2]` where match[2] captured
     * "][]" — emitting `answers_file[7][0]][]`. PHP's multipart parser does not merely
     * truncate that name, it DISCARDS the file: $_FILES comes back empty, Laravel sees no
     * upload, and the photo vanishes with no error to report to the visitor.
     */
    #[Test]
    public function the_swms_photo_field_name_stays_well_formed_when_rows_are_reindexed(): void
    {
        $blade = file_get_contents(resource_path('views/scan/show.blade.php'));

        $this->assertIsString($blade);
        $this->assertStringContainsString(
            "fi.name = match[1] + '[' + idx + '][]';",
            $blade,
            'The row re-indexer must rebuild the whole "[idx][]" suffix. Appending a captured '
                .'"][]" after its own "]" produces answers_file[q][0]][], which PHP drops.',
        );

        // The name that re-indexer produces must parse as a LIST of files per row — the shape
        // swmsUploadedFilesByRow() reads. The malformed variant does not.
        parse_str('answers_file[215686][1][]=photo.jpg', $wellFormed);
        $this->assertSame(
            ['answers_file' => [215686 => [1 => ['photo.jpg']]]],
            $wellFormed,
        );

        parse_str('answers_file[215686][1]][]=photo.jpg', $malformed);
        $this->assertNotSame(
            $wellFormed,
            $malformed,
            'Sanity check: the stray bracket really does change how the field is parsed.',
        );
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
            UploadedFile::fake()->image('swms-a.jpg', 24, 24),
            UploadedFile::fake()->image('swms-b.jpg', 64, 64),
            UploadedFile::fake()->image('swms-c.jpg', 128, 128),
        ];
        $sizes = array_map(fn (UploadedFile $p): int => $p->getSize(), $photos);
        $this->assertSame($sizes, array_unique($sizes), 'Fixtures must be distinguishable by size.');

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
        // Every hazard row keeps its own photo — none of them collapse onto row 0.
        $this->assertSame($sizes[0], $this->storedPhotoSize($instances[0]));
        $this->assertSame($sizes[1], $this->storedPhotoSize($instances[1]));
        $this->assertSame($sizes[2], $this->storedPhotoSize($instances[2]));
    }
}
