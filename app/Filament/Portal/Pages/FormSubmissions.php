<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use App\Support\FormAnswerHtml;
use App\Support\LegacyEquipmentTypeLabels;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormSubmissions extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $navigationLabel = 'Form Submissions';

    protected static ?string $title = 'Form Submission Log';

    protected static ?string $slug = 'form-submissions';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.portal.pages.form-submissions';

    public ?int $selectedProfileId = null;

    public string $fromDate = '';

    public string $toDate = '';

    public int $page = 1;

    public int $perPage = 50;

    /** @var Collection<int, FormBuilderQuestion> */
    public Collection $logQuestions;

    public ?string $profileName = null;

    /** Legacy form_submissions.php blocks the log for expired codes with a message. */
    public bool $profileExpired = false;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Form Submission Log';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => LegacyEquipmentTypeLabels::navTypes(),
            'activeTab' => null,
            'addCodeUrl' => ProfileResource::getUrl('index'),
            'canAddCode' => false,
            'hideActionBar' => true,
            'hideLegend' => true,
            'readonlyNav' => true,
        ]);
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessFormSubmissions(static::portalMembership());
    }

    public function mount(): void
    {
        $this->logQuestions = collect();

        $this->fromDate = (string) request()->query('from_date', '');
        $this->toDate = (string) request()->query('to_date', '');

        $requestedProfile = request()->integer('profile');

        if ($requestedProfile > 0) {
            $this->loadProfile($requestedProfile);
        }
    }

    public function search(): void
    {
        $this->page = 1;
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function deleteSession(string $sessionId): void
    {
        $client = $this->requireClient();

        $scoped = fn ($q) => $q
            ->whereHas('profile', fn ($p) => $p->where('client_id', $client->id))
            ->where('profile_id', $this->selectedProfileId)
            ->where('session_id', $sessionId);

        // Legacy delete_answer_by_session_id unlinks uploaded files before deleting rows.
        $paths = [];
        foreach ($scoped(FormBuilderAnswer::query())->get() as $answer) {
            $paths = array_merge($paths, FormAnswerHtml::extractFilePaths((string) $answer->question_answer));
        }

        if ($paths !== []) {
            Storage::disk('public')->delete(array_values(array_unique($paths)));
        }

        $scoped(FormBuilderAnswer::query())->delete();

        Notification::make()->title('Removed Successfully.')->success()->send();
    }

    /**
     * Build the flat submission table (headers + rows) shared by CSV / XLSX exports.
     *
     * @return array{0: list<string>, 1: list<list<string|int>>}
     */
    protected function buildSubmissionTable(Profile $profile): array
    {
        $headers = ['#', 'Date/Time', 'Session ID'];
        foreach ($this->logQuestions as $question) {
            if ((int) $question->question_type_id === 25) {
                array_push($headers, 'Name', 'Phone', 'Venue Address', 'Location Description/Type', 'Vehicle Reg No');
            } else {
                $headers[] = $question->log_columntitle ?: strip_tags((string) $question->question_text);
            }
        }

        $rows = [];
        $rowNum = 0;

        foreach ($this->filteredSessionsQuery($profile->id)->get() as $session) {
            $rowNum++;
            $answers = FormBuilderAnswer::query()
                ->where('profile_id', $profile->id)
                ->where('session_id', $session->session_id)
                ->get()
                ->keyBy('question_id');

            $row = [$rowNum, (string) $session->submitted_at, (string) $session->session_id];

            foreach ($this->logQuestions as $question) {
                $raw = (string) ($answers->get($question->question_id)?->question_answer ?? '');
                if ((int) $question->question_type_id === 25) {
                    $parts = explode(':::', $raw);
                    array_push(
                        $row,
                        $parts[0] ?? '',
                        $parts[1] ?? '',
                        $parts[5] ?? '',
                        $parts[6] ?? '',
                        (($parts[6] ?? '') === 'Vehicle') ? ($parts[7] ?? '') : '',
                    );
                } else {
                    $row[] = $raw;
                }
            }

            $rows[] = $row;
        }

        return [$headers, $rows];
    }

    /**
     * Legacy "Export" — a real .xlsx of the submission log.
     */
    public function exportXlsx(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $profile = $this->resolveProfile();
        [$headers, $rows] = $this->buildSubmissionTable($profile);

        $tmp = tempnam(sys_get_temp_dir(), 'fslog_').'.xlsx';
        $writer = new \OpenSpout\Writer\XLSX\Writer;
        $writer->openToFile($tmp);
        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues($headers));
        foreach ($rows as $row) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues(array_map('strval', $row)));
        }
        $writer->close();

        return response()
            ->download($tmp, 'form-submission-log-'.$profile->id.'.xlsx')
            ->deleteFileAfterSend();
    }

    /**
     * Legacy per-row "Download" — a single submission as a PDF.
     */
    public function downloadSessionPdf(string $sessionId): StreamedResponse
    {
        $profile = $this->resolveProfile();

        return $this->renderSessionsPdf(
            $profile,
            [$sessionId],
            'form-submission-'.$profile->id.'-'.now()->format('YmdHis').'.pdf',
        );
    }

    /**
     * Legacy "Download All" — every (filtered) submission as one PDF.
     */
    public function downloadAll(): StreamedResponse
    {
        $profile = $this->resolveProfile();
        $sessionIds = $this->filteredSessionsQuery($profile->id)->get()
            ->pluck('session_id')
            ->map(fn ($id): string => (string) $id)
            ->all();

        return $this->renderSessionsPdf(
            $profile,
            $sessionIds,
            'form-submissions-'.$profile->id.'-'.now()->format('Ymd').'.pdf',
        );
    }

    /**
     * @param  list<string>  $sessionIds
     */
    protected function renderSessionsPdf(Profile $profile, array $sessionIds, string $filename): StreamedResponse
    {
        $pdf = new \TCPDF;
        $pdf->SetCreator('ScanLink');
        $pdf->SetAuthor('ScanLink');
        $pdf->SetTitle('Form Submissions — '.$profile->id);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(14, 12, 14);
        $pdf->SetAutoPageBreak(true, 12);
        // TCPDF ignores rem-based sizes and defaults tiny — set a readable base font.
        $pdf->SetFont('helvetica', '', 10);

        // Company logo as an inline base64 data URI (TCPDF renders those reliably, unlike the
        // http URL the browser view uses or some local file paths); falls back to ScanLink logo.
        $logoUrl = $this->profilePdfLogoSrc($profile);

        // Collect every submission up-front so the whole report renders as ONE flowing
        // document (submissions divided by <hr>), not one page per submission.
        // Rows come from the shared presenter (form order, media, document links).
        $questions = FormBuilderQuestion::query()
            ->with('options')
            ->where('profile_id', $profile->id)
            ->get()
            ->keyBy('question_id');

        $sessions = [];
        foreach ($sessionIds as $sessionId) {
            $answers = FormBuilderAnswer::query()
                ->where('profile_id', $profile->id)
                ->where('session_id', $sessionId)
                ->get();

            if ($answers->isEmpty()) {
                continue;
            }

            $answerMap = [];
            foreach ($answers as $answer) {
                $answerMap[(int) $answer->question_id] = (string) ($answer->question_answer ?? '');
            }

            $rows = [];
            foreach (\App\Support\FormSubmissionPresenter::rows($questions, $answerMap, includeDisplayText: true) as $presented) {
                $html = \App\Support\FormSubmissionPresenter::answerPdfHtml($presented);
                if (trim(strip_tags($html)) === '' && ! str_contains($html, '<img')) {
                    $html = '&nbsp;';
                }

                $rows[] = ['label' => $presented['label'], 'kind' => $presented['kind'], 'html' => $html];
            }

            if ($rows === []) {
                continue;
            }

            $submittedRaw = $answers->min('date_time');
            $sessions[] = [
                'sessionId' => (string) $sessionId,
                'submittedAt' => $submittedRaw ? Carbon::parse($submittedRaw)->format('d M Y H:i') : '—',
                'rows' => $rows,
            ];
        }

        $pdf->AddPage();

        if ($sessions === []) {
            $pdf->writeHTML('<p>No submissions found.</p>');
        } else {
            $html = view('filament.portal.pages.form-submissions-pdf', [
                'profile' => $profile,
                'profileName' => trim((string) ($profile->code_profile_name ?: ($profile->name ?: $profile->form_title))),
                'logoUrl' => $logoUrl,
                'sessions' => $sessions,
                'generatedAt' => now()->format('d M Y H:i'),
            ])->render();

            $pdf->writeHTML($html, true, false, true, false, '');
        }

        $content = (string) $pdf->Output('', 'S');

        return response()->streamDownload(
            function () use ($content): void {
                echo $content;
            },
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function viewUrl(string $sessionId): string
    {
        return FormSubmissionView::getUrl().'?profile='.$this->selectedProfileId.'&session_id='.urlencode($sessionId)
            .'&from_date='.urlencode($this->fromDate)
            .'&to_date='.urlencode($this->toDate);
    }

    public function returnToListUrl(): string
    {
        return ProfileResource::getUrl('index', panel: 'portal');
    }

    public static function downloadSessionHtml(int $profileId, string $sessionId): Response
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $member = $user->clientMemberships()
            ->active()
            ->orderByDesc('role')
            ->first();

        abort_unless($member, 403);

        $allowed = $member->allowedProfileIds();

        $profile = Profile::query()
            ->where('client_id', $member->client_id)
            ->when($allowed !== null, fn ($q) => $q->whereIn('id', $allowed))
            ->active()
            ->findOrFail($profileId);

        $answers = FormBuilderAnswer::query()
            ->where('profile_id', $profile->id)
            ->where('session_id', $sessionId)
            ->get();

        abort_if($answers->isEmpty(), 404);

        $questions = FormBuilderQuestion::query()
            ->with('options')
            ->where('profile_id', $profile->id)
            ->get()
            ->keyBy('question_id');

        $answerMap = [];
        foreach ($answers as $answer) {
            $answerMap[(int) $answer->question_id] = (string) ($answer->question_answer ?? '');
        }

        // Shared presenter: form order, inline media, document links.
        $rows = [];
        foreach (\App\Support\FormSubmissionPresenter::rows($questions, $answerMap, includeDisplayText: true) as $presented) {
            $rows[] = [
                'label' => $presented['label'],
                'kind' => $presented['kind'],
                'html' => \App\Support\FormSubmissionPresenter::answerHtml($presented)->toHtml(),
            ];
        }

        // Company logo for the printed report header (falls back to the ScanLink logo).
        $logo = \App\Models\Logo::query()->where('profile_id', $profile->id)->orderBy('id')->first();
        $logoUrl = $logo?->logo_name
            ? \App\Support\PublicMediaPath::url($logo->logo_name)
            : asset('images/logo.png');

        $html = view('filament.portal.pages.form-submission-print', [
            'profile' => $profile,
            'sessionId' => $sessionId,
            'rows' => $rows,
            'submittedAt' => $answers->min('date_time'),
            'logoUrl' => $logoUrl,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    /**
     * @return LengthAwarePaginator<object>|null
     */
    public function paginatedSessions(): ?LengthAwarePaginator
    {
        if (! $this->selectedProfileId) {
            return null;
        }

        return $this->filteredSessionsQuery($this->selectedProfileId)
            ->paginate($this->perPage, ['*'], 'page', $this->page);
    }

    public function answerForSession(object $session, int $questionId): string
    {
        if (! $this->selectedProfileId) {
            return '';
        }

        static $cache = [];

        $key = $session->session_id;

        if (! isset($cache[$key])) {
            $cache[$key] = FormBuilderAnswer::query()
                ->where('profile_id', $this->selectedProfileId)
                ->where('session_id', $session->session_id)
                ->pluck('question_answer', 'question_id');
        }

        return (string) ($cache[$key][$questionId] ?? '');
    }

    /**
     * @return list<string>
     */
    public function covidCells(string $raw): array
    {
        $parts = explode(':::', $raw);

        return [
            $parts[0] ?? '',
            $parts[1] ?? '',
            $parts[5] ?? '',
            $parts[6] ?? '',
            (($parts[6] ?? '') === 'Vehicle') ? ($parts[7] ?? '') : '-',
        ];
    }

    protected function loadProfile(int $profileId): void
    {
        $client = $this->requireClient();
        $allowed = $this->requireClientUser()->allowedProfileIds();

        $profile = Profile::query()
            ->where('client_id', $client->id)
            // Sub-users only reach profiles selected for them in Manage User.
            ->when($allowed !== null, fn ($q) => $q->whereIn('id', $allowed))
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;
        $this->profileExpired = $profile->isExpired();
        $this->profileName = filled(trim((string) $profile->code_profile_name))
            ? (string) $profile->code_profile_name
            : $profile->displayLabel();

        if ($this->profileExpired) {
            $this->logQuestions = collect();

            return;
        }

        $q = FormBuilderQuestion::query()
            ->where('profile_id', $profileId)
            ->where('is_logchecked', true)
            ->orderBy('question_order');

        if (\Illuminate\Support\Facades\Schema::hasColumn('form_builder_question', 'is_deleted')) {
            $q->where(function ($query): void {
                $query->where('is_deleted', false)
                    ->orWhereNull('is_deleted')
                    ->orWhere('is_deleted', 0);
            });
        }

        $this->logQuestions = $q->get();
    }

    protected function resolveProfile(): Profile
    {
        $client = $this->requireClient();
        $allowed = $this->requireClientUser()->allowedProfileIds();

        return Profile::query()
            ->where('client_id', $client->id)
            ->when($allowed !== null, fn ($q) => $q->whereIn('id', $allowed))
            ->active()
            ->findOrFail($this->selectedProfileId);
    }

    /**
     * Company logo for the PDF report as a base64 data URI. TCPDF is unreliable at fetching the
     * http URL the browser view uses AND at loading some local file paths, but it renders inline
     * data URIs dependably (same approach used for signatures). The stored logo_name is
     * normalized the same way the scan page resolves it, with legacy public/ locations and the
     * bundled ScanLink logo as fallbacks. Returns null when no usable image exists.
     */
    public static function profilePdfLogoSrc(Profile $profile): ?string
    {
        $logo = \App\Models\Logo::query()->where('profile_id', $profile->id)->orderBy('id')->first();
        $name = trim((string) ($logo?->logo_name ?? ''));
        $normalized = \App\Support\PublicMediaPath::normalize($name);

        $candidates = [];
        if ($normalized !== '') {
            try {
                if (Storage::disk('public')->exists($normalized)) {
                    $candidates[] = Storage::disk('public')->path($normalized);
                }
            } catch (\Throwable) {
                // fall through to path guesses
            }

            $candidates[] = public_path('storage/'.$normalized);
            $candidates[] = storage_path('app/public/'.$normalized);
            $candidates[] = public_path($normalized);
        }
        if ($name !== '') {
            $candidates[] = public_path($name);
        }

        // Bundled ScanLink logo fallbacks.
        foreach (['images/logo.png', 'images/scanlink-logo.png', 'images/email-logo.png'] as $fallback) {
            $candidates[] = public_path($fallback);
        }

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && ($info = @getimagesize($candidate)) !== false) {
                $data = @file_get_contents($candidate);
                if ($data !== false && $data !== '') {
                    $mime = $info['mime'] ?? 'image/png';

                    return 'data:'.$mime.';base64,'.base64_encode($data);
                }
            }
        }

        return null;
    }

    protected function filteredSessionsQuery(int $profileId)
    {
        $query = FormBuilderAnswer::query()
            ->where('profile_id', $profileId)
            ->whereNotNull('session_id')
            ->where('session_id', '!=', '');

        if (filled($this->fromDate)) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', trim($this->fromDate))->startOfDay();
                $query->where('date_time', '>=', $from->toDateTimeString());
            } catch (\Throwable) {
                // ignore invalid date
            }
        }

        if (filled($this->toDate)) {
            try {
                $to = Carbon::createFromFormat('d/m/Y', trim($this->toDate))->endOfDay();
                $query->where('date_time', '<=', $to->toDateTimeString());
            } catch (\Throwable) {
                // ignore invalid date
            }
        }

        return $query
            ->selectRaw('session_id, MIN(date_time) as submitted_at, COUNT(*) as answer_count, MAX(app_user_firstname) as app_user_firstname, MAX(app_user_lastname) as app_user_lastname')
            ->groupBy('session_id')
            ->orderByDesc('submitted_at');
    }
}
