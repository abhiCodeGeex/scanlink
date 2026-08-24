<?php

namespace Tests\Unit\Support;

use App\Models\FormBuilderQuestion;
use App\Support\FormSubmissionPresenter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormSubmissionPresenterTest extends TestCase
{
    #[Test]
    public function it_uses_type_fallback_instead_of_raw_choice_options_as_label(): void
    {
        $question = new FormBuilderQuestion([
            'question_id' => 1,
            'question_type_id' => 3,
            'question_text' => 'Option A:::Option B:::Option C',
        ]);
        $question->setRelation('options', collect());

        $this->assertSame('Multiple choice', FormSubmissionPresenter::label($question));
    }

    #[Test]
    public function it_prefers_log_column_title_and_plain_question_text(): void
    {
        $withLog = new FormBuilderQuestion([
            'question_type_id' => 1,
            'question_text' => 'Ignored',
            'log_columntitle' => 'Visitor company',
        ]);
        $this->assertSame('Visitor company', FormSubmissionPresenter::label($withLog));

        $plain = new FormBuilderQuestion([
            'question_type_id' => 1,
            'question_text' => 'Full name',
        ]);
        $this->assertSame('Full name', FormSubmissionPresenter::label($plain));
    }

    #[Test]
    public function it_splits_triple_colon_answers_into_a_list(): void
    {
        $question = new FormBuilderQuestion([
            'question_type_id' => 4,
            'question_text' => 'Pick some',
        ]);
        $question->setRelation('options', collect());

        $row = FormSubmissionPresenter::presentOne(
            $question,
            'Alpha:::Beta:::Gamma',
        );

        $this->assertSame('list', $row['kind']);
        $this->assertSame(['Alpha', 'Beta', 'Gamma'], $row['items']);
        $this->assertSame('Pick some', $row['label']);
    }

    #[Test]
    public function it_formats_covid_check_in_as_named_fields(): void
    {
        $row = FormSubmissionPresenter::presentOne(
            new FormBuilderQuestion(['question_type_id' => 25, 'question_text' => '']),
            'Griffin Berg:::+1 (622) 478-7942:::1983-11-18:::21:55:::Venue:::Address:::Other:::test',
        );

        $this->assertSame('fields', $row['kind']);
        $this->assertSame('Check-in', $row['label']);
        $this->assertSame('Visitor name', $row['fields'][0]['label']);
        $this->assertSame('Griffin Berg', $row['fields'][0]['value']);
        $this->assertSame('Vehicle / other', $row['fields'][7]['label']);
        $this->assertSame('test', $row['fields'][7]['value']);
    }

    #[Test]
    public function it_divides_multiple_swms_hazard_rows_into_instances(): void
    {
        $question = new FormBuilderQuestion([
            'question_id' => 7,
            'question_type_id' => 22,
            'question_text' => 'SWMS Test',
        ]);

        $raw = 'task=Test Activity@@F@@hazards=Test Hazards@@F@@risk_before=2@@F@@control=Test Measures@@F@@risk_after=3'
            .'@@SWMS@@'
            .'task=Test 2 Activity@@F@@hazards=Test 2 Hazards@@F@@risk_before=4@@F@@control=Test 2 Measures@@F@@risk_after=5';

        $row = FormSubmissionPresenter::presentOne($question, $raw);

        $this->assertSame('swms', $row['kind']);
        $this->assertCount(2, $row['instances']);
        $this->assertSame('Task / Activity', $row['instances'][0][0]['label']);
        $this->assertSame('Test Activity', $row['instances'][0][0]['value']);
        $this->assertSame('Test 2 Activity', $row['instances'][1][0]['value']);

        $html = FormSubmissionPresenter::answerHtml($row)->toHtml();
        $this->assertStringContainsString('SWMS #1', $html);
        $this->assertStringContainsString('SWMS #2', $html);
        // A divider separates the second instance from the first.
        $this->assertStringContainsString('border-top', $html);

        $pdf = FormSubmissionPresenter::answerPdfHtml($row);
        $this->assertStringContainsString('SWMS #2', $pdf);
        $this->assertStringContainsString('<hr>', $pdf);
    }

    #[Test]
    public function it_renders_swms_photo_as_a_file_link(): void
    {
        $question = new FormBuilderQuestion([
            'question_id' => 8,
            'question_type_id' => 22,
            'question_text' => 'SWMS Test',
        ]);

        $raw = 'task=Dig trench@@F@@photo=form-uploads/6818/abc.jpg';

        $row = FormSubmissionPresenter::presentOne($question, $raw);
        $html = FormSubmissionPresenter::answerHtml($row)->toHtml();

        $this->assertStringContainsString('<a href=', $html);
        $this->assertStringContainsString('abc.jpg', $html);
    }

    #[Test]
    public function it_renders_inline_signature_as_image_not_raw_base64(): void
    {
        $question = new FormBuilderQuestion([
            'question_id' => 9,
            'question_type_id' => 18,
            'question_text' => 'Participant Name',
        ]);
        $question->setRelation('options', collect());

        // A genuinely valid PNG (built with GD) so the PDF path, which decodes + validates the
        // image, also embeds an <img>.
        $dataUri = self::validPngDataUri();
        $raw = 'Brent Palmer | Signature: '.$dataUri;

        $row = FormSubmissionPresenter::presentOne($question, $raw);
        $html = FormSubmissionPresenter::answerHtml($row)->toHtml();

        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('data:image/png;base64', $html);
        // The raw base64 must not appear as escaped plain text outside an <img src>.
        $this->assertStringNotContainsString('Signature: data:image', strip_tags($html));

        $pdf = FormSubmissionPresenter::answerPdfHtml($row);
        $this->assertStringContainsString('<img', $pdf);
    }

    #[Test]
    public function it_divides_multiple_signature_entries_into_instances(): void
    {
        $question = new FormBuilderQuestion([
            'question_id' => 12,
            'question_type_id' => 16,
            'question_text' => 'Signature Test',
        ]);
        $question->setRelation('options', collect());

        $png = self::validPngDataUri();
        $raw = 'name=Alice@@F@@signature='.$png.'@@ROW@@name=Bob@@F@@signature='.$png;

        $row = FormSubmissionPresenter::presentOne($question, $raw);

        $this->assertSame('sigrows', $row['kind']);
        $this->assertCount(2, $row['instances']);
        $this->assertSame('Name', $row['instances'][0][0]['label']);
        $this->assertSame('Alice', $row['instances'][0][0]['value']);
        $this->assertTrue($row['instances'][0][1]['is_signature']);

        $html = FormSubmissionPresenter::answerHtml($row)->toHtml();
        $this->assertStringContainsString('Signature #1', $html);
        $this->assertStringContainsString('Signature #2', $html);
        $this->assertSame(2, substr_count($html, '<img'));
        $this->assertStringContainsString('Alice', $html);
        $this->assertStringContainsString('Bob', $html);

        $pdf = FormSubmissionPresenter::answerPdfHtml($row);
        $this->assertStringContainsString('<img', $pdf);
    }

    #[Test]
    public function it_builds_rows_keyed_by_question_id(): void
    {
        $q = new FormBuilderQuestion([
            'question_id' => 42,
            'question_type_id' => 1,
            'question_text' => 'Comments',
        ]);

        $rows = FormSubmissionPresenter::rows(collect([42 => $q]), [42 => 'Hello world']);

        $this->assertCount(1, $rows);
        $this->assertSame('Comments', $rows[0]['label']);
        $this->assertSame('text', $rows[0]['kind']);
        $this->assertSame('Hello world', $rows[0]['value']);
    }

    #[Test]
    public function answer_html_escapes_text_and_renders_lists(): void
    {
        $text = FormSubmissionPresenter::answerHtml([
            'kind' => 'text',
            'value' => '<script>x</script>',
            'items' => [],
            'fields' => [],
            'signature_src' => '',
            'signature_meta' => '',
        ]);
        $this->assertStringContainsString('&lt;script&gt;', $text->toHtml());
        $this->assertStringNotContainsString('<script>', $text->toHtml());

        $list = FormSubmissionPresenter::answerHtml([
            'kind' => 'list',
            'value' => '',
            'items' => ['One', 'Two'],
            'fields' => [],
            'signature_src' => '',
            'signature_meta' => '',
        ]);
        $this->assertStringContainsString('<ul', $list->toHtml());
        $this->assertStringContainsString('One', $list->toHtml());
    }

    /**
     * A genuinely valid PNG (built with GD) as a data URI, so the PDF renderer's decode+validate
     * step accepts it and embeds an <img> — mirrors a real canvas signature.
     */
    private static function validPngDataUri(): string
    {
        $img = imagecreatetruecolor(4, 4);
        ob_start();
        imagepng($img);
        $binary = (string) ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,'.base64_encode($binary);
    }
}
