<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
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

    public function clearDates(): void
    {
        $this->fromDate = '';
        $this->toDate = '';
        $this->page = 1;
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function deleteSession(string $sessionId): void
    {
        $client = $this->requireClient();

        FormBuilderAnswer::query()
            ->whereHas('profile', fn ($q) => $q->where('client_id', $client->id))
            ->where('profile_id', $this->selectedProfileId)
            ->where('session_id', $sessionId)
            ->delete();

        Notification::make()->title('Removed Successfully.')->success()->send();
    }

    public function exportCsv(): StreamedResponse
    {
        $profile = $this->resolveProfile();
        $logQuestions = $this->logQuestions;
        $filename = 'form-submissions-'.$profile->id.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($profile, $logQuestions): void {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            $headers = ['#', 'Date/Time', 'Session ID'];
            foreach ($logQuestions as $question) {
                if ((int) $question->question_type_id === 25) {
                    array_push($headers, 'Name', 'Phone', 'Venue Address', 'Location Description/Type', 'Vehicle Reg No');
                } else {
                    $headers[] = $question->log_columntitle ?: strip_tags((string) $question->question_text);
                }
            }
            fputcsv($out, $headers);

            $sessions = $this->filteredSessionsQuery($profile->id)->get();
            $rowNum = 0;

            foreach ($sessions as $session) {
                $rowNum++;
                $answers = FormBuilderAnswer::query()
                    ->where('profile_id', $profile->id)
                    ->where('session_id', $session->session_id)
                    ->get()
                    ->keyBy('question_id');

                $row = [
                    $rowNum,
                    $session->submitted_at,
                    $session->session_id,
                ];

                foreach ($logQuestions as $question) {
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

                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadAll(): StreamedResponse
    {
        return $this->exportCsv();
    }

    public function viewUrl(string $sessionId): string
    {
        return FormSubmissionView::getUrl().'?profile='.$this->selectedProfileId.'&session_id='.urlencode($sessionId)
            .'&from_date='.urlencode($this->fromDate)
            .'&to_date='.urlencode($this->toDate);
    }

    public function printSessionUrl(string $sessionId): string
    {
        return route('portal.form-submissions.print', [
            'sessionId' => $sessionId,
            'profile' => $this->selectedProfileId,
        ]);
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

        $profile = Profile::query()
            ->where('client_id', $member->client_id)
            ->active()
            ->findOrFail($profileId);

        $answers = FormBuilderAnswer::query()
            ->with('question')
            ->where('profile_id', $profile->id)
            ->where('session_id', $sessionId)
            ->orderBy('question_id')
            ->get();

        abort_if($answers->isEmpty(), 404);

        $html = view('filament.portal.pages.form-submission-print', [
            'profile' => $profile,
            'sessionId' => $sessionId,
            'answers' => $answers,
            'submittedAt' => $answers->min('date_time'),
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

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;
        $this->profileName = filled(trim((string) $profile->code_profile_name))
            ? (string) $profile->code_profile_name
            : $profile->displayLabel();

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

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($this->selectedProfileId);
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
