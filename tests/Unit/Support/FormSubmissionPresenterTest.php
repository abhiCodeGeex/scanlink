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
}
