<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionType;
use App\Models\Profile;
use App\Services\FormBuilderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Legacy Kohana form builder UI (pin-perfect), backed by Laravel services.
 */
class LegacyFormBuilderController extends Controller
{
    public function __construct(protected FormBuilderService $formBuilder) {}

    public function index(Request $request): View|Response
    {
        $profile = $this->authorizeProfile((int) $request->query('profile_id', $request->query('profile', 0)));
        $enableFormAnalytics = (int) $request->query('enable_form_analytics', (int) $profile->enable_form_analytics);

        $questions = $this->formBuilder->questionsForProfile($profile->id);
        $recipients = $this->formBuilder->recipientsForProfile($profile)
            ->map(fn ($r) => ['recipient_email' => (string) $r->recipient_email])
            ->values()
            ->all();

        $palette = $this->legacyPaletteGroups();

        $formQuestionsArr = $questions->map(function (FormBuilderQuestion $q): array {
            $options = $q->options ?? collect();
            $questionText = (string) $q->question_text;

            if (in_array((int) $q->question_type_id, [3, 4, 5], true) && ! str_contains($questionText, ':::')) {
                $joined = $options
                    ->where('question_option_type_id', 0)
                    ->pluck('option_name')
                    ->filter()
                    ->implode(':::');

                if ($joined !== '') {
                    $questionText = $joined;
                }
            }

            return [
                'question_id' => $q->question_id,
                'question_type_id' => $q->question_type_id,
                'question_text' => $questionText,
                'image_title' => $q->image_title,
                'image_align' => $q->image_align,
                'button_link_url' => $q->button_link_url,
                'button_colour' => $q->button_colour,
                'doc_title' => $q->doc_title,
                'include_name' => $q->include_name,
                'include_employer' => $q->include_employer,
                'include_email' => $q->include_email,
                'include_phone' => $q->include_phone,
                'participant_include_signature' => $q->participant_include_signature,
                'participant_include_employer' => $q->participant_include_employer,
                'is_mandatory' => $q->is_mandatory,
                'is_logchecked' => $q->is_logchecked,
                'log_columntitle' => $q->log_columntitle,
                'covid_bg_color' => $q->covid_bg_color,
                'covid_text_color' => $q->covid_text_color,
                'question_order' => $q->question_order,
                'options' => $options->map(fn ($o) => [
                    'option_name' => $o->option_name,
                    'question_option_type_id' => $o->question_option_type_id,
                ])->all(),
            ];
        })->all();

        return response()
            ->view('legacy.formbuilder.shell', [
                'profile_id' => $profile->id,
                'form_name' => (string) ($profile->form_title ?: ''),
                'email_subject' => (string) ($profile->form_email_tag ?: ''),
                'enable_form_analytics' => $enableFormAnalytics,
                'recipient_email_arr' => $recipients !== [] ? $recipients : [['recipient_email' => '']],
                'form_questions_arr' => $formQuestionsArr,
                'question_type_0_arr' => $palette['question'],
                'question_type_1_arr' => $palette['format'],
                'question_type_2_arr' => $palette['answer'],
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function renderElement(Request $request): Response
    {
        $profile = $this->authorizeProfile((int) $request->input('profile_id'));
        $typeId = (int) $request->input('question_type_id');
        $questionId = (int) $request->input('question_id', 0);
        $userInput = (string) $request->input('user_input', '');
        $enableForm = filter_var($request->input('enable_form'), FILTER_VALIDATE_BOOLEAN)
            || $request->input('enable_form') === '1'
            || $request->input('enable_form') === 1
            || $request->input('enable_form') === 'true';

        // Prefer freshly uploaded temp files from session (legacy behaviour).
        if ($typeId === 11 && session('form_builder_image_temp')) {
            $userInput = (string) session('form_builder_image_temp');
            session()->forget('form_builder_image_temp');
        }
        if ($typeId === 21 && session('form_builder_doc_temp')) {
            $userInput = (string) session('form_builder_doc_temp');
            session()->forget('form_builder_doc_temp');
        }
        if ($typeId === 23 && session('form_builder_doc_multi_temp')) {
            $multi = session('form_builder_doc_multi_temp');
            $userInput = is_array($multi) ? implode(',', $multi) : (string) $multi;
            session()->forget('form_builder_doc_multi_temp');
        }

        // Keep existing media filenames when editing without a new upload.
        if ($userInput === '' && $questionId > 0 && in_array($typeId, [11, 21, 23], true)) {
            $existing = FormBuilderQuestion::query()
                ->where('profile_id', $profile->id)
                ->where('question_id', $questionId)
                ->value('question_text');
            $userInput = (string) ($existing ?? '');
        }

        $formName = trim((string) $request->input('form_name', ''));
        $emails = array_filter(array_map('trim', explode(',', (string) $request->input('email_arr', ''))));

        $options = $this->buildOptionsFromRequest($request, $typeId, $userInput);

        if (in_array($typeId, [3, 4, 5], true) && str_contains($userInput, ':::')) {
            // choice options already built
        } elseif (in_array($typeId, [3, 4, 5], true) && $userInput !== '') {
            $options = [];
            foreach (explode(':::', $userInput) as $opt) {
                $opt = trim($opt);
                if ($opt !== '') {
                    $options[] = ['option_name' => $opt, 'question_option_type_id' => 0];
                }
            }
        }

        $data = [
            'question_type_id' => $typeId,
            'question_text' => $userInput,
            'image_title' => (string) $request->input('image_title', ''),
            'image_align' => (string) $request->input('image_align', 'left'),
            'button_link_url' => (string) $request->input('button_link_url', ''),
            'button_colour' => ltrim((string) $request->input('button_colour', '007A01'), '#'),
            'doc_title' => (string) $request->input('doc_title', ''),
            'include_name' => filter_var($request->input('include_name'), FILTER_VALIDATE_BOOLEAN) || $request->input('include_name') === '1',
            'include_employer' => filter_var($request->input('include_employer'), FILTER_VALIDATE_BOOLEAN) || $request->input('include_employer') === '1',
            'include_email' => filter_var($request->input('include_email'), FILTER_VALIDATE_BOOLEAN) || $request->input('include_email') === '1',
            'include_phone' => filter_var($request->input('include_phone'), FILTER_VALIDATE_BOOLEAN) || $request->input('include_phone') === '1',
            'participant_include_signature' => filter_var($request->input('participant_include_signature'), FILTER_VALIDATE_BOOLEAN) || $request->input('participant_include_signature') === '1',
            'participant_include_employer' => filter_var($request->input('participant_include_employer'), FILTER_VALIDATE_BOOLEAN) || $request->input('participant_include_employer') === '1',
            'is_mandatory' => $request->input('mandatory_field') === 'true' || $request->input('mandatory_field') === '1' || $request->input('mandatory_field') === 1,
            'is_logchecked' => $request->input('is_logchecked') === '1' || $request->input('is_logchecked') === 1,
            'log_columntitle' => (string) $request->input('log_columntitle', ''),
            'covid_bg_color' => ltrim((string) $request->input('covid_bg_color', ''), '#'),
            'covid_text_color' => ltrim((string) $request->input('covid_text_color', ''), '#'),
        ];

        if ($typeId === 6) {
            $from = $request->input('scale_from');
            $to = $request->input('scale_to');
            $options = [
                ['option_name' => (string) $from, 'question_option_type_id' => 1],
                ['option_name' => (string) $to, 'question_option_type_id' => 2],
            ];
        }

        if ($typeId === 7) {
            $options = [];
            foreach (array_filter(array_map('trim', explode(',', (string) $request->input('row_text', '')))) as $row) {
                $options[] = ['option_name' => $row, 'question_option_type_id' => 5];
            }
            foreach (array_filter(array_map('trim', explode(',', (string) $request->input('column_text', '')))) as $col) {
                $options[] = ['option_name' => $col, 'question_option_type_id' => 6];
            }
        }

        $question = $this->formBuilder->saveQuestion(
            $profile,
            $data,
            $options,
            $questionId > 0 ? $questionId : null,
        );

        $this->formBuilder->updateFormSettings($profile, [
            'form_title' => $formName !== '' ? $formName : ($profile->form_title ?: 'Form'),
            'form_is_enable' => $enableForm,
            'form_active' => $enableForm,
            'recipients' => $emails,
        ]);

        $scaleFrom = (int) $request->input('scale_from', 0);
        $scaleTo = (int) $request->input('scale_to', 0);
        $rowText = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('row_text', '')))));
        $columnText = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('column_text', '')))));

        $html = view('legacy.formbuilder.control-wrap', [
            'question_id' => $question->question_id,
            'question_type_id' => (string) $typeId,
            'user_input' => $userInput,
            'scale_from' => $scaleFrom,
            'scale_to' => $scaleTo,
            'row_text' => $rowText,
            'column_text' => $columnText,
            'image_title' => $data['image_title'],
            'image_align' => $data['image_align'],
            'button_link_url' => $data['button_link_url'],
            'button_colour' => $data['button_colour'],
            'doc_title' => $data['doc_title'],
            'form_builder_image_temp' => $userInput,
            'form_builder_doc_multi_temp' => $userInput,
            'doc_file' => $userInput,
            'include_name' => $data['include_name'],
            'include_employer' => $data['include_employer'],
            'include_email' => $data['include_email'],
            'include_phone' => $data['include_phone'],
            'participant_include_signature' => $data['participant_include_signature'],
            'participant_include_employer' => $data['participant_include_employer'],
        ])->render();

        return response($html);
    }

    public function removeElement(Request $request): Response
    {
        $profile = $this->authorizeProfile((int) $request->input('profile_id'));
        $questionId = (int) $request->input('question_id');

        if ($questionId > 0) {
            $this->formBuilder->deleteQuestion($profile, $questionId);
        }

        return response('OK');
    }

    public function reorderElement(Request $request): Response
    {
        $profile = $this->authorizeProfile((int) $request->input('profile_id'));
        $currentId = $this->extractQuestionId((string) $request->input('current_ele_id', ''));
        $nextId = $this->extractQuestionId((string) $request->input('next_ele_id', ''));

        if ($currentId <= 0) {
            return response('OK');
        }

        $ordered = $this->formBuilder->questionsForProfile($profile->id)
            ->pluck('question_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $from = array_search($currentId, $ordered, true);
        if ($from === false) {
            return response('OK');
        }

        array_splice($ordered, $from, 1);

        if ($nextId > 0) {
            $to = array_search($nextId, $ordered, true);
            if ($to === false) {
                $ordered[] = $currentId;
            } else {
                array_splice($ordered, $to, 0, [$currentId]);
            }
        } else {
            $ordered[] = $currentId;
        }

        $this->formBuilder->reorder($profile, $ordered);

        return response('OK');
    }

    public function updateEnableForm(Request $request): Response
    {
        $profile = $this->authorizeProfile((int) $request->input('profile_id'));
        $enabled = $request->input('enable_form') === '1'
            || $request->input('enable_form') === 1
            || $request->input('enable_form') === 'true'
            || $request->input('enable_form') === true;

        $profile->forceFill([
            'form_is_enable' => $enabled,
            'form_active' => $enabled,
        ])->save();

        return response('OK');
    }

    /**
     * @return array{question: list<array{question_type_id: int, question_type: string}>, format: list<array{question_type_id: int, question_type: string}>, answer: list<array{question_type_id: int, question_type: string}>}
     */
    protected function legacyPaletteGroups(): array
    {
        $map = function ($types): array {
            return $types->map(fn (FormBuilderQuestionType $t): array => [
                'question_type_id' => (int) $t->question_type_id,
                'question_type' => (string) ($t->question_type ?: $t->label ?: 'Type '.$t->question_type_id),
            ])->values()->all();
        };

        $groups = $this->formBuilder->paletteGroups();

        return [
            'question' => $map($groups['question']),
            'format' => $map($groups['format']),
            'answer' => $map($groups['answer']),
        ];
    }

    /**
     * @return list<array{option_name: string, question_option_type_id: int}>
     */
    protected function buildOptionsFromRequest(Request $request, int $typeId, string $userInput): array
    {
        if (! in_array($typeId, [3, 4, 5], true)) {
            return [];
        }

        $options = [];
        foreach (explode(':::', $userInput) as $opt) {
            $opt = trim($opt);
            if ($opt !== '') {
                $options[] = ['option_name' => $opt, 'question_option_type_id' => 0];
            }
        }

        return $options;
    }

    protected function extractQuestionId(string $raw): int
    {
        if ($raw === '') {
            return 0;
        }

        if (str_contains($raw, 'question_div_')) {
            $parts = explode('question_div_', $raw);

            return (int) ($parts[1] ?? 0);
        }

        return (int) $raw;
    }

    protected function authorizeProfile(int $profileId): Profile
    {
        abort_if($profileId <= 0, 404);

        $profile = Profile::query()->findOrFail($profileId);
        $userId = Auth::id();

        abort_if($userId === null, 403);

        $allowed = ClientUser::query()
            ->where('auth_user_id', $userId)
            ->where('client_id', $profile->client_id)
            ->active()
            ->exists();

        abort_unless($allowed, 403);

        return $profile;
    }
}
